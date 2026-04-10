@extends('layouts.app')

@section('title', 'Inventaris - Inventory Masjid')

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 md:mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">📦 Daftar Inventaris</h1>
    <div class="flex flex-wrap gap-2">
        <!-- Scan QR Button -->
        <a href="{{ route('qrcode.scan') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-semibold text-center touch-target">
            📷 Scan QR
        </a>
        @if(auth()->user()->canEditItems())
        <!-- Bulk Print QR Button -->
        <a href="{{ route('qrcode.bulk') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg font-semibold text-center touch-target">
            🏷️ Cetak Label
        </a>
        <a href="{{ route('items.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-semibold text-center touch-target">
            + Tambah Barang
        </a>
        @endif
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow p-3 md:p-4 mb-4 md:mb-6">
    <form action="{{ route('items.index') }}" method="GET" class="space-y-3 md:space-y-0 md:flex md:flex-wrap md:gap-4 md:items-end">
        <div class="flex-1 min-w-0 md:min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama barang..."
                class="w-full border border-gray-300 rounded-lg px-4 py-3 md:py-2 text-base">
        </div>
        <div class="grid grid-cols-2 gap-3 md:contents">
            <div class="md:w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-3 md:py-2 text-base">
                    <option value="">Semua</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                <select name="location_id" class="w-full border border-gray-300 rounded-lg px-3 py-3 md:py-2 text-base">
                    <option value="">Semua</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 md:py-2 rounded-lg font-semibold touch-target">
                Filter
            </button>
            <a href="{{ route('items.index') }}" class="flex-1 md:flex-none bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 md:py-2 rounded-lg font-semibold text-center touch-target">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Mobile Card View (visible on small screens) -->
<div class="md:hidden space-y-3">
    @forelse($items as $item)
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-start gap-3">
            <!-- Photo -->
            @if($item->hasPhoto())
                <img src="{{ $item->photo_url }}" alt="{{ $item->name }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0" loading="lazy">
            @else
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-2xl flex-shrink-0">📦</div>
            @endif
            
            <!-- Info -->
            <div class="flex-1 min-w-0">
                <a href="{{ route('items.show', $item) }}" class="font-semibold text-blue-600 hover:text-blue-800 block truncate">{{ $item->name }}</a>
                <div class="text-sm text-gray-500 mt-1">
                    <span class="inline-block">📁 {{ $item->category->name }}</span>
                    <span class="mx-1">•</span>
                    <span class="inline-block">📍 {{ $item->location->name }}</span>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <span class="font-bold text-lg">{{ $item->quantity }}</span>
                    <span class="text-gray-500">{{ $item->unit }}</span>
                    @if($item->borrowed_quantity > 0)
                        <span class="text-xs text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded">({{ $item->borrowed_quantity }} dipinjam)</span>
                    @endif
                </div>
            </div>
            
            <!-- Condition Badge -->
            <div>
                @if($item->condition == 'baik')
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Baik</span>
                @elseif($item->condition == 'perlu_perbaikan')
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Perbaiki</span>
                @else
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>
                @endif
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t">
            <a href="{{ route('items.show', $item) }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-center text-sm font-medium">
                Detail
            </a>
            @if(auth()->user()->canManageLoans() && $item->available_quantity > 0)
                <a href="{{ route('loans.create', ['item_id' => $item->id]) }}" class="flex-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-center text-sm font-medium">
                    📤 Pinjam
                </a>
            @endif
            @if(auth()->user()->canManageStock())
                <a href="{{ route('stock-movements.create', ['item_id' => $item->id]) }}" class="flex-1 bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-2 rounded-lg text-center text-sm font-medium">
                    📊 Mutasi
                </a>
            @endif
            @if(auth()->user()->canEditItems())
                <a href="{{ route('items.edit', $item) }}" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-lg text-center text-sm font-medium">
                    ✏️
                </a>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
        <div class="text-4xl mb-2">📦</div>
        <p>Belum ada barang.</p>
        @if(auth()->user()->canEditItems())
        <a href="{{ route('items.create') }}" class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
            + Tambah Barang Pertama
        </a>
        @endif
    </div>
    @endforelse
</div>

<!-- Desktop Table View (hidden on small screens) -->
<div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Kategori</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Lokasi</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                    <th class="px-4 lg:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                        @if($item->hasPhoto())
                            <img src="{{ $item->photo_url }}" alt="{{ $item->name }}" class="w-12 h-12 object-cover rounded" loading="lazy">
                        @else
                            <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center text-gray-400">📦</div>
                        @endif
                    </td>
                    <td class="px-4 lg:px-6 py-4">
                        <a href="{{ route('items.show', $item) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">{{ $item->name }}</a>
                        @if($item->note)
                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($item->note, 40) }}</div>
                        @endif
                        <!-- Show category/location on tablet -->
                        <div class="lg:hidden text-xs text-gray-400 mt-1">
                            {{ $item->category->name }} • {{ $item->location->name }}
                        </div>
                    </td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ $item->category->name }}</td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ $item->location->name }}</td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm">
                        <span class="font-medium">{{ $item->quantity }}</span> {{ $item->unit }}
                        @if($item->borrowed_quantity > 0)
                            <span class="text-yellow-600 text-xs block">({{ $item->borrowed_quantity }} dipinjam)</span>
                        @endif
                    </td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                        @if($item->condition == 'baik')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Baik</span>
                        @elseif($item->condition == 'perlu_perbaikan')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Perbaiki</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>
                        @endif
                    </td>
                    <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-1 lg:gap-2">
                            <a href="{{ route('items.show', $item) }}" class="text-gray-600 hover:text-gray-900 px-2 py-1" title="Detail">👁️</a>
                            @if(auth()->user()->canManageLoans() && $item->available_quantity > 0)
                                <a href="{{ route('loans.create', ['item_id' => $item->id]) }}" class="text-yellow-600 hover:text-yellow-900 px-2 py-1" title="Pinjam">📤</a>
                            @endif
                            @if(auth()->user()->canManageStock())
                                <a href="{{ route('stock-movements.create', ['item_id' => $item->id]) }}" class="text-purple-600 hover:text-purple-900 px-2 py-1" title="Mutasi">📊</a>
                            @endif
                            @if(auth()->user()->canEditItems())
                                <a href="{{ route('items.edit', $item) }}" class="text-blue-600 hover:text-blue-900 px-2 py-1" title="Edit">✏️</a>
                            @endif
                            @if(auth()->user()->canDeleteItems())
                                <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline" data-confirm="Yakin hapus barang ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1" title="Hapus">🗑️</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <div class="text-4xl mb-2">📦</div>
                        <p>Belum ada barang.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $items->links() }}
</div>
@endsection
