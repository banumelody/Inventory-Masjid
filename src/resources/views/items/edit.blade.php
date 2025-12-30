@extends('layouts.app')

@section('title', 'Edit Barang - Inventory Masjid')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Barang</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Barang *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <select name="category_id" id="category_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">Lokasi *</label>
                    <select name="location_id" id="location_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="">Pilih Lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id', $item->location_id) == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $item->quantity) }}" min="0" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">Satuan *</label>
                    <select name="unit" id="unit" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="pcs" {{ old('unit', $item->unit) == 'pcs' ? 'selected' : '' }}>pcs</option>
                        <option value="unit" {{ old('unit', $item->unit) == 'unit' ? 'selected' : '' }}>unit</option>
                        <option value="set" {{ old('unit', $item->unit) == 'set' ? 'selected' : '' }}>set</option>
                        <option value="buah" {{ old('unit', $item->unit) == 'buah' ? 'selected' : '' }}>buah</option>
                        <option value="lembar" {{ old('unit', $item->unit) == 'lembar' ? 'selected' : '' }}>lembar</option>
                        <option value="roll" {{ old('unit', $item->unit) == 'roll' ? 'selected' : '' }}>roll</option>
                    </select>
                </div>
                <div>
                    <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">Kondisi *</label>
                    <select name="condition" id="condition" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="baik" {{ old('condition', $item->condition) == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="perlu_perbaikan" {{ old('condition', $item->condition) == 'perlu_perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                        <option value="rusak" {{ old('condition', $item->condition) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Barang</label>
                @if($item->hasPhoto())
                <div class="mb-3 flex items-center space-x-4">
                    <img src="{{ $item->photo_url }}" alt="{{ $item->name }}" class="w-24 h-24 object-cover rounded">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remove_photo" value="1" class="mr-2">
                        Hapus foto
                    </label>
                </div>
                @endif
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG. Maks 5MB. Kosongkan jika tidak ingin mengubah foto.</p>
            </div>

            <div class="mb-6">
                <label for="note" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea name="note" id="note" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">{{ old('note', $item->note) }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('items.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">
                    Batal
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
