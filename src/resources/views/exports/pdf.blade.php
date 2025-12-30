<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventaris Masjid</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { text-align: center; margin-bottom: 5px; }
        .header { text-align: center; margin-bottom: 20px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .footer { margin-top: 20px; font-size: 11px; }
        .signature { margin-top: 50px; text-align: right; }
        .signature-line { border-top: 1px solid #333; width: 200px; margin-left: auto; padding-top: 5px; }
    </style>
</head>
<body>
    <h1>🕌 LAPORAN INVENTARIS MASJID</h1>
    <div class="header">
        <p>Tanggal: {{ now()->format('d/m/Y') }}</p>
        <p>Kategori: {{ $categoryName }} | Lokasi: {{ $locationName }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th style="width: 60px;">Jumlah</th>
                <th style="width: 80px;">Kondisi</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name }}</td>
                <td>{{ $item->location->name }}</td>
                <td>{{ $item->quantity }} {{ $item->unit }}</td>
                <td>{{ $item->condition_label }}</td>
                <td>{{ $item->note ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Belum ada data barang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total Barang: {{ $items->count() }}</p>
        <p>Kondisi Baik: {{ $items->where('condition', 'baik')->count() }} | 
           Perlu Perbaikan: {{ $items->where('condition', 'perlu_perbaikan')->count() }} | 
           Rusak: {{ $items->where('condition', 'rusak')->count() }}</p>
    </div>

    <div class="signature">
        <p>Mengetahui,</p>
        <br><br><br>
        <div class="signature-line">Pengurus Masjid</div>
    </div>
</body>
</html>
