<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $query = StockMovement::with(['item.category', 'item.location']);

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $movements = $query->orderBy('moved_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $items = Item::orderBy('name')->get();

        return view('stock-movements.index', compact('movements', 'items'));
    }

    public function create(Request $request): View
    {
        $items = Item::with(['category', 'location'])->orderBy('name')->get();
        
        $selectedItem = null;
        if ($request->filled('item_id')) {
            $selectedItem = Item::find($request->item_id);
        }

        return view('stock-movements.create', compact('items', 'selectedItem'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'moved_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $item = Item::findOrFail($validated['item_id']);
        
        // Validate stock for outgoing
        if ($validated['type'] === 'out' && $validated['quantity'] > $item->quantity) {
            return back()->withInput()->with('error', 
                "Jumlah melebihi stok saat ini ({$item->quantity} {$item->unit}).");
        }

        DB::transaction(function () use ($validated, $item) {
            // Create movement record
            StockMovement::create($validated);

            // Update item quantity
            if ($validated['type'] === 'in') {
                $item->increment('quantity', $validated['quantity']);
            } else {
                $item->decrement('quantity', $validated['quantity']);
            }
        });

        return redirect()->route('stock-movements.index')
            ->with('success', 'Mutasi stok berhasil dicatat.');
    }

    public function itemHistory(Item $item): View
    {
        $movements = $item->stockMovements()
            ->orderBy('moved_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('stock-movements.item-history', compact('item', 'movements'));
    }
}
