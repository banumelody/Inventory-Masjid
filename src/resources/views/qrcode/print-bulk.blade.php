<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label QR Massal - {{ \App\Models\Setting::appName() }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
        
        .label-small {
            width: 60mm;
            height: 35mm;
            padding: 2mm;
        }
        
        .label-medium {
            width: 80mm;
            height: 45mm;
            padding: 3mm;
        }
        
        .qr-small svg {
            width: 24mm !important;
            height: 24mm !important;
        }
        
        .qr-medium svg {
            width: 34mm !important;
            height: 34mm !important;
        }
        
        .label-text {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .label-name {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.2;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Print Controls (hidden when printing) -->
    <div class="no-print bg-white shadow-lg p-4 mb-6 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('qrcode.bulk') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali</a>
                <span class="ml-4 text-gray-500">{{ $items->count() }} label</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">
                    Ukuran: <strong>{{ $size === 'small' ? 'Kecil (60×35mm)' : 'Sedang (80×45mm)' }}</strong>
                </span>
                <button onclick="window.print()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                    🖨️ Cetak Sekarang
                </button>
            </div>
        </div>
    </div>

    <!-- Labels Container -->
    <div class="max-w-4xl mx-auto px-4 pb-8">
        <div class="flex flex-wrap gap-3 justify-center">
            @foreach($items as $item)
            <div class="label-{{ $size }} bg-white border border-gray-300 rounded flex items-center gap-2 shadow-sm">
                <div class="qr-{{ $size }} flex-shrink-0">
                    {!! QrCode::size($size === 'small' ? 80 : 120)->errorCorrection('H')->generate($item->qr_code_url) !!}
                </div>
                <div class="flex-1 min-w-0 label-text">
                    <p class="font-bold {{ $size === 'small' ? 'text-xs' : 'text-sm' }} label-name">{{ $item->name }}</p>
                    <p class="text-gray-500 {{ $size === 'small' ? 'text-[8px]' : 'text-xs' }} font-mono mt-1">{{ $item->qr_code_key }}</p>
                    <p class="text-gray-400 {{ $size === 'small' ? 'text-[8px]' : 'text-[10px]' }}">{{ $item->location->name }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Print Tips -->
    <div class="no-print max-w-4xl mx-auto px-4 pb-8">
        <div class="bg-blue-50 rounded-lg p-4">
            <h3 class="font-semibold text-blue-800 mb-2">💡 Tips Cetak:</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Gunakan kertas stiker untuk hasil terbaik</li>
                <li>• Pastikan skala cetak 100% (tidak fit to page)</li>
                <li>• Preview dulu sebelum cetak</li>
                <li>• Untuk label banyak, gunakan kertas label A4</li>
            </ul>
        </div>
    </div>
</body>
</html>
