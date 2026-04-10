<?php

namespace App\Http\Controllers;

use App\Models\ScanLog;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanLogController extends Controller
{
    /**
     * Display listing of scan logs
     */
    public function index(Request $request): View
    {
        $query = ScanLog::with(['item.category', 'user'])
            ->orderBy('scanned_at', 'desc');

        // Filter by item
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scanned_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('scanned_at', '<=', $request->date_to);
        }

        // Filter by purpose
        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        $scanLogs = $query->paginate(20)->withQueryString();
        
        $items = Item::orderBy('name')->get();
        $purposes = ScanLog::distinct()->whereNotNull('purpose')->pluck('purpose');

        // Statistics
        $stats = [
            'total_scans' => ScanLog::count(),
            'today_scans' => ScanLog::whereDate('scanned_at', today())->count(),
            'week_scans' => ScanLog::where('scanned_at', '>=', now()->subWeek())->count(),
            'unique_items_scanned' => ScanLog::distinct('item_id')->count('item_id'),
        ];

        return view('scan-logs.index', compact('scanLogs', 'items', 'purposes', 'stats'));
    }

    /**
     * Show scan log detail
     */
    public function show(ScanLog $scanLog): View
    {
        $scanLog->load(['item.category', 'item.location', 'user']);
        
        return view('scan-logs.show', compact('scanLog'));
    }

    /**
     * Export scan logs to CSV
     */
    public function export(Request $request)
    {
        $query = ScanLog::with(['item.category', 'user'])
            ->orderBy('scanned_at', 'desc');

        // Apply same filters as index
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('scanned_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('scanned_at', '<=', $request->date_to);
        }

        $scanLogs = $query->get();

        $filename = 'scan_logs_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($scanLogs) {
            $file = fopen('php://output', 'w');
            
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'ID',
                'Waktu Scan',
                'Nama Barang',
                'Kode QR',
                'User',
                'Tujuan',
                'Catatan',
                'IP Address',
            ]);

            // Data
            foreach ($scanLogs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->scanned_at->format('Y-m-d H:i:s'),
                    $log->item->name ?? '-',
                    $log->item->qr_code_key ?? '-',
                    $log->user->name ?? 'Guest',
                    $log->purpose ?? '-',
                    $log->notes ?? '-',
                    $log->ip_address ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
