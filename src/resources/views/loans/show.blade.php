@extends('layouts.app')

@section('title', 'Detail Peminjaman - Inventory Masjid')

@section('content')
<div class="mb-6">
    <x-breadcrumb :items="[['label' => 'Peminjaman', 'url' => route('loans.index')], ['label' => 'Detail Peminjaman']]" />
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-4 md:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-6">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">📤 Detail Peminjaman</h1>
                    <p class="text-sm text-gray-500 mt-1">ID: #{{ $loan->id }}</p>
                </div>
                <span class="self-start px-3 py-1.5 text-sm font-semibold rounded-full bg-{{ $loan->status_color }}-100 text-{{ $loan->status_color }}-800">
                    {{ $loan->status }}
                </span>
            </div>

            <!-- Borrower Info -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">👤 Informasi Peminjam</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Nama Peminjam</dt>
                        <dd class="font-medium text-gray-900">{{ $loan->borrower_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">No. Telepon</dt>
                        <dd class="font-medium text-gray-900">
                            @if($loan->borrower_phone)
                                <a href="tel:{{ $loan->borrower_phone }}" class="text-blue-600 hover:text-blue-800">{{ $loan->borrower_phone }}</a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Item Info -->
            <div class="mb-6 pt-4 border-t">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">📦 Informasi Barang</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Nama Barang</dt>
                        <dd class="font-medium text-gray-900">
                            <a href="{{ route('items.show', $loan->item) }}" class="text-blue-600 hover:text-blue-800">{{ $loan->item->name }}</a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Kategori</dt>
                        <dd class="font-medium text-gray-900">{{ $loan->item->category->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Lokasi</dt>
                        <dd class="font-medium text-gray-900">{{ $loan->item->location->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Jumlah Dipinjam</dt>
                        <dd class="font-medium text-gray-900 text-lg">{{ $loan->quantity }} {{ $loan->item->unit }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Date Info -->
            <div class="mb-6 pt-4 border-t">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">📅 Tanggal</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Tanggal Pinjam</dt>
                        <dd class="font-medium text-gray-900">{{ $loan->borrowed_at->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Jatuh Tempo</dt>
                        <dd class="font-medium {{ $loan->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">
                            @if($loan->due_at)
                                {{ $loan->due_at->format('d/m/Y') }}
                                @if($loan->isOverdue())
                                    <span class="block text-sm text-red-500">({{ $loan->due_at->diffForHumans() }})</span>
                                @endif
                            @else
                                <span class="text-gray-400">Tidak ditentukan</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Tanggal Kembali</dt>
                        <dd class="font-medium text-gray-900">
                            @if($loan->returned_at)
                                {{ $loan->returned_at->format('d/m/Y') }}
                            @else
                                <span class="text-yellow-600">Belum dikembalikan</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Return Condition (if returned) -->
            @if($loan->isReturned() && $loan->returned_condition)
            <div class="mb-6 pt-4 border-t">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">🔍 Kondisi Pengembalian</h2>
                <div>
                    @if($loan->returned_condition === 'baik')
                        <span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-green-100 text-green-800">Baik</span>
                    @elseif($loan->returned_condition === 'perlu_perbaikan')
                        <span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Perlu Perbaikan</span>
                    @else
                        <span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>
                    @endif
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($loan->notes)
            <div class="pt-4 border-t">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">📝 Catatan</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $loan->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">⚡ Aksi</h2>
            <div class="space-y-3">
                @if(!$loan->isReturned() && auth()->user()->canManageLoans())
                    <a href="{{ route('loans.return', $loan) }}" class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-semibold text-center">
                        ✅ Kembalikan Barang
                    </a>
                @endif

                @if(!$loan->isReturned())
                    <a href="{{ route('loans.qr.print', $loan) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-semibold text-center">
                        🏷️ Cetak QR Pengembalian
                    </a>
                @endif

                <a href="{{ route('items.show', $loan->item) }}" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold text-center">
                    📦 Lihat Barang
                </a>

                @if(auth()->user()->isAdmin())
                    <form action="{{ route('loans.destroy', $loan) }}" method="POST" onsubmit="return confirm('Yakin hapus data peminjaman ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg font-semibold">
                            🗑️ Hapus Peminjaman
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Return QR Code -->
        @if($loan->hasReturnQrCode() && !$loan->isReturned())
        <div class="bg-white rounded-lg shadow p-4 md:p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">📱 QR Pengembalian</h2>
            <div class="text-center">
                <div class="inline-block p-3 bg-white border border-gray-200 rounded-lg mb-3">
                    <img src="{{ route('loans.qr.svg', $loan) }}" alt="Return QR Code" class="w-48 h-48">
                </div>
                <p class="text-xs text-gray-500 font-mono">{{ $loan->return_qr_key }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
