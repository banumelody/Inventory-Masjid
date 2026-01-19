@extends('layouts.app')

@section('title', 'Maintenance - Inventory Masjid')

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 md:mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">🔧 Maintenance</h1>
    @if(auth()->user()->canEditItems())
    <a href="{{ route('maintenances.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-semibold text-center touch-target">
        + Tambah Maintenance
    </a>
    @endif
</div>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 md:p-4">
        <div class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</div>
        <div class="text-xs text-yellow-600">Menunggu</div>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 md:p-4">
        <div class="text-2xl font-bold text-blue-700">{{ $stats['in_progress'] }}</div>
        <div class="text-xs text-blue-600">Dalam Proses</div>
    </div>
    <div class="bg-green-50 border border-green-200 rounded-lg p-3 md:p-4">
        <div class="text-2xl font-bold text-green-700">{{ $stats['completed_this_month'] }}</div>
        <div class="text-xs text-green-600">Selesai Bulan Ini</div>
    </div>
    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 md:p-4">
        <div class="text-2xl font-bold text-purple-700">Rp {{ number_format($stats['total_cost_this_month'], 0, ',', '.') }}</div>
        <div class="text-xs text-purple-600">Biaya Bulan Ini</div>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow p-3 md:p-4 mb-4 md:mb-6">
    <form action="{{ route('maintenances.index') }}" method="GET" class="space-y-3 md:space-y-0 md:flex md:flex-wrap md:gap-4 md:items-end">
        <div class="flex-1 min-w-0 md:min-w-[150px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Barang, vendor..."
                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-base">
        </div>
        <div class="md:w-36">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-base">
                <option value="">Semua</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>
        <div class="md:w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
            <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-base">
                <option value="">Semua</option>
                <option value="perbaikan" {{ request('type') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                <option value="perawatan" {{ request('type') == 'perawatan' ? 'selected' : '' }}>Perawatan</option>
                <option value="penggantian_part" {{ request('type') == 'penggantian_part' ? 'selected' : '' }}>Penggantian Part</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold">Filter</button>
            <a href="{{ route('maintenances.index') }}" class="flex-1 md:flex-none bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-semibold text-center">Reset</a>
        </div>
    </form>
</div>

<!-- Mobile Card View -->
<div class="md:hidden space-y-3">
    @forelse($maintenances as $maintenance)
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-start mb-2">
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-gray-900 truncate">{{ $maintenance->item->name }}</div>
                <div class="text-sm text-gray-500">{{ $maintenance->type_label }}</div>
            </div>
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $maintenance->status_color }}-100 text-{{ $maintenance->status_color }}-800">
                {{ $maintenance->status_label }}
            </span>
        </div>
        
        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $maintenance->description }}</p>
        
        <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
            @if($maintenance->vendor)
            <div>🏪 {{ $maintenance->vendor }}</div>
            @endif
            @if($maintenance->cost)
            <div>💰 Rp {{ number_format($maintenance->cost, 0, ',', '.') }}</div>
            @endif
        </div>
        
        <div class="flex gap-2 pt-3 border-t">
            <a href="{{ route('maintenances.show', $maintenance) }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-center text-sm">Detail</a>
            @if(!$maintenance->isCompleted() && !$maintenance->isCancelled() && auth()->user()->canEditItems())
            <a href="{{ route('maintenances.edit', $maintenance) }}" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-lg text-sm">✏️</a>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
        <div class="text-4xl mb-2">🔧</div>
        <p>Belum ada data maintenance.</p>
    </div>
    @endforelse
</div>

<!-- Desktop Table View -->
<div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biaya</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($maintenances as $maintenance)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $maintenance->item->name }}</div>
                        <div class="text-xs text-gray-500">{{ $maintenance->item->category->name }}</div>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-500">{{ $maintenance->type_label }}</td>
                    <td class="px-4 py-4">
                        <div class="text-sm text-gray-900">{{ $maintenance->vendor ?? '-' }}</div>
                        @if($maintenance->vendor_phone)
                        <div class="text-xs text-gray-500">{{ $maintenance->vendor_phone }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-900">
                        {{ $maintenance->cost ? 'Rp ' . number_format($maintenance->cost, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-4 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $maintenance->status_color }}-100 text-{{ $maintenance->status_color }}-800">
                            {{ $maintenance->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-500">
                        @if($maintenance->started_at)
                            {{ $maintenance->started_at->format('d/m/Y') }}
                        @else
                            {{ $maintenance->created_at->format('d/m/Y') }}
                        @endif
                    </td>
                    <td class="px-4 py-4 text-right text-sm">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('maintenances.show', $maintenance) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                            @if(!$maintenance->isCompleted() && !$maintenance->isCancelled() && auth()->user()->canEditItems())
                            <a href="{{ route('maintenances.edit', $maintenance) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <div class="text-4xl mb-2">🔧</div>
                        <p>Belum ada data maintenance.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $maintenances->links() }}
</div>
@endsection
