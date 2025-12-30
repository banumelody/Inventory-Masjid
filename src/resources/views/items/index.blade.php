@extends('layouts.app')

@section('title', 'Inventaris - Inventory Masjid')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Daftar Inventaris</h1>
    @if(auth()->user()->canEditItems())
    <a href="{{ route('items.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
        + Tambah Barang
    </a>
    @endif
</div>

<!-- Filter -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form action="{{ route('items.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama barang..."
                class="w-full border border-gray-300 rounded-lg px-4 py-2">
        </div>
        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
            <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
            <select name="location_id" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <option value="">Semua Lokasi</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                        {{ $location->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
            Filter
        </button>
        <a href="{{ route('items.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold">
            Reset
        </a>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($items as $item)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($item->hasPhoto())
                        <img src="{{ $item->photo_url }}" alt="{{ $item->name }}" class="w-12 h-12 object-cover rounded">
                    @else
                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                            📦
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <a href="{{ route('items.show', $item) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">{{ $item->name }}</a>
                    @if($item->note)
                        <div class="text-sm text-gray-500">{{ Str::limit($item->note, 50) }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->category->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->location->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="font-medium">{{ $item->quantity }}</span> {{ $item->unit }}
                    @if($item->borrowed_quantity > 0)
                        <span class="text-yellow-600 text-xs">({{ $item->borrowed_quantity }} dipinjam)</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($item->condition == 'baik')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Baik</span>
                    @elseif($item->condition == 'perlu_perbaikan')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Perlu Perbaikan</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <a href="{{ route('items.show', $item) }}" class="text-gray-600 hover:text-gray-900">Detail</a>
                    @if(auth()->user()->canManageLoans() && $item->available_quantity > 0)
                        <a href="{{ route('loans.create', ['item_id' => $item->id]) }}" class="text-yellow-600 hover:text-yellow-900">Pinjam</a>
                    @endif
                    @if(auth()->user()->canManageStock())
                        <a href="{{ route('stock-movements.create', ['item_id' => $item->id]) }}" class="text-purple-600 hover:text-purple-900">Mutasi</a>
                    @endif
                    @if(auth()->user()->canEditItems())
                        <a href="{{ route('items.edit', $item) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                    @endif
                    @if(auth()->user()->canDeleteItems())
                        <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus barang ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada barang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $items->links() }}
</div>
@endsection
