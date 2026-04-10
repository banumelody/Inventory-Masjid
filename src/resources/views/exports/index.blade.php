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

    // Show loading indicator
    const overlay = document.getElementById('export-loading');
    overlay.classList.remove('hidden');

    // Use a hidden iframe to detect download completion
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url + '?' + params.toString();
    document.body.appendChild(iframe);

    // Hide overlay after timeout (download should have started)
    setTimeout(function() {
        overlay.classList.add('hidden');
        document.body.removeChild(iframe);
    }, 5000);
}
</script>

{{-- Export loading overlay --}}
<div id="export-loading" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-xl shadow-2xl p-8 flex flex-col items-center gap-4">
        <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        <p class="text-gray-700 font-medium">Sedang menyiapkan export...</p>
    </div>
</div>
@endsection
