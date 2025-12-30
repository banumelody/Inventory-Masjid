<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use App\Models\Category;
use App\Models\Location;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
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

        return view('dashboard.index', compact(
            'stats',
            'recentItems',
            'overdueLoans',
            'recentMovements',
            'itemsByCategory',
            'itemsByLocation'
        ));
    }
}
