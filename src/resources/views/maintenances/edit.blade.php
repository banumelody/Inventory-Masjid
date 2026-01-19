@extends('layouts.app')

@section('title', 'Edit Maintenance - Inventory Masjid')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('maintenances.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali ke Daftar</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">🔧 Edit Maintenance</h1>

        <form action="{{ route('maintenances.update', $maintenance) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">Barang *</label>
                    <select name="item_id" id="item_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id', $maintenance->item_id) == $item->id ? 'selected' : '' }}>
                            {{ $item->name }} ({{ $item->category->name }} - {{ $item->location->name }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Maintenance *</label>
                        <select name="type" id="type" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                            <option value="perbaikan" {{ old('type', $maintenance->type) == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                            <option value="perawatan" {{ old('type', $maintenance->type) == 'perawatan' ? 'selected' : '' }}>Perawatan</option>
                            <option value="penggantian_part" {{ old('type', $maintenance->type) == 'penggantian_part' ? 'selected' : '' }}>Penggantian Part</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" id="status" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                            <option value="pending" {{ old('status', $maintenance->status) == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="in_progress" {{ old('status', $maintenance->status) == 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                            <option value="completed" {{ old('status', $maintenance->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ old('status', $maintenance->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi *</label>
                    <textarea name="description" id="description" rows="3" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">{{ old('description', $maintenance->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="vendor" class="block text-sm font-medium text-gray-700 mb-2">Vendor/Teknisi</label>
                        <input type="text" name="vendor" id="vendor" value="{{ old('vendor', $maintenance->vendor) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="vendor_phone" class="block text-sm font-medium text-gray-700 mb-2">Telepon Vendor</label>
                        <input type="text" name="vendor_phone" id="vendor_phone" value="{{ old('vendor_phone', $maintenance->vendor_phone) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Biaya (Rp)</label>
                        <input type="number" name="cost" id="cost" value="{{ old('cost', $maintenance->cost) }}" min="0" step="1000"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="started_at" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="started_at" id="started_at" value="{{ old('started_at', $maintenance->started_at?->format('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="completed_at" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                        <input type="date" name="completed_at" id="completed_at" value="{{ old('completed_at', $maintenance->completed_at?->format('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <div>
                    <label for="estimated_completion" class="block text-sm font-medium text-gray-700 mb-2">Estimasi Selesai</label>
                    <input type="date" name="estimated_completion" id="estimated_completion" value="{{ old('estimated_completion', $maintenance->estimated_completion?->format('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="notes" id="notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">{{ old('notes', $maintenance->notes) }}</textarea>
                </div>

                <!-- Existing Photos -->
                @if($maintenance->photos->count() > 0)
                <div class="border-t pt-4 mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">📷 Foto yang Sudah Ada</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($maintenance->photos as $photo)
                        <div class="relative group" id="photo-{{ $photo->id }}">
                            <img src="{{ $photo->url }}" alt="{{ $photo->caption }}" class="w-full h-24 object-cover rounded-lg">
                            <span class="absolute bottom-1 left-1 text-xs px-1.5 py-0.5 rounded bg-{{ $photo->type_color }}-100 text-{{ $photo->type_color }}-800">
                                {{ $photo->type_label }}
                            </span>
                            <button type="button" onclick="deletePhoto({{ $photo->id }})" 
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs opacity-0 group-hover:opacity-100 transition">
                                ✕
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Add New Photos -->
                <div class="border-t pt-4 mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">📷 Tambah Foto Baru</label>
                    <p class="text-xs text-gray-500 mb-3">Upload foto tambahan. Max 5MB per foto.</p>
                    
                    <div id="photo-upload-container" class="space-y-3">
                        <div class="photo-upload-row flex flex-col sm:flex-row gap-2 p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <input type="file" name="photos[]" accept="image/*" 
                                    class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            </div>
                            <div class="w-full sm:w-32">
                                <select name="photo_types[]" class="w-full text-sm border border-gray-300 rounded-lg px-2 py-2">
                                    @foreach($photoTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1">
                                <input type="text" name="photo_captions[]" placeholder="Keterangan (opsional)" 
                                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2">
                            </div>
                            <button type="button" onclick="removePhotoRow(this)" class="text-red-500 hover:text-red-700 px-2">✕</button>
                        </div>
                    </div>
                    
                    <button type="button" onclick="addPhotoRow()" class="mt-3 text-sm text-green-600 hover:text-green-800 font-medium">
                        + Tambah Foto Lain
                    </button>
                </div>
            </div>

            <div class="flex justify-between mt-6 pt-6 border-t">
                <button type="button" onclick="document.getElementById('delete-form').submit()" 
                    class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-3 rounded-lg font-semibold">Hapus</button>
                <div class="flex space-x-3">
                    <a href="{{ route('maintenances.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">Batal</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">Update</button>
                </div>
            </div>
        </form>
        
        <form id="delete-form" action="{{ route('maintenances.destroy', $maintenance) }}" method="POST" class="hidden" onsubmit="return confirm('Yakin hapus data maintenance ini?')">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
const photoTypes = @json($photoTypes);

function addPhotoRow() {
    const container = document.getElementById('photo-upload-container');
    const row = document.createElement('div');
    row.className = 'photo-upload-row flex flex-col sm:flex-row gap-2 p-3 bg-gray-50 rounded-lg';
    
    let typeOptions = '';
    for (const [value, label] of Object.entries(photoTypes)) {
        typeOptions += `<option value="${value}">${label}</option>`;
    }
    
    row.innerHTML = `
        <div class="flex-1">
            <input type="file" name="photos[]" accept="image/*" 
                class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
        </div>
        <div class="w-full sm:w-32">
            <select name="photo_types[]" class="w-full text-sm border border-gray-300 rounded-lg px-2 py-2">
                ${typeOptions}
            </select>
        </div>
        <div class="flex-1">
            <input type="text" name="photo_captions[]" placeholder="Keterangan (opsional)" 
                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <button type="button" onclick="removePhotoRow(this)" class="text-red-500 hover:text-red-700 px-2">✕</button>
    `;
    container.appendChild(row);
}

function removePhotoRow(btn) {
    const container = document.getElementById('photo-upload-container');
    if (container.children.length > 1) {
        btn.closest('.photo-upload-row').remove();
    }
}

async function deletePhoto(photoId) {
    if (!confirm('Yakin hapus foto ini?')) return;
    
    try {
        const response = await fetch(`/maintenance-photos/${photoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
        
        const result = await response.json();
        if (result.success) {
            document.getElementById('photo-' + photoId).remove();
        } else {
            alert('Gagal menghapus foto');
        }
    } catch (error) {
        alert('Terjadi kesalahan');
    }
}
</script>
@endsection
