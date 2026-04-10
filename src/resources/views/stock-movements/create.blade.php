@extends('layouts.app')

@section('title', 'Tambah Mutasi - Inventory Masjid')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-breadcrumb :items="[['label' => 'Mutasi Stok', 'url' => route('stock-movements.index')], ['label' => 'Tambah']]" />
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Mutasi Stok</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('stock-movements.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">Barang *</label>
                <select name="item_id" id="item_id" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    <option value="">Pilih Barang</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" 
                            data-stock="{{ $item->quantity }}"
                            data-unit="{{ $item->unit }}"
                            {{ (old('item_id') == $item->id || ($selectedItem && $selectedItem->id == $item->id)) ? 'selected' : '' }}>
                            {{ $item->name }} (Stok: {{ $item->quantity }} {{ $item->unit }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Mutasi *</label>
                    <select name="type" id="type" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Masuk (+)</option>
                        <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Keluar (-)</option>
                    </select>
                </div>
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Jumlah *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan *</label>
                    <select name="reason" id="reason" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="">Pilih Alasan</option>
                        <option value="Pembelian" {{ old('reason') == 'Pembelian' ? 'selected' : '' }}>Pembelian</option>
                        <option value="Donasi Masuk" {{ old('reason') == 'Donasi Masuk' ? 'selected' : '' }}>Donasi Masuk</option>
                        <option value="Penyesuaian Stok" {{ old('reason') == 'Penyesuaian Stok' ? 'selected' : '' }}>Penyesuaian Stok</option>
                        <option value="Barang Rusak" {{ old('reason') == 'Barang Rusak' ? 'selected' : '' }}>Barang Rusak</option>
                        <option value="Barang Hilang" {{ old('reason') == 'Barang Hilang' ? 'selected' : '' }}>Barang Hilang</option>
                        <option value="Donasi Keluar" {{ old('reason') == 'Donasi Keluar' ? 'selected' : '' }}>Donasi Keluar</option>
                        <option value="Lainnya" {{ old('reason') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label for="moved_at" class="block text-sm font-medium text-gray-700 mb-2">Tanggal *</label>
                    <input type="date" name="moved_at" id="moved_at" value="{{ old('moved_at', date('Y-m-d')) }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('stock-movements.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">Batal</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
