<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Loan;
use App\Models\StockMovement;
use App\Scopes\MasjidScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Item::with(['category', 'location']);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($locationId = $request->input('location_id')) {
            $query->where('location_id', $locationId);
        }

        if ($condition = $request->input('condition')) {
            $query->where('condition', $condition);
        }

        $items = $query->orderBy('name')->paginate($request->input('per_page', 20));

        return response()->json($items);
    }

    public function show(Item $item): JsonResponse
    {
        $item->load(['category', 'location']);

        return response()->json([
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'quantity' => $item->quantity,
            'available_quantity' => $item->available_quantity,
            'borrowed_quantity' => $item->borrowed_quantity,
            'unit' => $item->unit,
            'condition' => $item->condition,
            'condition_label' => $item->condition_label,
            'category' => $item->category ? ['id' => $item->category->id, 'name' => $item->category->name] : null,
            'location' => $item->location ? ['id' => $item->location->id, 'name' => $item->location->name] : null,
            'qr_code_key' => $item->qr_code_key,
            'photo' => $item->photo_url,
            'min_stock' => $item->min_stock,
            'acquisition_date' => $item->acquisition_date,
            'acquisition_source' => $item->acquisition_source,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = Category::withCount('items')->orderBy('name')->get();
        return response()->json($categories);
    }

    public function locations(): JsonResponse
    {
        $locations = Location::withCount('items')->orderBy('name')->get();
        return response()->json($locations);
    }

    public function loans(Request $request): JsonResponse
    {
        $query = Loan::with(['item']);

        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->whereNull('returned_at');
            } elseif ($status === 'returned') {
                $query->whereNotNull('returned_at');
            } elseif ($status === 'overdue') {
                $query->whereNull('returned_at')
                    ->whereNotNull('due_at')
                    ->where('due_at', '<', now());
            }
        }

        $loans = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json($loans);
    }

    public function loanShow(Loan $loan): JsonResponse
    {
        $loan->load(['item.category', 'item.location']);

        return response()->json($loan);
    }

    public function stockMovements(Request $request): JsonResponse
    {
        $movements = StockMovement::with(['item', 'user'])
            ->orderBy('moved_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json($movements);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
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
        ]);
    }
}
