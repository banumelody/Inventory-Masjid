@extends('layouts.app')

@section('title', 'Backup Database - Inventory Masjid')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Backup Database</h1>
    <form action="{{ route('backups.create') }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold" data-confirm="Buat backup sekarang?">
            + Buat Backup
        </button>
    </form>
</div>

<div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-6">
    <p class="text-sm">
        <strong>Info:</strong> Backup otomatis dijalankan setiap hari. File backup disimpan selama 30 hari.
    </p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama File</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ukuran</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($backups as $backup)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $backup->filename }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $backup->size_formatted }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $backup->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <a href="{{ route('backups.download', $backup) }}" class="text-blue-600 hover:text-blue-900">Download</a>
                    <form action="{{ route('backups.destroy', $backup) }}" method="POST" class="inline" data-confirm="Yakin hapus backup ini?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada backup.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $backups->links() }}
</div>
@endsection
