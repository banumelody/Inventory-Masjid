<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Item;
use App\Models\ActivityLog;
use App\Scopes\MasjidScope;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LoanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Loan::with(['item.category', 'item.location']);

        if ($request->filled('status')) {
            if ($request->status === 'borrowed') {
                $query->whereNull('returned_at');
            } elseif ($request->status === 'returned') {
                $query->whereNotNull('returned_at');
            } elseif ($request->status === 'overdue') {
                $query->whereNull('returned_at')
                      ->whereNotNull('due_at')
                      ->where('due_at', '<', now());
            }
        }

        if ($request->filled('search')) {
            $query->where('borrower_name', 'like', '%' . $request->search . '%');
        }

        $loans = $query->orderBy('borrowed_at', 'desc')->paginate(10)->withQueryString();

        return view('loans.index', compact('loans'));
    }

    public function create(Request $request): View
    {
        $items = Item::with(['category', 'location'])
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();
        
        $selectedItem = null;
        if ($request->filled('item_id')) {
            $selectedItem = Item::find($request->item_id);
        }

        return view('loans.create', compact('items', 'selectedItem'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'borrower_name' => 'required|string|max:255',
            'borrower_phone' => 'nullable|string|max:50',
            'quantity' => 'required|integer|min:1',
            'borrowed_at' => 'required|date',
            'due_at' => 'nullable|date|after_or_equal:borrowed_at',
            'notes' => 'nullable|string',
        ]);

        $item = Item::findOrFail($validated['item_id']);
        
        if ($validated['quantity'] > $item->available_quantity) {
            return back()->withInput()->with('error', 
                "Jumlah melebihi stok tersedia ({$item->available_quantity} {$item->unit}).");
        }

        // Create loan and generate QR key
        $loan = Loan::create($validated);
        $loan->generateReturnQrKey();

        // Log activity
        ActivityLog::log('create', "Peminjaman baru: {$item->name} oleh {$loan->borrower_name}", $loan, null, $validated);

        return redirect()->route('loans.index')
            ->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function show(Loan $loan): View
    {
        $loan->load(['item.category', 'item.location']);
        return view('loans.show', compact('loan'));
    }

    public function returnForm(Loan $loan): View
    {
        if ($loan->isReturned()) {
            abort(404, 'Barang sudah dikembalikan.');
        }
        
        $loan->load(['item']);
        return view('loans.return', compact('loan'));
    }

    public function returnItem(Request $request, Loan $loan): RedirectResponse
    {
        if ($loan->isReturned()) {
            return redirect()->route('loans.index')
                ->with('error', 'Barang sudah dikembalikan sebelumnya.');
        }

        $validated = $request->validate([
            'returned_at' => 'required|date|after_or_equal:' . $loan->borrowed_at->format('Y-m-d'),
            'returned_condition' => 'required|in:baik,perlu_perbaikan,rusak',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $loan->toArray();

        $loan->update([
            'returned_at' => $validated['returned_at'],
            'returned_condition' => $validated['returned_condition'],
            'notes' => $validated['notes'] ?? $loan->notes,
        ]);

        // Log activity
        ActivityLog::log('return', "Pengembalian: {$loan->item->name} dari {$loan->borrower_name}", $loan, $oldValues, $validated);

        return redirect()->route('loans.index')
            ->with('success', 'Pengembalian berhasil dicatat.');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $loan->delete();

        return redirect()->route('loans.index')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }

    /**
     * Scan return page - QR scanner for returning items
     */
    public function scanReturnPage(): View
    {
        return view('loans.scan-return');
    }

    /**
     * Handle scan return - redirect to return form
     */
    public function handleScanReturn(string $qrKey): RedirectResponse
    {
        $loan = Loan::withoutGlobalScope(MasjidScope::class)->where('return_qr_key', $qrKey)->first();

        if (!$loan) {
            return redirect()->route('loans.scan-return')
                ->with('error', 'QR Code tidak ditemukan atau tidak valid.');
        }

        if ($loan->isReturned()) {
            return redirect()->route('loans.show', $loan)
                ->with('error', 'Barang ini sudah dikembalikan pada ' . $loan->returned_at->format('d/m/Y'));
        }

        return redirect()->route('loans.return', $loan);
    }

    /**
     * Quick return via scan - immediately mark as returned
     */
    public function quickReturn(Request $request, string $qrKey): RedirectResponse
    {
        $loan = Loan::withoutGlobalScope(MasjidScope::class)->where('return_qr_key', $qrKey)->first();

        if (!$loan) {
            return redirect()->route('loans.scan-return')
                ->with('error', 'QR Code tidak ditemukan.');
        }

        if ($loan->isReturned()) {
            return redirect()->route('loans.show', $loan)
                ->with('error', 'Barang ini sudah dikembalikan.');
        }

        $loan->update([
            'returned_at' => now(),
            'returned_condition' => 'baik',
        ]);

        return redirect()->route('loans.index')
            ->with('success', "Barang '{$loan->item->name}' berhasil dikembalikan oleh {$loan->borrower_name}.");
    }

    /**
     * Generate QR code for loan return
     */
    public function generateReturnQr(Loan $loan): RedirectResponse
    {
        if (!$loan->hasReturnQrCode()) {
            $loan->generateReturnQrKey();
        }

        return redirect()->route('loans.show', $loan)
            ->with('success', 'QR Code pengembalian berhasil dibuat.');
    }

    /**
     * Get return QR code SVG
     */
    public function returnQrSvg(Loan $loan): \Illuminate\Http\Response
    {
        if (!$loan->hasReturnQrCode()) {
            abort(404);
        }

        $svg = QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($loan->return_qr_url);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
        ]);
    }

    /**
     * Print return QR label
     */
    public function printReturnQr(Loan $loan): View
    {
        if (!$loan->hasReturnQrCode()) {
            $loan->generateReturnQrKey();
        }

        $loan->load(['item']);
        return view('loans.print-return-qr', compact('loan'));
    }
}
