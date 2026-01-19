@extends('layouts.app')

@section('title', 'Scan Pengembalian - Inventory Masjid')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('loans.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali ke Daftar Peminjaman</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">📷 Scan Pengembalian</h1>
        
        <p class="text-gray-600 text-center mb-6">Scan QR Code pada slip peminjaman untuk mengembalikan barang</p>

        <!-- Camera View -->
        <div id="scanner-container" class="relative mb-4">
            <video id="scanner-video" class="w-full rounded-lg bg-gray-900" autoplay playsinline></video>
            <div id="scanner-overlay" class="absolute inset-0 flex items-center justify-center">
                <div class="border-2 border-orange-500 w-48 h-48 rounded-lg opacity-50"></div>
            </div>
        </div>

        <!-- Status Messages -->
        <div id="scanner-status" class="text-center mb-4">
            <p class="text-gray-500">Memuat kamera...</p>
        </div>

        <!-- Scan Result (hidden by default) -->
        <div id="scan-result" class="hidden bg-green-50 border border-green-300 rounded-lg p-4 mb-4">
            <h3 class="font-semibold text-green-800 mb-2">✓ QR Code Terdeteksi</h3>
            <div id="result-content"></div>
            <div class="mt-4 flex gap-2">
                <button id="btn-quick-return" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-semibold">
                    ✓ Kembalikan Sekarang
                </button>
                <button id="btn-detail-return" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-semibold">
                    📝 Isi Detail
                </button>
            </div>
        </div>

        <!-- Error Display -->
        <div id="scanner-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
        </div>

        <!-- Manual Input Fallback -->
        <div class="border-t pt-4 mt-4">
            <p class="text-sm text-gray-500 text-center mb-3">Kamera tidak bisa? Masukkan kode manual:</p>
            <form action="" method="GET" id="manual-form" class="flex gap-2">
                <input type="text" 
                       id="manual-code" 
                       name="code" 
                       placeholder="Masukkan kode QR"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <button type="submit" 
                        class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
                    Cari
                </button>
            </form>
        </div>
    </div>

    <!-- Instructions -->
    <div class="mt-6 bg-orange-50 rounded-lg p-4">
        <h3 class="font-semibold text-orange-800 mb-2">💡 Cara Penggunaan:</h3>
        <ul class="text-sm text-orange-700 space-y-1">
            <li>• Scan QR Code pada slip/label peminjaman</li>
            <li>• Pilih "Kembalikan Sekarang" untuk pengembalian cepat (kondisi baik)</li>
            <li>• Pilih "Isi Detail" jika kondisi barang berubah</li>
        </ul>
    </div>

    <!-- Active Loans Quick List -->
    <div class="mt-6 bg-white rounded-lg shadow p-4">
        <h3 class="font-semibold text-gray-800 mb-3">📋 Peminjaman Aktif</h3>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            @php
                $activeLoans = \App\Models\Loan::with('item')
                    ->whereNull('returned_at')
                    ->orderBy('due_at')
                    ->limit(10)
                    ->get();
            @endphp
            @forelse($activeLoans as $loan)
            <a href="{{ route('loans.return', $loan) }}" class="block p-3 rounded-lg border hover:bg-gray-50 {{ $loan->isOverdue() ? 'border-red-300 bg-red-50' : 'border-gray-200' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-medium">{{ $loan->item->name }}</div>
                        <div class="text-sm text-gray-500">{{ $loan->borrower_name }} - {{ $loan->quantity }} {{ $loan->item->unit }}</div>
                    </div>
                    <div class="text-right">
                        @if($loan->isOverdue())
                            <span class="text-xs text-red-600 font-medium">Terlambat</span>
                        @elseif($loan->due_at)
                            <span class="text-xs text-gray-500">{{ $loan->due_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <p class="text-gray-500 text-sm text-center py-4">Tidak ada peminjaman aktif</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusEl = document.getElementById('scanner-status');
    const errorEl = document.getElementById('scanner-error');
    const videoEl = document.getElementById('scanner-video');
    const manualForm = document.getElementById('manual-form');
    const scanResult = document.getElementById('scan-result');
    const resultContent = document.getElementById('result-content');
    const btnQuickReturn = document.getElementById('btn-quick-return');
    const btnDetailReturn = document.getElementById('btn-detail-return');
    
    let currentQrKey = null;
    let html5QrCodeScanner = null;

    // Manual form submission
    manualForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('manual-code').value.trim();
        if (code) {
            window.location.href = '/loans/return-scan/' + code;
        }
    });

    function showError(message) {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
        statusEl.innerHTML = '<p class="text-red-500">Kamera tidak tersedia</p>';
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Extract QR key from URL
        let qrKey = decodedText;
        const urlMatch = decodedText.match(/\/loans\/return-scan\/([a-zA-Z0-9]+)/);
        if (urlMatch) {
            qrKey = urlMatch[1];
        }

        currentQrKey = qrKey;

        // Stop scanning temporarily
        if (html5QrCodeScanner) {
            html5QrCodeScanner.pause();
        }

        // Show result
        statusEl.innerHTML = '<p class="text-green-600 font-semibold">✓ QR Code terdeteksi!</p>';
        resultContent.innerHTML = '<p class="text-sm text-gray-600">Kode: ' + qrKey + '</p>';
        scanResult.classList.remove('hidden');
    }

    function onScanError(errorMessage) {
        // Ignore scan errors (no QR found is normal)
    }

    // Quick return button
    btnQuickReturn.addEventListener('click', function() {
        if (currentQrKey) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/loans/quick-return/' + currentQrKey;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            document.body.appendChild(form);
            form.submit();
        }
    });

    // Detail return button
    btnDetailReturn.addEventListener('click', function() {
        if (currentQrKey) {
            window.location.href = '/loans/return-scan/' + currentQrKey;
        }
    });

    async function startScanner() {
        try {
            // Hide the original video element, library creates its own
            videoEl.style.display = 'none';
            
            // Get the container and let the library use it
            const container = document.getElementById('scanner-container');
            container.innerHTML = '<div id="qr-reader" style="width: 100%;"></div>';
            
            html5QrCodeScanner = new Html5QrcodeScanner(
                "qr-reader",
                { 
                    fps: 10, 
                    qrbox: { width: 200, height: 200 },
                    rememberLastUsedCamera: true,
                },
                false
            );
            
            html5QrCodeScanner.render(onScanSuccess, onScanError);
            
            statusEl.innerHTML = '<p class="text-green-600">✓ Kamera aktif - arahkan ke QR Code</p>';
            
        } catch (err) {
            console.error('Scanner error:', err);
            showError('Tidak dapat mengakses kamera. Pastikan browser diizinkan mengakses kamera.');
        }
    }

    // Start the scanner
    startScanner();
});
</script>
@endsection
