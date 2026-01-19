<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label QR - {{ $item->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: auto;
                margin: 10mm;
            }
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
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
            hyphens: auto;
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
                <a href="{{ route('qrcode.preview', $item) }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali</a>
            </div>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Ukuran:</span>
                    <select id="size-select" class="border border-gray-300 rounded px-3 py-1">
                        <option value="small" selected>Kecil (60×35mm)</option>
                        <option value="medium">Sedang (80×45mm)</option>
                    </select>
                </label>
                <label class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Jumlah:</span>
                    <input type="number" id="copies" value="1" min="1" max="20" 
                           class="border border-gray-300 rounded px-3 py-1 w-16">
                </label>
                <button onclick="window.print()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                    🖨️ Cetak
                </button>
            </div>
        </div>
    </div>

    <!-- Labels Container -->
    <div class="max-w-4xl mx-auto px-4 pb-8">
        <div id="labels-container" class="flex flex-wrap gap-4 justify-center">
            <!-- Labels will be rendered here -->
        </div>
    </div>

    <!-- Hidden QR SVG template -->
    <div id="qr-svg-template" class="hidden">
        {!! QrCode::size(100)->errorCorrection('H')->generate($item->qr_code_url) !!}
    </div>

    <script>
        const item = {
            name: @json($item->name),
            qrKey: @json($item->qr_code_key),
            location: @json($item->location->name),
        };

        const qrSvg = document.getElementById('qr-svg-template').innerHTML;

        function createLabel(size) {
            const isSmall = size === 'small';
            const labelDiv = document.createElement('div');
            labelDiv.className = `label-${size} bg-white border border-gray-300 rounded flex items-center gap-2 shadow-sm`;
            
            labelDiv.innerHTML = `
                <div class="qr-${size} flex-shrink-0">
                    ${qrSvg}
                </div>
                <div class="flex-1 min-w-0 label-text">
                    <p class="font-bold ${isSmall ? 'text-xs' : 'text-sm'} label-name">${item.name}</p>
                    <p class="text-gray-500 ${isSmall ? 'text-[8px]' : 'text-xs'} font-mono mt-1">${item.qrKey}</p>
                    <p class="text-gray-400 ${isSmall ? 'text-[8px]' : 'text-[10px]'}">${item.location}</p>
                </div>
            `;
            
            return labelDiv;
        }

        function renderLabels() {
            const container = document.getElementById('labels-container');
            const size = document.getElementById('size-select').value;
            const copies = parseInt(document.getElementById('copies').value) || 1;
            
            container.innerHTML = '';
            for (let i = 0; i < copies; i++) {
                container.appendChild(createLabel(size));
            }
        }

        document.getElementById('size-select').addEventListener('change', renderLabels);
        document.getElementById('copies').addEventListener('change', renderLabels);
        document.getElementById('copies').addEventListener('input', renderLabels);

        // Initial render
        document.addEventListener('DOMContentLoaded', renderLabels);
    </script>
</body>
</html>
