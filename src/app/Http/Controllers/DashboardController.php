<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use App\Models\Category;
use App\Models\Location;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\ScanLog;
use App\Models\Masjid;
use App\Scopes\MasjidScope;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Superadmin without masjid context → show platform overview
        if ($user->isSuperAdmin() && !session('current_masjid_id')) {
            return $this->superadminDashboard();
        }

        // Regular dashboard (scoped by MasjidScope)
        return $this->tenantDashboard();
    }

    private function superadminDashboard(): View
    {
        $stats = [
            'total_masjids' => Masjid::count(),
            'active_masjids' => Masjid::where('status', 'active')->count(),
            'total_items' => Item::withoutGlobalScope(MasjidScope::class)->count(),
            'total_quantity' => Item::withoutGlobalScope(MasjidScope::class)->sum('quantity'),
            'total_users' => User::where('is_superadmin', false)->count(),
            'total_loans' => Loan::withoutGlobalScope(MasjidScope::class)->count(),
            'active_loans' => Loan::withoutGlobalScope(MasjidScope::class)->whereNull('returned_at')->count(),
            'overdue_loans' => Loan::withoutGlobalScope(MasjidScope::class)
                ->whereNull('returned_at')
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->count(),
        ];

        $masjids = Masjid::withCount([
            'users',
            'items',
            'loans as active_loans_count' => function ($q) {
                $q->whereNull('returned_at');
            },
        ])->orderBy('name')->limit(10)->get();

        $recentMasjids = Masjid::orderBy('created_at', 'desc')->limit(5)->get();

        return view('dashboard.superadmin', compact('stats', 'masjids', 'recentMasjids'));
    }

    private function tenantDashboard(): View
    {
        // Summary stats
        $stats = [
            'total_items' => Item::count(),
            'total_quantity' => Item::sum('quantity'),
            'items_good' => Item::where('condition', 'baik')->count(),
            'items_need_repair' => Item::where('condition', 'perlu_perbaikan')->count(),
            'items_broken' => Item::where('condition', 'rusak')->count(),
            'active_loans' => Loan::whereNull('returned_at')->count(),
            'overdue_loans' => Loan::whereNull('returned_at')
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->count(),
            'total_categories' => Category::count(),
            'total_locations' => Location::count(),
            'total_users' => User::count(),
            'items_with_qr' => Item::whereNotNull('qr_code_key')->count(),
            'today_scans' => ScanLog::whereDate('scanned_at', today())->count(),
        ];

        // Recent items
        $recentItems = Item::with(['category', 'location'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Overdue loans
        $overdueLoans = Loan::with(['item'])
            ->whereNull('returned_at')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        // Recent movements
        $recentMovements = StockMovement::with(['item'])
            ->orderBy('moved_at', 'desc')
            ->limit(5)
            ->get();

        // Items by category
        $itemsByCategory = Category::withCount('items')
            ->orderBy('items_count', 'desc')
            ->limit(5)
            ->get();

        // Items by location
        $itemsByLocation = Location::withCount('items')
            ->orderBy('items_count', 'desc')
            ->limit(5)
            ->get();

        // Analytics data for charts
        $loanTrends = $this->getLoanTrends();
        $mostBorrowedItems = $this->getMostBorrowedItems();
        $conditionStats = $this->getConditionStats();

        // Recent scans
        $recentScans = ScanLog::with(['item', 'user'])
            ->orderBy('scanned_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'recentItems',
            'overdueLoans',
            'recentMovements',
            'itemsByCategory',
            'itemsByLocation',
            'loanTrends',
            'mostBorrowedItems',
            'conditionStats',
            'recentScans'
        ));
    }

    /**
     * Get loan trends for the last 12 months
     */
    private function getLoanTrends(): array
    {
        $months = [];
        $borrowed = [];
        $returned = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $months[] = $date->translatedFormat('M Y');

            // Count loans borrowed in this month
            $borrowed[] = Loan::whereYear('borrowed_at', $date->year)
                ->whereMonth('borrowed_at', $date->month)
                ->count();

            // Count loans returned in this month
            $returned[] = Loan::whereNotNull('returned_at')
                ->whereYear('returned_at', $date->year)
                ->whereMonth('returned_at', $date->month)
                ->count();
        }

        return [
            'labels' => $months,
            'borrowed' => $borrowed,
            'returned' => $returned,
        ];
    }

    /**
     * Get most borrowed items (top 10)
     */
    private function getMostBorrowedItems(): array
    {
        $items = Item::select('items.id', 'items.name')
            ->join('loans', 'items.id', '=', 'loans.item_id')
            ->selectRaw('COUNT(loans.id) as loan_count')
            ->selectRaw('SUM(loans.quantity) as total_borrowed')
            ->groupBy('items.id', 'items.name')
            ->orderByDesc('loan_count')
            ->limit(10)
            ->get();

        return [
            'labels' => $items->pluck('name')->toArray(),
            'counts' => $items->pluck('loan_count')->toArray(),
            'quantities' => $items->pluck('total_borrowed')->toArray(),
        ];
    }

    /**
     * Get condition statistics for pie chart
     */
    private function getConditionStats(): array
    {
        $conditions = Item::select('condition')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('condition')
            ->get()
            ->keyBy('condition');

        return [
            'labels' => ['Baik', 'Perlu Perbaikan', 'Rusak'],
            'data' => [
                $conditions->get('baik')?->count ?? 0,
                $conditions->get('perlu_perbaikan')?->count ?? 0,
                $conditions->get('rusak')?->count ?? 0,
            ],
            'colors' => ['#22c55e', '#eab308', '#ef4444'],
        ];
    }
}
