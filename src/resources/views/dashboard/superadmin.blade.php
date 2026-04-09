@extends('layouts.app')

@section('title', 'Dashboard Superadmin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">👑 Dashboard Superadmin</h1>
            <p class="text-gray-500 mt-1">Ringkasan platform inventaris seluruh masjid</p>
        </div>
        <a href="{{ route('masjids.index') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
            🕌 Kelola Masjid
        </a>
    </div>

    <!-- Platform Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-xl">🕌</div>
                <div>
                    <p class="text-xs text-gray-500">Total Masjid</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['total_masjids'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-xl">📦</div>
                <div>
                    <p class="text-xs text-gray-500">Total Inventaris</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['total_items'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-xl">👥</div>
                <div>
                    <p class="text-xs text-gray-500">Total Pengguna</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center text-xl">📤</div>
                <div>
                    <p class="text-xs text-gray-500">Peminjaman Aktif</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['active_loans'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-xs text-gray-500">Masjid Aktif</p>
            <p class="text-xl font-bold text-green-600">{{ $stats['active_masjids'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-xs text-gray-500">Total Stok</p>
            <p class="text-xl font-bold text-blue-600">{{ number_format($stats['total_quantity']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-xs text-gray-500">Total Peminjaman</p>
            <p class="text-xl font-bold text-indigo-600">{{ $stats['total_loans'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-xs text-gray-500">Terlambat</p>
            <p class="text-xl font-bold {{ $stats['overdue_loans'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $stats['overdue_loans'] }}</p>
        </div>
    </div>

    <!-- Masjid List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">🕌 Daftar Masjid</h2>
            <a href="{{ route('masjids.index') }}" class="text-sm text-green-600 hover:text-green-700">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Masjid</th>
                        <th class="px-6 py-3 text-center">Pengguna</th>
                        <th class="px-6 py-3 text-center">Inventaris</th>
                        <th class="px-6 py-3 text-center">Pinjaman Aktif</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($masjids as $masjid)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-800">{{ $masjid->name }}</p>
                                <p class="text-xs text-gray-500">{{ $masjid->city }}, {{ $masjid->province }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $masjid->users_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $masjid->items_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $masjid->active_loans_count > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $masjid->active_loans_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('masjids.show', $masjid) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                                <form action="{{ route('masjids.switch') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="masjid_id" value="{{ $masjid->id }}">
                                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm">Masuk →</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada masjid terdaftar</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
