@extends('layouts.app')

@section('title', 'Kelola Masjid')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">🕌 Kelola Masjid</h1>
            <p class="text-gray-500 mt-1">Daftar seluruh masjid yang menggunakan platform ini</p>
        </div>
        <a href="{{ route('masjids.create') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
            ➕ Tambah Masjid
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-xs text-gray-500">Total Masjid</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['total_masjids'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-xs text-gray-500">Masjid Aktif</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['active_masjids'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-xs text-gray-500">Total Inventaris</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['total_items'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-xs text-gray-500">Total Pengguna</p>
            <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_users'] }}</p>
        </div>
    </div>

    <!-- Masjid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($masjids as $masjid)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="p-5">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-800 truncate">{{ $masjid->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $masjid->city }}, {{ $masjid->province }}</p>
                        <p class="text-xs text-gray-400 mt-1 truncate">{{ $masjid->address }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $masjid->status === 'active' ? 'bg-green-100 text-green-800' : ($masjid->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ $masjid->status_label }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-3 mt-4 pt-4 border-t border-gray-100">
                    <div class="text-center">
                        <p class="text-lg font-bold text-blue-600">{{ $masjid->items_count }}</p>
                        <p class="text-xs text-gray-500">Inventaris</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-purple-600">{{ $masjid->users_count }}</p>
                        <p class="text-xs text-gray-500">Pengguna</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold {{ $masjid->active_loans_count > 0 ? 'text-yellow-600' : 'text-gray-400' }}">{{ $masjid->active_loans_count }}</p>
                        <p class="text-xs text-gray-500">Pinjaman</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('masjids.show', $masjid) }}" class="flex-1 text-center bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-2 rounded-lg text-sm transition">
                        Detail
                    </a>
                    <form action="{{ route('masjids.switch') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="masjid_id" value="{{ $masjid->id }}">
                        <button type="submit" class="w-full bg-green-50 text-green-600 hover:bg-green-100 px-3 py-2 rounded-lg text-sm transition">
                            Masuk →
                        </button>
                    </form>
                    <a href="{{ route('masjids.edit', $masjid) }}" class="bg-gray-50 text-gray-600 hover:bg-gray-100 px-3 py-2 rounded-lg text-sm transition">
                        ✏️
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-xl shadow-sm p-8 text-center border border-gray-100">
            <p class="text-gray-400 text-lg mb-2">🕌</p>
            <p class="text-gray-500">Belum ada masjid terdaftar</p>
            <a href="{{ route('masjids.create') }}" class="text-green-600 hover:text-green-700 text-sm mt-2 inline-block">Tambah masjid pertama →</a>
        </div>
        @endforelse
    </div>

    {{ $masjids->links() }}
</div>
@endsection
