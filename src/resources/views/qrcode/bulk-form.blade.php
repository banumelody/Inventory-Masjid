@extends('layouts.app')

@section('title', 'Cetak Label QR Massal - Inventory Masjid')

@section('content')
<div class="mb-6">
    <a href="{{ route('items.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali ke Daftar</a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-xl font-bold text-gray-800 mb-6">🏷️ Cetak Label QR Massal</h1>

    <form action="{{ route('qrcode.bulk.print') }}" method="POST">
        @csrf
        
        <!-- Size Selection -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran Label</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="size" value="small" checked class="text-green-600 focus:ring-green-500">
                    <span>Kecil (60×35mm)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="size" value="medium" class="text-green-600 focus:ring-green-500">
                    <span>Sedang (80×45mm)</span>
                </label>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-4 flex gap-2">
            <button type="button" onclick="selectAll()" class="text-sm text-blue-600 hover:text-blue-800">Pilih Semua</button>
            <span class="text-gray-300">|</span>
            <button type="button" onclick="selectNone()" class="text-sm text-blue-600 hover:text-blue-800">Hapus Pilihan</button>
            <span class="text-gray-300">|</span>
            <button type="button" onclick="selectNoQr()" class="text-sm text-blue-600 hover:text-blue-800">Pilih yang Belum Ada QR</button>
        </div>

        <!-- Items Selection -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Barang <span id="selected-count" class="text-green-600">(0 dipilih)</span>
            </label>
            <div class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto">
                @foreach($items as $item)
                <label class="flex items-center gap-3 p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0">
                    <input type="checkbox" 
                           name="items[]" 
                           value="{{ $item->id }}" 
                           data-has-qr="{{ $item->hasQrCode() ? '1' : '0' }}"
                           class="item-checkbox text-green-600 focus:ring-green-500 rounded"
                           {{ in_array($item->id, $selectedIds) ? 'checked' : '' }}
                           onchange="updateCount()">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium truncate">{{ $item->name }}</p>
                        <p class="text-sm text-gray-500">{{ $item->category->name }} • {{ $item->location->name }}</p>
                    </div>
                    @if($item->hasQrCode())
                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">✓ QR</span>
                    @else
                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">Belum ada QR</span>
                    @endif
                </label>
                @endforeach
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                🖨️ Siapkan untuk Cetak
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
function updateCount() {
    const count = document.querySelectorAll('.item-checkbox:checked').length;
    document.getElementById('selected-count').textContent = `(${count} dipilih)`;
}

function selectAll() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = true);
    updateCount();
}

function selectNone() {
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
    updateCount();
}

function selectNoQr() {
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.checked = cb.dataset.hasQr === '0';
    });
    updateCount();
}

// Initial count
document.addEventListener('DOMContentLoaded', updateCount);
</script>
@endsection
