@extends('layouts.app')

@section('title', 'Laporan Lintas Masjid')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">
        📊 Laporan Lintas Masjid
        <span class="text-sm font-normal text-blue-600 ml-2">🌐 Global View</span>
    </h1>
</div>

<!-- Summary Totals -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-2xl font-bold text-blue-600">{{ $totals['total_items'] }}</p>
        <p class="text-sm text-gray-500">Total Jenis Barang</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-2xl font-bold text-green-600">{{ $totals['total_quantity'] }}</p>
        <p class="text-sm text-gray-500">Total Kuantitas</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-2xl font-bold text-yellow-600">{{ $totals['active_loans'] }}</p>
        <p class="text-sm text-gray-500">Peminjaman Aktif</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <p class="text-2xl font-bold text-red-600">{{ $totals['overdue_loans'] }}</p>
        <p class="text-sm text-gray-500">Terlambat</p>
    </div>
</div>

<!-- Per-Masjid Comparison Table -->
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Perbandingan Per Masjid</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Masjid</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jenis Barang</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kuantitas</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        <span class="text-green-600">Baik</span>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        <span class="text-yellow-600">Perlu Perbaikan</span>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                        <span class="text-red-600">Rusak</span>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pinjaman Aktif</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Terlambat</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($masjidStats as $stat)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('masjids.show', $stat['masjid']) }}" class="text-blue-600 hover:underline font-medium">
                            {{ $stat['masjid']->name }}
                        </a>
                        <div class="text-xs text-gray-400">{{ $stat['masjid']->city }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-center font-medium">{{ $stat['total_items'] }}</td>
                    <td class="px-4 py-3 text-sm text-center">{{ $stat['total_quantity'] }}</td>
                    <td class="px-4 py-3 text-sm text-center text-green-600">{{ $stat['items_good'] }}</td>
                    <td class="px-4 py-3 text-sm text-center text-yellow-600">{{ $stat['items_need_repair'] }}</td>
                    <td class="px-4 py-3 text-sm text-center text-red-600">{{ $stat['items_broken'] }}</td>
                    <td class="px-4 py-3 text-sm text-center">{{ $stat['active_loans'] }}</td>
                    <td class="px-4 py-3 text-sm text-center">
                        @if($stat['overdue_loans'] > 0)
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-semibold">{{ $stat['overdue_loans'] }}</span>
                        @else
                        <span class="text-gray-400">0</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
