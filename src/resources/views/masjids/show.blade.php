@extends('layouts.app')

@section('title', $masjid->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('masjids.index') }}" class="hover:text-green-600">Kelola Masjid</a>
                <span>→</span>
                <span class="text-gray-700">{{ $masjid->name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">🕌 {{ $masjid->name }}</h1>
            <p class="text-gray-500 mt-1">{{ $masjid->address }}, {{ $masjid->city }}, {{ $masjid->province }}</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('masjids.switch') }}" method="POST">
                @csrf
                <input type="hidden" name="masjid_id" value="{{ $masjid->id }}">
                <button type="submit" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                    🔀 Masuk ke Masjid Ini
                </button>
            </form>
            <a href="{{ route('masjids.edit', $masjid) }}" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm">
                ✏️ Edit
            </a>
            <form action="{{ route('masjids.destroy', $masjid) }}" method="POST" class="inline" data-confirm="PERINGATAN: Semua data masjid (item, peminjaman, user, dll) akan dihapus permanen. Lanjutkan?">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 transition text-sm">
                    🗑️ Hapus Masjid
                </button>
            </form>
        </div>
    </div>

    <!-- Info & Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Masjid Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-3">Informasi Masjid</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Status</dt>
                    <dd>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $masjid->status === 'active' ? 'bg-green-100 text-green-800' : ($masjid->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $masjid->status_label }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Kota</dt>
                    <dd class="text-gray-700">{{ $masjid->city }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Provinsi</dt>
                    <dd class="text-gray-700">{{ $masjid->province }}</dd>
                </div>
                @if($masjid->phone)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Telepon</dt>
                    <dd class="text-gray-700">{{ $masjid->phone }}</dd>
                </div>
                @endif
                @if($masjid->email)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Email</dt>
                    <dd class="text-gray-700">{{ $masjid->email }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-500">Slug</dt>
                    <dd class="text-gray-700 font-mono text-xs">{{ $masjid->slug }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Terdaftar</dt>
                    <dd class="text-gray-700">{{ $masjid->created_at->format('d M Y') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Stats Cards -->
        <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-3 gap-3">
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $masjid->items_count }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Inventaris</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-purple-600">{{ $masjid->users_count }}</p>
                <p class="text-xs text-gray-500 mt-1">Pengguna</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $masjid->categories_count }}</p>
                <p class="text-xs text-gray-500 mt-1">Kategori</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-indigo-600">{{ $masjid->locations_count }}</p>
                <p class="text-xs text-gray-500 mt-1">Lokasi</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-yellow-600">{{ $masjid->active_loans_count }}</p>
                <p class="text-xs text-gray-500 mt-1">Pinjaman Aktif</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold {{ $masjid->overdue_loans_count > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $masjid->overdue_loans_count }}</p>
                <p class="text-xs text-gray-500 mt-1">Terlambat</p>
            </div>
        </div>
    </div>

    <!-- Users -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">👥 Pengguna Masjid</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Nama</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-center">Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $user->role->name === 'admin' ? 'bg-red-100 text-red-800' : ($user->role->name === 'operator' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $user->role->display_name }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-6 py-4 text-center text-gray-400">Belum ada pengguna</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Items -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">📦 Inventaris Terbaru</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Nama Barang</th>
                        <th class="px-6 py-3 text-left">Kategori</th>
                        <th class="px-6 py-3 text-left">Lokasi</th>
                        <th class="px-6 py-3 text-center">Jumlah</th>
                        <th class="px-6 py-3 text-center">Kondisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentItems as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $item->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $item->category->name ?? '-' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $item->location->name ?? '-' }}</td>
                        <td class="px-6 py-3 text-center">{{ $item->quantity }} {{ $item->unit }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $item->condition === 'baik' ? 'bg-green-100 text-green-800' : ($item->condition === 'perlu_perbaikan' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $item->condition_label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-400">Belum ada inventaris</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Loans -->
    @if($activeLoans->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">📤 Peminjaman Aktif</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Barang</th>
                        <th class="px-6 py-3 text-left">Peminjam</th>
                        <th class="px-6 py-3 text-center">Jumlah</th>
                        <th class="px-6 py-3 text-center">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($activeLoans as $loan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $loan->item->name ?? '-' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $loan->borrower_name }}</td>
                        <td class="px-6 py-3 text-center">{{ $loan->quantity }}</td>
                        <td class="px-6 py-3 text-center text-sm">{{ $loan->due_at?->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $loan->status_color }}-100 text-{{ $loan->status_color }}-800">
                                {{ $loan->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
