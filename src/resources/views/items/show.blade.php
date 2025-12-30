@extends('layouts.app')

@section('title', $item->name . ' - Inventory Masjid')

@section('content')
<div class="mb-6">
    <a href="{{ route('items.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali ke Daftar</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <h1 class="text-2xl font-bold text-gray-800">{{ $item->name }}</h1>
                <div class="flex space-x-2">
                    @if(auth()->user()->canEditItems())
                        <a href="{{ route('items.edit', $item) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">Edit</a>
                    @endif
                    @if(auth()->user()->canDeleteItems())
                        <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus barang ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">Hapus</button>
                        </form>
                    @endif
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm text-gray-500">Kategori</dt>
                    <dd class="font-medium">{{ $item->category->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Lokasi</dt>
                    <dd class="font-medium">{{ $item->location->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Stok Total</dt>
                    <dd class="font-medium text-lg">{{ $item->quantity }} {{ $item->unit }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Tersedia</dt>
                    <dd class="font-medium text-lg text-green-600">{{ $item->available_quantity }} {{ $item->unit }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Sedang Dipinjam</dt>
                    <dd class="font-medium {{ $item->borrowed_quantity > 0 ? 'text-yellow-600' : '' }}">
                        {{ $item->borrowed_quantity }} {{ $item->unit }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Kondisi</dt>
                    <dd>
                        @if($item->condition == 'baik')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Baik</span>
                        @elseif($item->condition == 'perlu_perbaikan')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Perlu Perbaikan</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>
                        @endif
                    </dd>
                </div>
            </dl>

            @if($item->note)
            <div class="mt-4 pt-4 border-t">
                <dt class="text-sm text-gray-500">Catatan</dt>
                <dd class="mt-1">{{ $item->note }}</dd>
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Aksi Cepat</h2>
            <div class="flex flex-wrap gap-3">
                @if(auth()->user()->canManageLoans() && $item->available_quantity > 0)
                    <a href="{{ route('loans.create', ['item_id' => $item->id]) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                        📤 Pinjamkan Barang
                    </a>
                @endif
                @if(auth()->user()->canManageStock())
                    <a href="{{ route('stock-movements.create', ['item_id' => $item->id]) }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                        📊 Mutasi Stok
                    </a>
                @endif
                <a href="{{ route('stock-movements.item', $item) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    📜 Riwayat Mutasi
                </a>
            </div>
        </div>

        <!-- Active Loans -->
        @if($item->activeLoans->count() > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Sedang Dipinjam</h2>
            <div class="space-y-3">
                @foreach($item->activeLoans as $loan)
                <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                    <div>
                        <p class="font-medium">{{ $loan->borrower_name }}</p>
                        <p class="text-sm text-gray-500">{{ $loan->quantity }} {{ $item->unit }} - sejak {{ $loan->borrowed_at->format('d/m/Y') }}</p>
                    </div>
                    @if($loan->isOverdue())
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Terlambat!</span>
                    @endif
                    @if(auth()->user()->canManageLoans())
                        <a href="{{ route('loans.return', $loan) }}" class="text-green-600 hover:text-green-800 text-sm">Kembalikan</a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Stock Movements -->
        @if($item->stockMovements->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Mutasi Stok Terbaru</h2>
                <a href="{{ route('stock-movements.item', $item) }}" class="text-blue-600 hover:text-blue-800 text-sm">Lihat Semua</a>
            </div>
            <div class="space-y-2">
                @foreach($item->stockMovements as $movement)
                <div class="flex justify-between items-center py-2 border-b last:border-0">
                    <div>
                        <span class="text-sm {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }} font-medium">
                            {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                        </span>
                        <span class="text-sm text-gray-500">{{ $movement->reason }}</span>
                    </div>
                    <span class="text-sm text-gray-400">{{ $movement->moved_at->format('d/m/Y') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar - Photo -->
    <div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Foto Barang</h2>
            @if($item->hasPhoto())
                <img src="{{ $item->photo_url }}" alt="{{ $item->name }}" class="w-full rounded-lg">
            @else
                <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                    <div class="text-center">
                        <span class="text-6xl">📦</span>
                        <p class="mt-2">Tidak ada foto</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
