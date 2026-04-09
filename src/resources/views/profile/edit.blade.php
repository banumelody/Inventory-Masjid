@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800">👤 Profil Saya</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Update Profile -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Profil</h2>
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('name') border-red-500 @enderror" required>
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('email') border-red-500 @enderror" required>
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <input type="text" value="{{ $user->role->name ?? '-' }}" class="w-full border border-gray-200 rounded-lg px-4 py-2 bg-gray-50 text-gray-500" disabled>
            </div>

            @if($user->masjid)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Masjid</label>
                <input type="text" value="{{ $user->masjid->name }}" class="w-full border border-gray-200 rounded-lg px-4 py-2 bg-gray-50 text-gray-500" disabled>
            </div>
            @endif

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                Simpan Perubahan
            </button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Ubah Password</h2>
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                <input type="password" name="current_password"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('current_password') border-red-500 @enderror" required>
                @error('current_password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('password') border-red-500 @enderror" required>
                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
            </div>

            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                Ubah Password
            </button>
        </form>
    </div>
</div>
@endsection
