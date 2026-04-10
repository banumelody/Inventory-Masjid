<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Loan;
use App\Models\StockMovement;
use App\Scopes\MasjidScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    // --- Write endpoints ---

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'condition' => 'required|in:baik,perlu_perbaikan,rusak',
            'description' => 'nullable|string|max:1000',
            'min_stock' => 'nullable|integer|min:0',
            'acquisition_date' => 'nullable|date',
            'acquisition_source' => 'nullable|string|max:255',
        ]);

        $validated['qr_code_key'] = bin2hex(random_bytes(12));
        $item = Item::create($validated);
        ActivityLog::log('create', $item, null, $validated);

        return response()->json($item->load(['category', 'location']), 201);
    }

    public function update(Request $request, Item $item): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'location_id' => 'sometimes|required|exists:locations,id',
            'quantity' => 'sometimes|required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'condition' => 'sometimes|required|in:baik,perlu_perbaikan,rusak',
            'description' => 'nullable|string|max:1000',
            'min_stock' => 'nullable|integer|min:0',
            'acquisition_date' => 'nullable|date',
            'acquisition_source' => 'nullable|string|max:255',
        ]);

        $old = $item->toArray();
        $item->update($validated);
        ActivityLog::log('update', $item, $old, $item->toArray());

        return response()->json($item->load(['category', 'location']));
    }

    public function destroy(Item $item): JsonResponse
    {
        ActivityLog::log('delete', $item, $item->toArray());
        $item->delete();

        return response()->json(['message' => 'Barang berhasil dihapus.']);
    }

    public function storeLoan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'borrower_name' => 'required|string|max:255',
            'borrower_phone' => 'nullable|string|max:20',
            'quantity' => 'required|integer|min:1',
            'borrowed_at' => 'required|date',
            'due_at' => 'nullable|date|after_or_equal:borrowed_at',
            'notes' => 'nullable|string|max:1000',
        ]);

        $item = Item::findOrFail($validated['item_id']);
        if ($validated['quantity'] > $item->available_quantity) {
            return response()->json(['message' => 'Jumlah melebihi stok tersedia.'], 422);
        }

        $validated['return_qr_key'] = bin2hex(random_bytes(12));
        $loan = Loan::create($validated);
        ActivityLog::log('create', $loan, null, $validated);

        return response()->json($loan->load('item'), 201);
    }

    public function returnLoan(Request $request, Loan $loan): JsonResponse
    {
        if ($loan->returned_at) {
            return response()->json(['message' => 'Peminjaman sudah dikembalikan.'], 422);
        }

        $validated = $request->validate([
            'returned_at' => 'required|date',
            'returned_condition' => 'required|in:baik,perlu_perbaikan,rusak',
            'notes' => 'nullable|string|max:1000',
        ]);

        $old = $loan->toArray();
        $loan->update($validated);
        ActivityLog::log('update', $loan, $old, $loan->toArray());

        return response()->json($loan->load('item'));
    }

    public function destroyLoan(Loan $loan): JsonResponse
    {
        ActivityLog::log('delete', $loan, $loan->toArray());
        $loan->delete();

        return response()->json(['message' => 'Peminjaman berhasil dihapus.']);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    public function updateCategory(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    public function destroyCategory(Category $category): JsonResponse
    {
        if ($category->items()->count() > 0) {
            return response()->json(['message' => 'Kategori masih memiliki barang.'], 422);
        }
        $category->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }

    public function storeLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $location = Location::create($validated);

        return response()->json($location, 201);
    }

    public function updateLocation(Request $request, Location $location): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $location->update($validated);

        return response()->json($location);
    }

    public function destroyLocation(Location $location): JsonResponse
    {
        if ($location->items()->count() > 0) {
            return response()->json(['message' => 'Lokasi masih memiliki barang.'], 422);
        }
        $location->delete();

        return response()->json(['message' => 'Lokasi berhasil dihapus.']);
    }
}
