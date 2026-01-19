@extends('layouts.app')

@section('title', 'Audit Scan QR - Inventory Masjid')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('items.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Kembali ke Daftar</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2 text-center">📋 Audit Scan QR</h1>
        <p class="text-gray-500 text-center mb-6">Scan QR code untuk keperluan audit/pengecekan inventaris</p>
        
        <!-- Purpose Selection -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan Scan</label>
            <div class="grid grid-cols-2 gap-2" id="purpose-selector">
                @foreach(\App\Models\ScanLog::PURPOSES as $key => $label)
                <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors purpose-option">
                    <input type="radio" name="purpose" value="{{ $key }}" class="text-green-600 focus:ring-green-500"
                           {{ $key === 'audit' ? 'checked' : '' }}>
                    <span class="text-sm">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Notes Input -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
            <input type="text" id="scan-notes" placeholder="Tambahkan catatan jika perlu..."
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
        </div>

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
            <form id="manual-form" class="flex gap-2">
                <input type="text" 
                       id="manual-code" 
                       placeholder="Masukkan kode QR"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Cari
                </button>
            </form>
        </div>
    </div>

    <!-- Scan Count for this session -->
    <div class="mt-4 bg-blue-50 rounded-lg p-4">
        <div class="flex justify-between items-center">
            <span class="text-blue-800 font-medium">Scan sesi ini:</span>
            <span id="scan-count" class="text-2xl font-bold text-blue-600">0</span>
        </div>
    </div>

    <!-- Instructions -->
    <div class="mt-4 bg-green-50 rounded-lg p-4">
        <h3 class="font-semibold text-green-800 mb-2">💡 Mode Audit:</h3>
        <ul class="text-sm text-green-700 space-y-1">
            <li>• Pilih tujuan scan terlebih dahulu</li>
            <li>• Scan akan tercatat untuk keperluan audit</li>
            <li>• Admin dapat melihat log scan di menu Scan Logs</li>
            <li>• Tambahkan catatan jika ada temuan</li>
        </ul>
    </div>
</div>

<!-- Form for POST submission -->
<form id="audit-form" method="POST" action="" class="hidden">
    @csrf
    <input type="hidden" name="purpose" id="form-purpose">
    <input type="hidden" name="notes" id="form-notes">
</form>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusEl = document.getElementById('scanner-status');
    const errorEl = document.getElementById('scanner-error');
    const videoEl = document.getElementById('scanner-video');
    const manualForm = document.getElementById('manual-form');
    const auditForm = document.getElementById('audit-form');
    const scanCountEl = document.getElementById('scan-count');
    
    let scanCount = 0;
    let isProcessing = false;

    function getSelectedPurpose() {
        return document.querySelector('input[name="purpose"]:checked')?.value || 'audit';
    }

    function getNotes() {
        return document.getElementById('scan-notes').value.trim();
    }

    // Manual form submission
    manualForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('manual-code').value.trim();
        if (code) {
            submitAuditScan(code);
        }
    });

    function submitAuditScan(qrKey) {
        if (isProcessing) return;
        isProcessing = true;

        // Extract key from URL if needed
        const urlMatch = qrKey.match(/\/i\/([a-zA-Z0-9]+)/);
        if (urlMatch) {
            qrKey = urlMatch[1];
        }

        // Set form values
        document.getElementById('form-purpose').value = getSelectedPurpose();
        document.getElementById('form-notes').value = getNotes();
        
        // Set form action
        auditForm.action = '/i/' + qrKey + '/audit';
        
        // Submit form
        auditForm.submit();
    }

    function showError(message) {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
        statusEl.innerHTML = '<p class="text-red-500">Kamera tidak tersedia</p>';
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;
        
        statusEl.innerHTML = '<p class="text-green-600 font-semibold">✓ QR Code terdeteksi! Menyimpan...</p>';
        submitAuditScan(decodedText);
    }

    function onScanError(errorMessage) {
        // Ignore scan errors
    }

    async function startScanner() {
        try {
            videoEl.style.display = 'none';
            
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

    // Update purpose option styling
    document.querySelectorAll('.purpose-option').forEach(label => {
        const radio = label.querySelector('input[type="radio"]');
        
        function updateStyle() {
            document.querySelectorAll('.purpose-option').forEach(l => {
                l.classList.remove('border-green-500', 'bg-green-50');
                l.classList.add('border-gray-200');
            });
            
            if (radio.checked) {
                label.classList.remove('border-gray-200');
                label.classList.add('border-green-500', 'bg-green-50');
            }
        }
        
        radio.addEventListener('change', updateStyle);
        updateStyle();
    });

    // Start the scanner
    startScanner();
});
</script>
@endsection
