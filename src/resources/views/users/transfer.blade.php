@extends('layouts.app')

@section('title', 'Transfer Pengguna')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">🔄 Transfer Pengguna</h1>
    <a href="{{ route('users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-semibold text-center">← Kembali</a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-lg">
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm text-blue-800">
            <strong>Pengguna:</strong> {{ $user->name }} ({{ $user->email }})<br>
            <strong>Role:</strong> {{ $user->role->display_name }}<br>
            <strong>Masjid Saat Ini:</strong> {{ $user->masjid?->name ?? 'Tidak ada' }}
        </p>
    </div>

    <form action="{{ route('users.transfer.process', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Masjid Tujuan</label>
            <select name="masjid_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('masjid_id') border-red-500 @enderror" required>
                <option value="">-- Pilih Masjid --</option>
                @foreach($masjids as $masjid)
                <option value="{{ $masjid->id }}" {{ $user->masjid_id == $masjid->id ? 'disabled' : '' }}>
                    {{ $masjid->name }} — {{ $masjid->city }}
                    @if($user->masjid_id == $masjid->id) (Saat Ini) @endif
                </option>
                @endforeach
            </select>
            @error('masjid_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold"
                onclick="return confirm('Yakin ingin memindahkan pengguna ini ke masjid lain?')">
                Transfer Pengguna
            </button>
            <a href="{{ route('users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold">Batal</a>
        </div>
    </form>
</div>
@endsection
