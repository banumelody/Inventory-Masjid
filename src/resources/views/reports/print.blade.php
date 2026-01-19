<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Inventaris - {{ \App\Models\Setting::appName() }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { 
                print-color-adjust: exact; 
                -webkit-print-color-adjust: exact;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-white p-8">
    <div class="no-print mb-4">
        <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
            🖨️ Cetak
        </button>
        <a href="{{ route('reports.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold ml-2">
            ← Kembali
        </a>
    </div>

    <div class="text-center mb-8">
        @if(\App\Models\Setting::hasLogo())
            <img src="{{ \App\Models\Setting::logoUrl() }}" alt="{{ \App\Models\Setting::appName() }}" class="h-12 mx-auto mb-2">
        @else
            <span class="text-4xl">🕌</span>
        @endif
        <h1 class="text-2xl font-bold">LAPORAN INVENTARIS</h1>
        <h2 class="text-lg font-semibold text-gray-700">{{ \App\Models\Setting::orgName() ?: \App\Models\Setting::appName() }}</h2>
        <p class="text-gray-600 mt-2">Tanggal: {{ now()->format('d/m/Y') }}</p>
        <p class="text-gray-600">Kategori: {{ $categoryName }} | Lokasi: {{ $locationName }}</p>
    </div>

    <table class="min-w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">No</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">Nama Barang</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">Kategori</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">Lokasi</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">Jumlah</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">Kondisi</th>
                <th class="border border-gray-300 px-4 py-2 text-left text-sm font-semibold">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            <tr>
                <td class="border border-gray-300 px-4 py-2 text-sm">{{ $index + 1 }}</td>
                <td class="border border-gray-300 px-4 py-2 text-sm font-medium">{{ $item->name }}</td>
                <td class="border border-gray-300 px-4 py-2 text-sm">{{ $item->category->name }}</td>
                <td class="border border-gray-300 px-4 py-2 text-sm">{{ $item->location->name }}</td>
                <td class="border border-gray-300 px-4 py-2 text-sm">{{ $item->quantity }} {{ $item->unit }}</td>
                <td class="border border-gray-300 px-4 py-2 text-sm">
                    @if($item->condition == 'baik')
                        Baik
                    @elseif($item->condition == 'perlu_perbaikan')
                        Perlu Perbaikan
                    @else
                        Rusak
                    @endif
                </td>
                <td class="border border-gray-300 px-4 py-2 text-sm">{{ $item->note ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="border border-gray-300 px-4 py-4 text-center text-gray-500">Belum ada barang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-8 text-sm text-gray-600">
        <p>Total Barang: {{ $items->count() }}</p>
        <p>Kondisi Baik: {{ $items->where('condition', 'baik')->count() }}</p>
        <p>Perlu Perbaikan: {{ $items->where('condition', 'perlu_perbaikan')->count() }}</p>
        <p>Rusak: {{ $items->where('condition', 'rusak')->count() }}</p>
    </div>

    <div class="mt-12 flex justify-end">
        <div class="text-center">
            <p class="mb-16">Mengetahui,</p>
            <p class="border-t border-gray-400 pt-2">Pengurus Masjid</p>
        </div>
    </div>
</body>
</html>
