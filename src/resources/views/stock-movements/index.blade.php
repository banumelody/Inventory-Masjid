@extends('layouts.app')

@section('title', 'Mutasi Stok - Inventory Masjid')

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 md:mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">📊 Riwayat Mutasi Stok</h1>
    @if(auth()->user()->canManageStock())
    <a href="{{ route('stock-movements.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-semibold text-center touch-target">
        + Tambah Mutasi
    </a>
    @endif
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow p-3 md:p-4 mb-4 md:mb-6">
    <form action="{{ route('stock-movements.index') }}" method="GET" class="space-y-3 md:space-y-0 md:flex md:flex-wrap md:gap-4 md:items-end">
        <div class="flex-1 min-w-0 md:min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Barang</label>
            <select name="item_id" class="w-full border border-gray-300 rounded-lg px-4 py-3 md:py-2 text-base">
                <option value="">Semua Barang</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="md:w-40">
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
            <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-3 md:py-2 text-base">
                <option value="">Semua</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Masuk</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Keluar</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 md:py-2 rounded-lg font-semibold touch-target">Filter</button>
            <a href="{{ route('stock-movements.index') }}" class="flex-1 md:flex-none bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 md:py-2 rounded-lg font-semibold text-center touch-target">Reset</a>
        </div>
    </form>
</div>

<!-- Mobile Card View -->
<div class="md:hidden space-y-3">
    @forelse($movements as $movement)
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-start mb-2">
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-gray-900 truncate">{{ $movement->item->name }}</div>
                <div class="text-xs text-gray-500">{{ $movement->item->category->name }}</div>
            </div>
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $movement->type_color }}-100 text-{{ $movement->type_color }}-800">
                {{ $movement->type_label }}
            </span>
        </div>
        
        <div class="flex justify-between items-center text-sm mb-2">
            <div class="text-gray-500">📅 {{ $movement->moved_at->format('d/m/Y') }}</div>
            <div class="font-bold text-lg text-{{ $movement->type_color }}-600">
                {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }} {{ $movement->item->unit }}
            </div>
        </div>
        
        <div class="text-sm border-t pt-2">
            <span class="text-gray-600">{{ $movement->reason }}</span>
            @if($movement->notes)
                <span class="text-gray-400 ml-1">• {{ $movement->notes }}</span>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
        <div class="text-4xl mb-2">📊</div>
        <p>Belum ada data mutasi stok.</p>
        @if(auth()->user()->canManageStock())
        <a href="{{ route('stock-movements.create') }}" class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
            + Tambah Mutasi
        </a>
        @endif
    </div>
    @endforelse
</div>

<!-- Desktop Table View -->
<div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Catatan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($movements as $movement)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $movement->moved_at->format('d/m/Y') }}</td>
                    <td class="px-4 lg:px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $movement->item->name }}</div>
                        <div class="text-xs text-gray-500">{{ $movement->item->category->name }}</div>
                    </td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $movement->type_color }}-100 text-{{ $movement->type_color }}-800">
                            {{ $movement->type_label }}
                        </span>
                    </td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm">
                        <span class="font-medium text-{{ $movement->type_color }}-600">
                            {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                        </span>
                        {{ $movement->item->unit }}
                    </td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $movement->reason }}</td>
                    <td class="px-4 lg:px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">{{ $movement->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <div class="text-4xl mb-2">📊</div>
                        <p>Belum ada data mutasi stok.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $movements->links() }}
</div>
@endsection
