@extends('layouts.app')

@section('title', 'Dashboard - Inventory Masjid')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">📊 Dashboard</h1>

<!-- Stats Overview -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-3xl font-bold text-green-600">{{ $stats['total_items'] }}</div>
        <div class="text-sm text-gray-500">Total Jenis Barang</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['total_quantity']) }}</div>
        <div class="text-sm text-gray-500">Total Stok</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-3xl font-bold text-yellow-600">{{ $stats['active_loans'] }}</div>
        <div class="text-sm text-gray-500">Sedang Dipinjam</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-3xl font-bold {{ $stats['overdue_loans'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $stats['overdue_loans'] }}</div>
        <div class="text-sm text-gray-500">Terlambat Kembali</div>
    </div>
</div>

<!-- Kondisi Barang -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <span class="text-2xl mr-3">✅</span>
            <div>
                <div class="text-2xl font-bold text-green-700">{{ $stats['items_good'] }}</div>
                <div class="text-sm text-green-600">Kondisi Baik</div>
            </div>
        </div>
    </div>
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
            <span class="text-2xl mr-3">⚠️</span>
            <div>
                <div class="text-2xl font-bold text-yellow-700">{{ $stats['items_need_repair'] }}</div>
                <div class="text-sm text-yellow-600">Perlu Perbaikan</div>
            </div>
        </div>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <span class="text-2xl mr-3">❌</span>
            <div>
                <div class="text-2xl font-bold text-red-700">{{ $stats['items_broken'] }}</div>
                <div class="text-sm text-red-600">Rusak</div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Overdue Loans Warning -->
    @if($overdueLoans->count() > 0)
    <div class="bg-red-50 border border-red-300 rounded-lg p-4">
        <h2 class="text-lg font-semibold text-red-800 mb-3">🚨 Peminjaman Terlambat</h2>
        <div class="space-y-2">
            @foreach($overdueLoans as $loan)
            <div class="bg-white rounded p-3 flex justify-between items-center">
                <div>
                    <div class="font-medium">{{ $loan->item->name }}</div>
                    <div class="text-sm text-gray-500">{{ $loan->borrower_name }} - sejak {{ $loan->borrowed_at->format('d/m/Y') }}</div>
                </div>
                <div class="text-red-600 text-sm font-medium">
                    {{ $loan->due_at->diffForHumans() }}
                </div>
            </div>
            @endforeach
        </div>
        <a href="{{ route('loans.index', ['status' => 'overdue']) }}" class="block text-center text-red-600 hover:text-red-800 mt-3 text-sm">
            Lihat Semua →
        </a>
    </div>
    @endif

    <!-- Recent Items -->
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-3">📦 Barang Terbaru</h2>
        <div class="space-y-2">
            @forelse($recentItems as $item)
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div>
                    <a href="{{ route('items.show', $item) }}" class="font-medium text-blue-600 hover:text-blue-800">{{ $item->name }}</a>
                    <div class="text-sm text-gray-500">{{ $item->category->name }}</div>
                </div>
                <div class="text-sm text-gray-500">{{ $item->quantity }} {{ $item->unit }}</div>
            </div>
            @empty
            <p class="text-gray-500 text-sm">Belum ada barang.</p>
            @endforelse
        </div>
        <a href="{{ route('items.index') }}" class="block text-center text-blue-600 hover:text-blue-800 mt-3 text-sm">
            Lihat Semua →
        </a>
    </div>

    <!-- Recent Movements -->
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-3">📊 Mutasi Terbaru</h2>
        <div class="space-y-2">
            @forelse($recentMovements as $movement)
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div>
                    <div class="font-medium">{{ $movement->item->name }}</div>
                    <div class="text-sm text-gray-500">{{ $movement->reason }}</div>
                </div>
                <div class="text-sm font-medium {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-sm">Belum ada mutasi.</p>
            @endforelse
        </div>
        <a href="{{ route('stock-movements.index') }}" class="block text-center text-blue-600 hover:text-blue-800 mt-3 text-sm">
            Lihat Semua →
        </a>
    </div>

    <!-- Items by Category -->
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-3">📁 Per Kategori</h2>
        <div class="space-y-2">
            @foreach($itemsByCategory as $category)
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div class="font-medium">{{ $category->name }}</div>
                <div class="text-sm text-gray-500">{{ $category->items_count }} barang</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Items by Location -->
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-3">📍 Per Lokasi</h2>
        <div class="space-y-2">
            @foreach($itemsByLocation as $location)
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div class="font-medium">{{ $location->name }}</div>
                <div class="text-sm text-gray-500">{{ $location->items_count }} barang</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow p-4">
    <h2 class="text-lg font-semibold mb-3">⚡ Aksi Cepat</h2>
    <div class="flex flex-wrap gap-3">
        @if(auth()->user()->canEditItems())
        <a href="{{ route('items.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">+ Tambah Barang</a>
        @endif
        @if(auth()->user()->canManageLoans())
        <a href="{{ route('loans.create') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">📤 Pinjamkan</a>
        @endif
        @if(auth()->user()->canManageStock())
        <a href="{{ route('stock-movements.create') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">📊 Mutasi Stok</a>
        @endif
        <a href="{{ route('export.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">📥 Export</a>
        <a href="{{ route('feedbacks.create') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">💬 Feedback</a>
    </div>
</div>
@endsection
