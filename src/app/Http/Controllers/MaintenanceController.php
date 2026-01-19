<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\MaintenancePhoto;
use App\Models\Item;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Maintenance::with(['item.category', 'item.location', 'user', 'photos']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by item
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhereHas('item', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $maintenances = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Stats
        $stats = [
            'pending' => Maintenance::where('status', 'pending')->count(),
            'in_progress' => Maintenance::where('status', 'in_progress')->count(),
            'completed_this_month' => Maintenance::where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->count(),
            'total_cost_this_month' => Maintenance::where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->whereYear('completed_at', now()->year)
                ->sum('cost'),
        ];

        return view('maintenances.index', compact('maintenances', 'stats'));
    }

    public function create(Request $request): View
    {
        $items = Item::with(['category', 'location'])
            ->orderBy('name')
            ->get();

        $selectedItem = null;
        if ($request->filled('item_id')) {
            $selectedItem = Item::find($request->item_id);
        }

        $photoTypes = MaintenancePhoto::getTypes();

        return view('maintenances.create', compact('items', 'selectedItem', 'photoTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:perbaikan,perawatan,penggantian_part',
            'description' => 'required|string',
            'vendor' => 'nullable|string|max:255',
            'vendor_phone' => 'nullable|string|max:50',
            'cost' => 'nullable|numeric|min:0',
            'started_at' => 'nullable|date',
            'estimated_completion' => 'nullable|date|after_or_equal:started_at',
            'notes' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB
            'photo_types' => 'nullable|array',
            'photo_types.*' => 'in:before,progress,after',
            'photo_captions' => 'nullable|array',
            'photo_captions.*' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = $request->filled('started_at') ? 'in_progress' : 'pending';

        $maintenance = Maintenance::create($validated);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            $this->handlePhotoUploads($request, $maintenance);
        }

        // Log activity
        ActivityLog::log(
            'create',
            "Menambahkan maintenance untuk barang: {$maintenance->item->name}",
            $maintenance,
            null,
            $validated
        );

        return redirect()->route('maintenances.index')
            ->with('success', 'Data maintenance berhasil ditambahkan.');
    }

    public function show(Maintenance $maintenance): View
    {
        $maintenance->load(['item.category', 'item.location', 'user', 'photos.uploader']);
        $photoTypes = MaintenancePhoto::getTypes();
        return view('maintenances.show', compact('maintenance', 'photoTypes'));
    }

    public function edit(Maintenance $maintenance): View
    {
        $items = Item::with(['category', 'location'])
            ->orderBy('name')
            ->get();

        $maintenance->load('photos');
        $photoTypes = MaintenancePhoto::getTypes();

        return view('maintenances.edit', compact('maintenance', 'items', 'photoTypes'));
    }

    public function update(Request $request, Maintenance $maintenance): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:perbaikan,perawatan,penggantian_part',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'description' => 'required|string',
            'vendor' => 'nullable|string|max:255',
            'vendor_phone' => 'nullable|string|max:50',
            'cost' => 'nullable|numeric|min:0',
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date|after_or_equal:started_at',
            'estimated_completion' => 'nullable|date',
            'notes' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'photo_types' => 'nullable|array',
            'photo_types.*' => 'in:before,progress,after',
            'photo_captions' => 'nullable|array',
            'photo_captions.*' => 'nullable|string|max:255',
        ]);

        // Auto set completed_at if status changed to completed
        if ($validated['status'] === 'completed' && !$validated['completed_at']) {
            $validated['completed_at'] = now();
        }

        $oldValues = $maintenance->toArray();
        $maintenance->update($validated);

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            $this->handlePhotoUploads($request, $maintenance);
        }

        // Log activity
        ActivityLog::log(
            'update',
            "Mengupdate maintenance untuk barang: {$maintenance->item->name}",
            $maintenance,
            $oldValues,
            $validated
        );

        return redirect()->route('maintenances.index')
            ->with('success', 'Data maintenance berhasil diupdate.');
    }

    public function destroy(Maintenance $maintenance): RedirectResponse
    {
        $itemName = $maintenance->item->name;
        
        // Delete associated photos from storage
        foreach ($maintenance->photos as $photo) {
            Storage::disk('public')->delete('maintenance-photos/' . $photo->filename);
        }
        
        // Log activity before delete
        ActivityLog::log(
            'delete',
            "Menghapus maintenance untuk barang: {$itemName}",
            $maintenance,
            $maintenance->toArray(),
            null
        );

        $maintenance->delete();

        return redirect()->route('maintenances.index')
            ->with('success', 'Data maintenance berhasil dihapus.');
    }

    /**
     * Quick update status
     */
    public function updateStatus(Request $request, Maintenance $maintenance): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $oldStatus = $maintenance->status;
        $maintenance->status = $validated['status'];

        if ($validated['status'] === 'in_progress' && !$maintenance->started_at) {
            $maintenance->started_at = now();
        }

        if ($validated['status'] === 'completed' && !$maintenance->completed_at) {
            $maintenance->completed_at = now();
        }

        $maintenance->save();

        // Log activity
        ActivityLog::log(
            'update',
            "Mengubah status maintenance dari {$oldStatus} ke {$validated['status']}",
            $maintenance
        );

        return back()->with('success', 'Status maintenance berhasil diupdate.');
    }

    /**
     * Item maintenance history
     */
    public function itemHistory(Item $item): View
    {
        $maintenances = $item->maintenances()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('maintenances.item-history', compact('item', 'maintenances'));
    }

    /**
     * Upload additional photos to existing maintenance
     */
    public function uploadPhoto(Request $request, Maintenance $maintenance): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'type' => 'required|in:before,progress,after',
            'caption' => 'nullable|string|max:255',
        ]);

        $file = $request->file('photo');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        $file->storeAs('maintenance-photos', $filename, 'public');

        $photo = $maintenance->photos()->create([
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'type' => $request->type,
            'caption' => $request->caption,
            'uploaded_by' => auth()->id(),
        ]);

        ActivityLog::log(
            'create',
            "Menambahkan foto {$photo->type_label} pada maintenance #{$maintenance->id}",
            $photo
        );

        return response()->json([
            'success' => true,
            'photo' => [
                'id' => $photo->id,
                'url' => $photo->url,
                'type' => $photo->type,
                'type_label' => $photo->type_label,
                'caption' => $photo->caption,
                'original_name' => $photo->original_name,
            ],
        ]);
    }

    /**
     * Delete a photo
     */
    public function deletePhoto(MaintenancePhoto $photo): JsonResponse
    {
        $maintenance = $photo->maintenance;
        
        // Delete file from storage
        Storage::disk('public')->delete('maintenance-photos/' . $photo->filename);
        
        ActivityLog::log(
            'delete',
            "Menghapus foto {$photo->type_label} dari maintenance #{$maintenance->id}",
            $photo
        );

        $photo->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Update photo caption/type
     */
    public function updatePhoto(Request $request, MaintenancePhoto $photo): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'nullable|in:before,progress,after',
            'caption' => 'nullable|string|max:255',
        ]);

        if (isset($validated['type'])) {
            $photo->type = $validated['type'];
        }
        if (array_key_exists('caption', $validated)) {
            $photo->caption = $validated['caption'];
        }
        $photo->save();

        return response()->json([
            'success' => true,
            'photo' => [
                'id' => $photo->id,
                'type' => $photo->type,
                'type_label' => $photo->type_label,
                'caption' => $photo->caption,
            ],
        ]);
    }

    /**
     * Handle photo uploads for store/update
     */
    private function handlePhotoUploads(Request $request, Maintenance $maintenance): void
    {
        $photos = $request->file('photos');
        $types = $request->input('photo_types', []);
        $captions = $request->input('photo_captions', []);

        foreach ($photos as $index => $file) {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('maintenance-photos', $filename, 'public');

            $maintenance->photos()->create([
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'type' => $types[$index] ?? 'progress',
                'caption' => $captions[$index] ?? null,
                'uploaded_by' => auth()->id(),
            ]);
        }
    }
}
