@extends('layouts.app')

@section('title', 'Export Data - Inventory Masjid')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Export Data Inventaris</h1>

<div class="bg-white rounded-lg shadow p-6">
    <form id="exportForm" class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori</label>
                <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-3">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Lokasi</label>
                <select name="location_id" class="w-full border border-gray-300 rounded-lg px-4 py-3">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Pilih Format Export</h3>
            <div class="flex space-x-4">
                <a href="#" onclick="exportData('excel')" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-4 rounded-lg font-semibold text-center">
                    📊 Export Excel (CSV)
                </a>
                <a href="#" onclick="exportData('pdf')" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-4 rounded-lg font-semibold text-center">
                    📄 Export PDF
                </a>
            </div>
        </div>
    </form>
</div>

<script>
function exportData(format) {
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    for (const [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }
    
    const url = format === 'excel' 
        ? '{{ route("export.excel") }}' 
        : '{{ route("export.pdf") }}';
    
    window.location.href = url + '?' + params.toString();
}
</script>
@endsection
