<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label QR Pengembalian - {{ $loan->item->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            @page { margin: 5mm; size: auto; }
        }
        .label-small { width: 50mm; height: 30mm; }
        .label-medium { width: 70mm; height: 40mm; }
    </style>
</head>
<body class="bg-gray-100 p-4">
    <!-- Print Controls -->
    <div class="no-print max-w-lg mx-auto mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center mb-4">
                <a href="{{ route('loans.show', $loan) }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali</a>
                <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                    🖨️ Cetak Label
                </button>
            </div>
            <p class="text-sm text-gray-600">Label ini dapat ditempelkan pada slip peminjaman atau diberikan kepada peminjam.</p>
        </div>
    </div>

    <!-- Label Preview -->
    <div class="flex flex-col items-center gap-6">
        <!-- Small Label -->
        <div class="bg-white p-3 rounded shadow label-small flex items-center gap-2 border-2 border-dashed border-gray-300">
            <img src="{{ route('loans.qr.svg', $loan) }}" alt="QR Code" class="w-16 h-16">
            <div class="flex-1 min-w-0">
                <div class="font-bold text-xs truncate">{{ $loan->item->name }}</div>
                <div class="text-xs text-gray-600 truncate">{{ $loan->borrower_name }}</div>
                <div class="text-xs text-gray-500">{{ $loan->borrowed_at->format('d/m/Y') }}</div>
            </div>
        </div>

        <!-- Medium Label -->
        <div class="bg-white p-4 rounded shadow label-medium border-2 border-dashed border-gray-300">
            <div class="flex items-start gap-3">
                <img src="{{ route('loans.qr.svg', $loan) }}" alt="QR Code" class="w-20 h-20">
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm mb-1">{{ $loan->item->name }}</div>
                    <div class="text-xs text-gray-600">
                        <div><strong>Peminjam:</strong> {{ $loan->borrower_name }}</div>
                        <div><strong>Jumlah:</strong> {{ $loan->quantity }} {{ $loan->item->unit }}</div>
                        <div><strong>Pinjam:</strong> {{ $loan->borrowed_at->format('d/m/Y') }}</div>
                        @if($loan->due_at)
                        <div><strong>Kembali:</strong> {{ $loan->due_at->format('d/m/Y') }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-2 text-center">
                <div class="text-xs text-gray-400">Scan untuk mengembalikan</div>
            </div>
        </div>
    </div>

    <!-- Print Version (hidden on screen) -->
    <div class="print-only hidden">
        <div class="flex flex-wrap gap-2 p-2">
            @for($i = 0; $i < 4; $i++)
            <div class="bg-white p-3 border label-small flex items-center gap-2">
                <img src="{{ route('loans.qr.svg', $loan) }}" alt="QR Code" class="w-16 h-16">
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-xs truncate">{{ $loan->item->name }}</div>
                    <div class="text-xs text-gray-600 truncate">{{ $loan->borrower_name }}</div>
                    <div class="text-xs text-gray-500">{{ $loan->borrowed_at->format('d/m/Y') }}</div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</body>
</html>
