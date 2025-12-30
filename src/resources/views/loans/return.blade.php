@extends('layouts.app')

@section('title', 'Kembalikan Barang - Inventory Masjid')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Kembalikan Barang</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Detail Peminjaman</h2>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Barang</dt>
                <dd class="font-medium">{{ $loan->item->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Peminjam</dt>
                <dd class="font-medium">{{ $loan->borrower_name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Jumlah</dt>
                <dd class="font-medium">{{ $loan->quantity }} {{ $loan->item->unit }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Tanggal Pinjam</dt>
                <dd class="font-medium">{{ $loan->borrowed_at->format('d/m/Y') }}</dd>
            </div>
            @if($loan->due_at)
            <div>
                <dt class="text-gray-500">Jatuh Tempo</dt>
                <dd class="font-medium {{ $loan->isOverdue() ? 'text-red-600' : '' }}">
                    {{ $loan->due_at->format('d/m/Y') }}
                    @if($loan->isOverdue())
                        (Terlambat!)
                    @endif
                </dd>
            </div>
            @endif
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Form Pengembalian</h2>
        <form action="{{ route('loans.return.store', $loan) }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="returned_at" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kembali *</label>
                    <input type="date" name="returned_at" id="returned_at" value="{{ old('returned_at', date('Y-m-d')) }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="returned_condition" class="block text-sm font-medium text-gray-700 mb-2">Kondisi Saat Kembali *</label>
                    <select name="returned_condition" id="returned_condition" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="baik" {{ old('returned_condition') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="perlu_perbaikan" {{ old('returned_condition') == 'perlu_perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                        <option value="rusak" {{ old('returned_condition') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Pengembalian</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('loans.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">Batal</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">Konfirmasi Pengembalian</button>
            </div>
        </form>
    </div>
</div>
@endsection
