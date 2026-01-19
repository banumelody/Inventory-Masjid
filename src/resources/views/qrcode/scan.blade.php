@extends('layouts.app')

@section('title', 'Scan QR Code - Inventory Masjid')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('items.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali ke Daftar</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">📷 Scan QR Code</h1>
        
        <p class="text-gray-600 text-center mb-6">Arahkan kamera ke QR Code pada label barang</p>

        <!-- Camera View -->
        <div id="scanner-container" class="relative mb-4">
            <video id="scanner-video" class="w-full rounded-lg bg-gray-900" autoplay playsinline></video>
            <div id="scanner-overlay" class="absolute inset-0 flex items-center justify-center">
                <div class="border-2 border-green-500 w-48 h-48 rounded-lg opacity-50"></div>
            </div>
        </div>

        <!-- Status Messages -->
        <div id="scanner-status" class="text-center mb-4">
            <p class="text-gray-500">Memuat kamera...</p>
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
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Cari
                </button>
            </form>
        </div>
    </div>

    <!-- Instructions -->
    <div class="mt-6 bg-blue-50 rounded-lg p-4">
        <h3 class="font-semibold text-blue-800 mb-2">💡 Tips:</h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Pastikan pencahayaan cukup</li>
            <li>• Posisikan QR Code di tengah kotak hijau</li>
            <li>• Jaga jarak 10-20 cm dari label</li>
            <li>• Izinkan akses kamera jika diminta</li>
        </ul>
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
    
    let html5QrCode = null;

    // Manual form submission
    manualForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('manual-code').value.trim();
        if (code) {
            window.location.href = '/i/' + code;
        }
    });

    function showError(message) {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
        statusEl.innerHTML = '<p class="text-red-500">Kamera tidak tersedia</p>';
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanning
        if (html5QrCode) {
            html5QrCode.stop();
        }

        // Extract QR key from URL or use directly
        let qrKey = decodedText;
        
        // If it's a full URL, extract the key
        const urlMatch = decodedText.match(/\/i\/([a-zA-Z0-9]+)/);
        if (urlMatch) {
            qrKey = urlMatch[1];
        }

        statusEl.innerHTML = '<p class="text-green-600 font-semibold">✓ QR Code terdeteksi! Mengarahkan...</p>';
        
        // Redirect to item
        window.location.href = '/i/' + qrKey;
    }

    function onScanError(errorMessage) {
        // Ignore scan errors (no QR found is normal)
    }

    async function startScanner() {
        try {
            html5QrCode = new Html5Qrcode("scanner-video", { 
                formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE] 
            });

            const config = {
                fps: 10,
                qrbox: { width: 200, height: 200 },
                aspectRatio: 1,
            };

            // Hide the original video element, library creates its own
            videoEl.style.display = 'none';
            
            // Get the container and let the library use it
            const container = document.getElementById('scanner-container');
            container.innerHTML = '<div id="qr-reader" style="width: 100%;"></div>';
            
            const html5QrCodeScanner = new Html5QrcodeScanner(
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
