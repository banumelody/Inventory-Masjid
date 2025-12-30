@extends('layouts.app')

@section('title', 'Riwayat Mutasi {{ $item->name }} - Inventory Masjid')

@section('content')
<div class="mb-6">
    <a href="{{ route('items.show', $item) }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali ke Detail Barang</a>
</div>

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Mutasi: {{ $item->name }}</h1>
        <p class="text-gray-600">Stok saat ini: {{ $item->quantity }} {{ $item->unit }}</p>
    </div>
    @if(auth()->user()->canManageStock())
    <a href="{{ route('stock-movements.create', ['item_id' => $item->id]) }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
        + Tambah Mutasi
    </a>
    @endif
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($movements as $movement)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $movement->moved_at->format('d/m/Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $movement->type_color }}-100 text-{{ $movement->type_color }}-800">
                        {{ $movement->type_label }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="font-medium text-{{ $movement->type_color }}-600">
                        {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                    </span>
                    {{ $item->unit }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $movement->reason }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $movement->notes ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada riwayat mutasi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $movements->links() }}
</div>
@endsection
