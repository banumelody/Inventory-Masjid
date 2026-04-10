@extends('layouts.app')

@section('title', __('ui.add_item_title') . ' - Inventory Masjid')

@section('content')
<div class="max-w-2xl mx-auto">
    <x-breadcrumb :items="[['label' => __('ui.items'), 'url' => route('items.index')], ['label' => __('ui.add_item_title')]]" />
    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">📦 {{ __('ui.add_item_title') }}</h1>

    <div class="bg-white rounded-lg shadow p-4 md:p-6">
        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" data-draft="item_create">
            @csrf
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('ui.item_name') }} *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                    placeholder="{{ __('ui.search_placeholder') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base focus:ring-green-500 focus:border-green-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('ui.category') }} *</label>
                    <select name="category_id" id="category_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base focus:ring-green-500 focus:border-green-500">
                        <option value="">{{ __('ui.select_category') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('ui.location') }} *</label>
                    <select name="location_id" id="location_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base focus:ring-green-500 focus:border-green-500">
                        <option value="">{{ __('ui.select_location') }}</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Jumlah, Satuan, Kondisi -->
            <div class="grid grid-cols-3 gap-3 md:gap-4 mb-4">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">{{ __('ui.quantity') }} *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="0" required
                        inputmode="numeric"
                        class="w-full border border-gray-300 rounded-lg px-3 md:px-4 py-3 text-base focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">{{ __('ui.unit') }} *</label>
                    <select name="unit" id="unit" required
                        class="w-full border border-gray-300 rounded-lg px-2 md:px-4 py-3 text-base focus:ring-green-500 focus:border-green-500">
                        <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>pcs</option>
                        <option value="unit" {{ old('unit') == 'unit' ? 'selected' : '' }}>unit</option>
                        <option value="set" {{ old('unit') == 'set' ? 'selected' : '' }}>set</option>
                        <option value="buah" {{ old('unit') == 'buah' ? 'selected' : '' }}>buah</option>
                        <option value="lembar" {{ old('unit') == 'lembar' ? 'selected' : '' }}>lembar</option>
                        <option value="roll" {{ old('unit') == 'roll' ? 'selected' : '' }}>roll</option>
                    </select>
                </div>
                <div>
                    <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">{{ __('ui.condition') }} *</label>
                    <select name="condition" id="condition" required
                        class="w-full border border-gray-300 rounded-lg px-2 md:px-4 py-3 text-base focus:ring-green-500 focus:border-green-500">
                        <option value="baik" {{ old('condition', 'baik') == 'baik' ? 'selected' : '' }}>{{ __('ui.condition_good') }}</option>
                        <option value="perlu_perbaikan" {{ old('condition') == 'perlu_perbaikan' ? 'selected' : '' }}>{{ __('ui.condition_repair') }}</option>
                        <option value="rusak" {{ old('condition') == 'rusak' ? 'selected' : '' }}>{{ __('ui.condition_broken') }}</option>
                    </select>
                </div>
            </div>

            <!-- Foto -->
            <div class="mb-4">
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">{{ __('ui.photo') }}</label>
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base focus:ring-green-500 focus:border-green-500">
                <p class="text-xs text-gray-500 mt-1">JPG/PNG, maks 5MB. Auto resize & kompresi.</p>
            </div>

            <!-- Catatan -->
            <div class="mb-6">
                <label for="note" class="block text-sm font-medium text-gray-700 mb-2">{{ __('ui.notes') }}</label>
                <textarea name="note" id="note" rows="2"
                    placeholder="Warna, merek, kondisi khusus, dll."
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base focus:ring-green-500 focus:border-green-500">{{ old('note') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                <a href="{{ route('items.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold text-center touch-target order-3 sm:order-1">
                    {{ __('ui.cancel') }}
                </a>
                <button type="submit" name="action" value="save_and_new" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold touch-target order-2">
                    💾 {{ __('ui.save_and_add') }}
                </button>
                <button type="submit" name="action" value="save" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold touch-target order-1 sm:order-3">
                    ✅ {{ __('ui.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
