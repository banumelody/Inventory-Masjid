<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $query = Item::with(['category', 'location']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $items = $query->orderBy('name')->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('items.index', compact('items', 'categories', 'locations'));
    }

    public function show(Item $item): View
    {
        $item->load(['category', 'location', 'activeLoans', 'stockMovements' => function($q) {
            $q->orderBy('moved_at', 'desc')->limit(10);
        }, 'activeMaintenances', 'scanLogs' => function($q) {
            $q->with('user')->orderBy('scanned_at', 'desc')->limit(10);
        }]);
        
        return view('items.show', compact('item'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        return view('items.create', compact('categories', 'locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'condition' => 'required|in:baik,perlu_perbaikan,rusak',
            'note' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $this->uploadPhoto($request->file('photo'));
        }

        unset($validated['photo']);
        $item = Item::create($validated);

        // Log activity
        ActivityLog::log('create', "Menambahkan barang: {$item->name}", $item, null, $validated);

        // Handle "Simpan & Tambah Lagi"
        if ($request->input('action') === 'save_and_new') {
            return redirect()->route('items.create')
                ->with('success', 'Barang berhasil ditambahkan. Silakan tambah lagi.');
        }

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item): View
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        return view('items.edit', compact('item', 'categories', 'locations'));
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'condition' => 'required|in:baik,perlu_perbaikan,rusak',
            'note' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'remove_photo' => 'nullable|boolean',
        ]);

        $oldValues = $item->toArray();

        // Handle photo removal
        if ($request->boolean('remove_photo') && $item->photo_path) {
            Storage::disk('public')->delete($item->photo_path);
            $validated['photo_path'] = null;
        }

        // Handle new photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($item->photo_path) {
                Storage::disk('public')->delete($item->photo_path);
            }
            $validated['photo_path'] = $this->uploadPhoto($request->file('photo'));
        }

        unset($validated['photo'], $validated['remove_photo']);
        $item->update($validated);

        // Log activity
        ActivityLog::log('update', "Mengupdate barang: {$item->name}", $item, $oldValues, $validated);

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $itemName = $item->name;
        $oldValues = $item->toArray();

        // Delete photo if exists
        if ($item->photo_path) {
            Storage::disk('public')->delete($item->photo_path);
        }

        $item->delete();

        // Log activity
        ActivityLog::log('delete', "Menghapus barang: {$itemName}", null, $oldValues, null);

        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    protected function uploadPhoto($file): string
    {
        $filename = 'items/' . uniqid() . '_' . time() . '.jpg';
        
        // Read and resize image
        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
        
        // Get original dimensions
        $origWidth = imagesx($image);
        $origHeight = imagesy($image);
        
        // Calculate new dimensions (max 1200px)
        $maxSize = 1200;
        if ($origWidth > $maxSize || $origHeight > $maxSize) {
            if ($origWidth > $origHeight) {
                $newWidth = $maxSize;
                $newHeight = (int) ($origHeight * ($maxSize / $origWidth));
            } else {
                $newHeight = $maxSize;
                $newWidth = (int) ($origWidth * ($maxSize / $origHeight));
            }
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }
        
        // Create resized image
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        
        // Save to storage
        $fullPath = storage_path('app/public/' . $filename);
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        imagejpeg($resized, $fullPath, 85);
        
        imagedestroy($image);
        imagedestroy($resized);
        
        return $filename;
    }
}
