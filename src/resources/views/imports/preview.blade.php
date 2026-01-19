@extends('layouts.app')

@section('title', 'Preview Import - Inventory Masjid')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('imports.create') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">📋 Preview Data Import</h1>
        <p class="text-gray-500 mb-6">Periksa data sebelum diimport. Total: {{ count($data) }} baris.</p>

        <form action="{{ route('imports.process') }}" method="POST">
            @csrf

            <!-- Column Mapping -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h2 class="font-semibold text-gray-700 mb-4">Mapping Kolom</h2>
                <p class="text-sm text-gray-500 mb-4">Cocokkan kolom file dengan field yang dibutuhkan:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($expectedColumns as $field => $config)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ ucfirst($field) }}
                            @if($config['required'])
                            <span class="text-red-500">*</span>
                            @endif
                        </label>
                        <select name="column_mapping[{{ $field }}]" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            {{ $config['required'] ? 'required' : '' }}>
                            <option value="">-- Pilih Kolom --</option>
                            @foreach($headers as $header)
                            <option value="{{ $header }}" 
                                {{ in_array($header, $config['aliases']) || $header === $field ? 'selected' : '' }}>
                                {{ $header }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Data Preview -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            @foreach($headers as $header)
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach(array_slice($data, 0, 10) as $index => $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-gray-400">{{ $index + 1 }}</td>
                            @foreach($headers as $header)
                            <td class="px-3 py-2 text-gray-900">{{ $row[$header] ?? '' }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                        @if(count($data) > 10)
                        <tr>
                            <td colspan="{{ count($headers) + 1 }}" class="px-3 py-2 text-center text-gray-500 italic">
                                ... dan {{ count($data) - 10 }} baris lainnya
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('imports.create') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">Batal</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                    Import {{ count($data) }} Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
