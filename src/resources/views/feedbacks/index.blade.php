@extends('layouts.app')

@section('title', 'Daftar Feedback - Inventory Masjid')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">💬 Daftar Feedback</h1>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modul</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pesan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($feedbacks as $feedback)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $feedback->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $feedback->user?->name ?? 'Guest' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($feedback->module) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $feedback->type_label }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($feedback->message, 50) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $feedback->status_color }}-100 text-{{ $feedback->status_color }}-800">
                        {{ $feedback->status_label }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button onclick="openModal({{ $feedback->id }}, '{{ $feedback->status }}', '{{ addslashes($feedback->admin_notes) }}')" class="text-blue-600 hover:text-blue-900">Update</button>
                    <form action="{{ route('feedbacks.destroy', $feedback) }}" method="POST" class="inline" data-confirm="Yakin hapus?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada feedback.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $feedbacks->links() }}
</div>

<!-- Modal -->
<div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold mb-4">Update Status Feedback</h3>
        <form id="updateForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="modalStatus" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="new">Baru</option>
                    <option value="in_progress">Diproses</option>
                    <option value="resolved">Selesai</option>
                    <option value="closed">Ditutup</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin</label>
                <textarea name="admin_notes" id="modalNotes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">Batal</button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id, status, notes) {
    document.getElementById('updateForm').action = '/feedbacks/' + id;
    document.getElementById('modalStatus').value = status;
    document.getElementById('modalNotes').value = notes || '';
    document.getElementById('updateModal').classList.remove('hidden');
    document.getElementById('updateModal').classList.add('flex');
}
function closeModal() {
    document.getElementById('updateModal').classList.add('hidden');
    document.getElementById('updateModal').classList.remove('flex');
}
</script>
@endsection
