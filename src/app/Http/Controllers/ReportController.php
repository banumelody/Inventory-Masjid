<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Masjid;
use App\Models\Loan;
use App\Scopes\MasjidScope;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $isGlobalView = $user->isSuperAdmin() && !session('current_masjid_id');

        if ($isGlobalView) {
            return $this->globalReport($request);
        }

        $query = Item::with(['category', 'location']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $items = $query->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('reports.index', compact('items', 'categories', 'locations'));
    }

    private function globalReport(Request $request): View
    {
        $masjids = Masjid::where('status', 'active')->orderBy('name')->get();

        $masjidStats = [];
        foreach ($masjids as $masjid) {
            $masjidStats[] = [
                'masjid' => $masjid,
                'total_items' => Item::withoutGlobalScope(MasjidScope::class)->where('masjid_id', $masjid->id)->count(),
                'total_quantity' => Item::withoutGlobalScope(MasjidScope::class)->where('masjid_id', $masjid->id)->sum('quantity'),
                'items_good' => Item::withoutGlobalScope(MasjidScope::class)->where('masjid_id', $masjid->id)->where('condition', 'baik')->count(),
                'items_need_repair' => Item::withoutGlobalScope(MasjidScope::class)->where('masjid_id', $masjid->id)->where('condition', 'perlu_perbaikan')->count(),
                'items_broken' => Item::withoutGlobalScope(MasjidScope::class)->where('masjid_id', $masjid->id)->where('condition', 'rusak')->count(),
                'active_loans' => Loan::withoutGlobalScope(MasjidScope::class)->where('masjid_id', $masjid->id)->whereNull('returned_at')->count(),
                'overdue_loans' => Loan::withoutGlobalScope(MasjidScope::class)->where('masjid_id', $masjid->id)->whereNull('returned_at')->whereNotNull('due_at')->where('due_at', '<', now())->count(),
            ];
        }

        $totals = [
            'total_items' => Item::withoutGlobalScope(MasjidScope::class)->count(),
            'total_quantity' => Item::withoutGlobalScope(MasjidScope::class)->sum('quantity'),
            'active_loans' => Loan::withoutGlobalScope(MasjidScope::class)->whereNull('returned_at')->count(),
            'overdue_loans' => Loan::withoutGlobalScope(MasjidScope::class)->whereNull('returned_at')->whereNotNull('due_at')->where('due_at', '<', now())->count(),
        ];

        return view('reports.global', compact('masjidStats', 'totals', 'masjids'));
    }

    public function print(Request $request): View
    {
        $query = Item::with(['category', 'location']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $items = $query->orderBy('name')->get();
        
        $categoryName = 'Semua Kategori';
        $locationName = 'Semua Lokasi';
        
        if ($request->filled('category_id')) {
            $category = Category::find($request->category_id);
            $categoryName = $category ? $category->name : 'Semua Kategori';
        }
        
        if ($request->filled('location_id')) {
            $location = Location::find($request->location_id);
            $locationName = $location ? $location->name : 'Semua Lokasi';
        }

        return view('reports.print', compact('items', 'categoryName', 'locationName'));
    }
}
