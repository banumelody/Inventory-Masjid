@extends('layouts.app')

@section('title', 'Kirim Feedback - Inventory Masjid')

@section('content')
<div class="max-w-xl mx-auto">
    <x-breadcrumb :items="[['label' => 'Feedback', 'url' => route('feedbacks.index')], ['label' => 'Kirim Feedback']]" />
    <h1 class="text-2xl font-bold text-gray-800 mb-6">💬 Kirim Feedback</h1>

    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-6">
        <p class="text-sm">
            Bantu kami meningkatkan aplikasi ini! Sampaikan masalah, saran, atau pertanyaan Anda.
        </p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('feedbacks.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="module" class="block text-sm font-medium text-gray-700 mb-2">Modul / Halaman *</label>
                <select name="module" id="module" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    <option value="">Pilih Modul</option>
                    <option value="inventaris" {{ old('module') == 'inventaris' ? 'selected' : '' }}>Inventaris</option>
                    <option value="peminjaman" {{ old('module') == 'peminjaman' ? 'selected' : '' }}>Peminjaman</option>
                    <option value="mutasi" {{ old('module') == 'mutasi' ? 'selected' : '' }}>Mutasi Stok</option>
                    <option value="kategori" {{ old('module') == 'kategori' ? 'selected' : '' }}>Kategori</option>
                    <option value="lokasi" {{ old('module') == 'lokasi' ? 'selected' : '' }}>Lokasi</option>
                    <option value="laporan" {{ old('module') == 'laporan' ? 'selected' : '' }}>Laporan / Export</option>
                    <option value="pengguna" {{ old('module') == 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                    <option value="backup" {{ old('module') == 'backup' ? 'selected' : '' }}>Backup</option>
                    <option value="umum" {{ old('module') == 'umum' ? 'selected' : '' }}>Umum / Lainnya</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Feedback *</label>
                <select name="type" id="type" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    <option value="bug" {{ old('type') == 'bug' ? 'selected' : '' }}>🐛 Bug / Masalah</option>
                    <option value="suggestion" {{ old('type') == 'suggestion' ? 'selected' : '' }}>💡 Saran</option>
                    <option value="question" {{ old('type') == 'question' ? 'selected' : '' }}>❓ Pertanyaan</option>
                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>📝 Lainnya</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Pesan *</label>
                <textarea name="message" id="message" rows="5" required
                    placeholder="Jelaskan feedback Anda di sini..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-green-500 focus:border-green-500">{{ old('message') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">Batal</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">Kirim Feedback</button>
            </div>
        </form>
    </div>
</div>
@endsection
