@extends('layouts.app')

@section('title', 'Detail Maintenance - Inventory Masjid')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('maintenances.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali ke Daftar</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $maintenance->item->name }}</h1>
                <p class="text-gray-500">{{ $maintenance->item->category->name }} - {{ $maintenance->item->location->name }}</p>
            </div>
            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-{{ $maintenance->status_color }}-100 text-{{ $maintenance->status_color }}-800">
                {{ $maintenance->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Tipe Maintenance</h3>
                <p class="text-gray-900">{{ $maintenance->type_label }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Dibuat oleh</h3>
                <p class="text-gray-900">{{ $maintenance->user?->name ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500 mb-1">Deskripsi</h3>
                <p class="text-gray-900 whitespace-pre-line">{{ $maintenance->description }}</p>
            </div>
        </div>

        @if($maintenance->vendor || $maintenance->cost)
        <div class="border-t mt-6 pt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Info Vendor & Biaya</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Vendor/Teknisi</h3>
                    <p class="text-gray-900">{{ $maintenance->vendor ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Telepon Vendor</h3>
                    <p class="text-gray-900">
                        @if($maintenance->vendor_phone)
                        <a href="tel:{{ $maintenance->vendor_phone }}" class="text-blue-600">{{ $maintenance->vendor_phone }}</a>
                        @else
                        -
                        @endif
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Biaya</h3>
                    <p class="text-gray-900 font-semibold">{{ $maintenance->cost ? 'Rp ' . number_format($maintenance->cost, 0, ',', '.') : '-' }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="border-t mt-6 pt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Timeline</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Tanggal Mulai</h3>
                    <p class="text-gray-900">{{ $maintenance->started_at?->format('d/m/Y') ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Estimasi Selesai</h3>
                    <p class="text-gray-900">{{ $maintenance->estimated_completion?->format('d/m/Y') ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Tanggal Selesai</h3>
                    <p class="text-gray-900 {{ $maintenance->completed_at ? 'text-green-600 font-semibold' : '' }}">
                        {{ $maintenance->completed_at?->format('d/m/Y') ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        @if($maintenance->notes)
        <div class="border-t mt-6 pt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Catatan</h2>
            <p class="text-gray-600 whitespace-pre-line">{{ $maintenance->notes }}</p>
        </div>
        @endif

        <!-- Photo Documentation Section -->
        <div class="border-t mt-6 pt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">📷 Dokumentasi Foto</h2>
                @if(auth()->user()->canEditItems() && !$maintenance->isCompleted() && !$maintenance->isCancelled())
                <button onclick="document.getElementById('upload-photo-modal').classList.remove('hidden')" 
                    class="text-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg">
                    + Tambah Foto
                </button>
                @endif
            </div>

            @if($maintenance->photos->count() > 0)
                @foreach(['before' => 'Sebelum', 'progress' => 'Proses', 'after' => 'Sesudah'] as $type => $label)
                    @php $typePhotos = $maintenance->photos->where('type', $type); @endphp
                    @if($typePhotos->count() > 0)
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-600 mb-2 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-{{ $type === 'before' ? 'red' : ($type === 'progress' ? 'yellow' : 'green') }}-500"></span>
                            {{ $label }}
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($typePhotos as $photo)
                            <div class="relative group">
                                <img src="{{ $photo->url }}" alt="{{ $photo->caption ?? $photo->original_name }}" 
                                    class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-90 transition"
                                    onclick="openLightbox('{{ $photo->url }}', '{{ $photo->caption ?? $photo->original_name }}')">
                                @if($photo->caption)
                                <p class="text-xs text-gray-500 mt-1 truncate">{{ $photo->caption }}</p>
                                @endif
                                @if(auth()->user()->canEditItems())
                                <button onclick="deletePhoto({{ $photo->id }})" 
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 text-xs opacity-0 group-hover:opacity-100 transition">
                                    ✕
                                </button>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            @else
            <p class="text-gray-500 text-sm">Belum ada foto dokumentasi.</p>
            @endif
        </div>

        <!-- Quick Status Update -->
        @if(!$maintenance->isCompleted() && !$maintenance->isCancelled() && auth()->user()->canEditItems())
        <div class="border-t mt-6 pt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Update Status</h2>
            <div class="flex flex-wrap gap-2">
                @if($maintenance->isPending())
                <form action="{{ route('maintenances.status', $maintenance) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="in_progress">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        🔄 Mulai Pengerjaan
                    </button>
                </form>
                @endif
                @if($maintenance->isInProgress())
                <form action="{{ route('maintenances.status', $maintenance) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        ✅ Selesai
                    </button>
                </form>
                @endif
                <form action="{{ route('maintenances.status', $maintenance) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg" onclick="return confirm('Yakin batalkan maintenance ini?')">
                        ❌ Batalkan
                    </button>
                </form>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="border-t mt-6 pt-6 flex justify-between">
            <a href="{{ route('items.show', $maintenance->item) }}" class="text-blue-600 hover:text-blue-800">
                Lihat Barang →
            </a>
            @if(!$maintenance->isCompleted() && !$maintenance->isCancelled() && auth()->user()->canEditItems())
            <a href="{{ route('maintenances.edit', $maintenance) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                ✏️ Edit
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Upload Photo Modal -->
@if(auth()->user()->canEditItems())
<div id="upload-photo-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Upload Foto</h3>
            <button onclick="document.getElementById('upload-photo-modal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <form id="upload-photo-form" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                    <input type="file" name="photo" id="photo-input" accept="image/*" required
                        class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="type" id="photo-type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        @foreach($photoTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (opsional)</label>
                    <input type="text" name="caption" id="photo-caption" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Keterangan foto">
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="document.getElementById('upload-photo-modal').classList.add('hidden')" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">Batal</button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Upload</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Lightbox -->
<div id="lightbox" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4" onclick="closeLightbox()">
    <button class="absolute top-4 right-4 text-white text-3xl" onclick="closeLightbox()">✕</button>
    <img id="lightbox-img" src="" alt="" class="max-w-full max-h-[90vh] object-contain">
    <p id="lightbox-caption" class="absolute bottom-4 left-0 right-0 text-center text-white"></p>
</div>

<script>
function openLightbox(url, caption) {
    document.getElementById('lightbox-img').src = url;
    document.getElementById('lightbox-caption').textContent = caption;
    document.getElementById('lightbox').classList.remove('hidden');
}

function closeLightbox() {
    document.getElementById('lightbox').classList.add('hidden');
}

@if(auth()->user()->canEditItems())
document.getElementById('upload-photo-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('photo', document.getElementById('photo-input').files[0]);
    formData.append('type', document.getElementById('photo-type').value);
    formData.append('caption', document.getElementById('photo-caption').value);
    
    try {
        const response = await fetch('{{ route("maintenances.photos.upload", $maintenance) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            location.reload();
        } else {
            alert('Gagal upload foto');
        }
    } catch (error) {
        alert('Terjadi kesalahan');
    }
});

async function deletePhoto(photoId) {
    if (!confirm('Yakin hapus foto ini?')) return;
    
    try {
        const response = await fetch(`/maintenance-photos/${photoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
        
        const result = await response.json();
        if (result.success) {
            location.reload();
        } else {
            alert('Gagal menghapus foto');
        }
    } catch (error) {
        alert('Terjadi kesalahan');
    }
}
@endif
</script>
@endsection
