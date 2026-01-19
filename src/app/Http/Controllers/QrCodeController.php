<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ScanLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    /**
     * Generate QR code key untuk item
     */
    public function generate(Item $item): RedirectResponse
    {
        if (!$item->hasQrCode()) {
            $item->generateQrCodeKey();
        }

        return redirect()->route('items.show', $item)
            ->with('success', 'QR Code berhasil dibuat.');
    }

    /**
     * Preview QR code single item
     */
    public function preview(Item $item): View
    {
        if (!$item->hasQrCode()) {
            $item->generateQrCodeKey();
        }

        return view('qrcode.preview', compact('item'));
    }

    /**
     * Halaman cetak label QR single item
     */
    public function printSingle(Item $item): View
    {
        if (!$item->hasQrCode()) {
            $item->generateQrCodeKey();
        }

        return view('qrcode.print-single', compact('item'));
    }

    /**
     * Form pilih items untuk cetak massal
     */
    public function bulkForm(Request $request): View
    {
        $items = Item::with(['category', 'location'])
            ->orderBy('name')
            ->get();

        $selectedIds = $request->input('items', []);

        return view('qrcode.bulk-form', compact('items', 'selectedIds'));
    }

    /**
     * Halaman cetak massal QR labels
     */
    public function bulkPrint(Request $request): View
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'exists:items,id',
            'size' => 'in:small,medium',
        ]);

        $items = Item::whereIn('id', $validated['items'])->orderBy('name')->get();
        $size = $validated['size'] ?? 'small';

        // Generate QR keys for items yang belum punya
        foreach ($items as $item) {
            if (!$item->hasQrCode()) {
                $item->generateQrCodeKey();
            }
        }

        return view('qrcode.print-bulk', compact('items', 'size'));
    }

    /**
     * Halaman scan QR code
     */
    public function scanPage(): View
    {
        return view('qrcode.scan');
    }

    /**
     * Handle scan result - redirect ke detail item
     * Supports optional purpose and notes via query params
     */
    public function handleScan(Request $request, string $qrKey): RedirectResponse
    {
        $item = Item::where('qr_code_key', $qrKey)->first();

        if (!$item) {
            return redirect()->route('qrcode.scan')
                ->with('error', 'QR Code tidak ditemukan atau tidak valid.');
        }

        // Log scan
        ScanLog::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'scanned_at' => now(),
            'purpose' => $request->input('purpose'),
            'notes' => $request->input('notes'),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('items.show', $item)
            ->with('success', 'QR Code berhasil dipindai!');
    }

    /**
     * Handle scan with purpose selection (for audit mode)
     */
    public function handleScanWithPurpose(Request $request, string $qrKey): RedirectResponse
    {
        $validated = $request->validate([
            'purpose' => 'required|in:audit,check,maintenance,other',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = Item::where('qr_code_key', $qrKey)->first();

        if (!$item) {
            return redirect()->route('qrcode.scan')
                ->with('error', 'QR Code tidak ditemukan atau tidak valid.');
        }

        // Log scan with purpose
        ScanLog::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'scanned_at' => now(),
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'] ?? null,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('items.show', $item)
            ->with('success', 'QR Code berhasil dipindai untuk ' . ScanLog::PURPOSES[$validated['purpose']] . '!');
    }

    /**
     * Audit scan page - scan with purpose selection
     */
    public function auditScanPage(): View
    {
        return view('qrcode.audit-scan');
    }

    /**
     * Generate QR code SVG untuk ditampilkan
     */
    public function qrSvg(Item $item): \Illuminate\Http\Response
    {
        if (!$item->hasQrCode()) {
            abort(404);
        }

        $svg = QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($item->qr_code_url);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
        ]);
    }
}
