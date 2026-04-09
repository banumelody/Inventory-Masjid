@extends('layouts.app')

@section('title', 'Edit ' . $masjid->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('masjids.index') }}" class="hover:text-green-600">Kelola Masjid</a>
        <span>→</span>
        <a href="{{ route('masjids.show', $masjid) }}" class="hover:text-green-600">{{ $masjid->name }}</a>
        <span>→</span>
        <span class="text-gray-700">Edit</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h1 class="text-xl font-bold text-gray-800 mb-6">✏️ Edit Masjid</h1>

        <form action="{{ route('masjids.update', $masjid) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Masjid <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $masjid->name) }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $masjid->slug) }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                @error('slug') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-500">*</span></label>
                <textarea name="address" id="address" rows="2" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">{{ old('address', $masjid->address) }}</textarea>
                @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota <span class="text-red-500">*</span></label>
                    <input type="text" name="city" id="city" value="{{ old('city', $masjid->city) }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    @error('city') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
                    <input type="text" name="province" id="province" value="{{ old('province', $masjid->province) }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    @error('province') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $masjid->phone) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $masjid->email) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="active" {{ old('status', $masjid->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status', $masjid->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="suspended" {{ old('status', $masjid->status) === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                </select>
                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                    💾 Simpan Perubahan
                </button>
                <a href="{{ route('masjids.show', $masjid) }}" class="text-gray-500 hover:text-gray-700 text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
