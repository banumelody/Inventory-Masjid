@extends('layouts.app')

@section('title', 'Tambah Maintenance - Inventory Masjid')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-breadcrumb :items="[['label' => 'Maintenance', 'url' => route('maintenances.index')], ['label' => 'Tambah']]" />
    <div class="mb-6">
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">🔧 Tambah Maintenance</h1>

        <form action="{{ route('maintenances.store') }}" method="POST" enctype="multipart/form-data" data-draft="maintenance_create">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">Barang *</label>
                    <select name="item_id" id="item_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id', $selectedItem?->id) == $item->id ? 'selected' : '' }}>
                            {{ $item->name }} ({{ $item->category->name }} - {{ $item->location->name }})
                        </option>
                        @endforeach
                    </select>
                    @error('item_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Maintenance *</label>
                    <select name="type" id="type" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="perbaikan" {{ old('type') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="perawatan" {{ old('type') == 'perawatan' ? 'selected' : '' }}>Perawatan</option>
                        <option value="penggantian_part" {{ old('type') == 'penggantian_part' ? 'selected' : '' }}>Penggantian Part</option>
                    </select>
                    @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi *</label>
                    <textarea name="description" id="description" rows="3" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500"
                        placeholder="Jelaskan masalah atau pekerjaan yang perlu dilakukan...">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="vendor" class="block text-sm font-medium text-gray-700 mb-2">Vendor/Teknisi</label>
                        <input type="text" name="vendor" id="vendor" value="{{ old('vendor') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500"
                            placeholder="Nama vendor/teknisi">
                    </div>
                    <div>
                        <label for="vendor_phone" class="block text-sm font-medium text-gray-700 mb-2">Telepon Vendor</label>
                        <input type="text" name="vendor_phone" id="vendor_phone" value="{{ old('vendor_phone') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500"
                            placeholder="08xxx">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">Perkiraan Biaya (Rp)</label>
                        <input type="number" name="cost" id="cost" value="{{ old('cost') }}" min="0" step="1000"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500"
                            placeholder="0">
                    </div>
                    <div>
                        <label for="started_at" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="started_at" id="started_at" value="{{ old('started_at') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika belum dimulai</p>
                    </div>
                </div>

                <div>
                    <label for="estimated_completion" class="block text-sm font-medium text-gray-700 mb-2">Estimasi Selesai</label>
                    <input type="date" name="estimated_completion" id="estimated_completion" value="{{ old('estimated_completion') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                    <textarea name="notes" id="notes" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500"
                        placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                </div>

                <!-- Photo Upload Section -->
                <div class="border-t pt-4 mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">📷 Foto Dokumentasi</label>
                    <p class="text-xs text-gray-500 mb-3">Upload foto kondisi barang (sebelum/proses/sesudah). Max 5MB per foto.</p>
                    
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

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('maintenances.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">Batal</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">Simpan</button>
            </div>
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
</script>
@endsection
