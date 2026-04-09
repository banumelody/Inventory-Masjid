@extends('layouts.public')

@section('title', 'Daftarkan Masjid Anda')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">🕌 Daftarkan Masjid Anda</h1>
        <p class="text-gray-500 mt-2">Mulai kelola inventaris masjid dengan mudah dan gratis</p>
    </div>

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6 md:p-8">
        <form action="{{ route('register.store') }}" method="POST">
            @csrf

            <!-- Informasi Masjid -->
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🕌 Informasi Masjid</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Masjid <span class="text-red-500">*</span></label>
                    <input type="text" name="masjid_name" value="{{ old('masjid_name') }}" placeholder="Masjid Al-Ikhlas"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('masjid_name') border-red-500 @enderror" required>
                    @error('masjid_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="2" placeholder="Jl. Contoh No. 123"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('address') border-red-500 @enderror" required>{{ old('address') }}</textarea>
                    @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kota <span class="text-red-500">*</span></label>
                    <input type="text" name="city" value="{{ old('city') }}" placeholder="Jakarta"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('city') border-red-500 @enderror" required>
                    @error('city')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
                    <input type="text" name="province" value="{{ old('province') }}" placeholder="DKI Jakarta"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('province') border-red-500 @enderror" required>
                    @error('province')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telepon Masjid</label>
                    <input type="text" name="masjid_phone" value="{{ old('masjid_phone') }}" placeholder="021-1234567"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Masjid</label>
                    <input type="email" name="masjid_email" value="{{ old('masjid_email') }}" placeholder="info@masjid.com"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>

            <!-- Informasi Admin -->
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">👤 Akun Admin</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" placeholder="Ahmad Fauzi"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('admin_name') border-red-500 @enderror" required>
                    @error('admin_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="admin@email.com"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('admin_email') border-red-500 @enderror" required>
                    @error('admin_email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="admin_password"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('admin_password') border-red-500 @enderror" required>
                    @error('admin_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <input type="password" name="admin_password_confirmation"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2" required>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold text-center">
                    🚀 Daftarkan Masjid
                </button>
                <a href="{{ route('login') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold text-center">
                    Sudah punya akun? Login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
