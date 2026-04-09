<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\ImportLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    public function index(): View
    {
        $imports = ImportLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('imports.index', compact('imports'));
    }

    public function create(): View
    {
        return view('imports.create');
    }

    public function preview(Request $request): View|RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
            'type' => 'required|in:items,categories,locations',
        ]);

        $file = $request->file('file');
        $type = $request->type;

        try {
            $data = $this->parseFile($file);

            if (empty($data)) {
                return back()->with('error', 'File kosong atau tidak dapat dibaca.');
            }

            // Store in session for processing
            session(['import_data' => $data, 'import_type' => $type, 'import_filename' => $file->getClientOriginalName()]);

            // Get column mapping suggestions
            $headers = array_keys($data[0] ?? []);
            $expectedColumns = $this->getExpectedColumns($type);

            return view('imports.preview', compact('data', 'type', 'headers', 'expectedColumns'));

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    public function process(Request $request): RedirectResponse
    {
        $data = session('import_data');
        $type = session('import_type');
        $filename = session('import_filename');

        if (!$data || !$type) {
            return redirect()->route('imports.create')
                ->with('error', 'Data import tidak ditemukan. Silakan upload ulang.');
        }

        $request->validate([
            'column_mapping' => 'required|array',
        ]);

        $mapping = $request->column_mapping;

        // Create import log
        $importLog = ImportLog::create([
            'user_id' => auth()->id(),
            'filename' => $filename,
            'type' => $type,
            'total_rows' => count($data),
            'status' => 'processing',
        ]);

        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header

                try {
                    $mappedData = $this->mapRowData($row, $mapping);
                    $result = $this->importRow($type, $mappedData, $rowNumber);

                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $failedCount++;
                        $errors[] = "Baris {$rowNumber}: " . $result['error'];
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            $importLog->update([
                'success_rows' => $successCount,
                'failed_rows' => $failedCount,
                'errors' => $errors,
                'status' => $failedCount === count($data) ? 'failed' : 'completed',
            ]);

            // Log activity
            ActivityLog::log(
                'import',
                "Import {$type}: {$successCount} berhasil, {$failedCount} gagal dari {$importLog->total_rows} data",
                $importLog
            );

            // Clear session
            session()->forget(['import_data', 'import_type', 'import_filename']);

            $message = "Import selesai: {$successCount} berhasil, {$failedCount} gagal.";
            return redirect()->route('imports.index')
                ->with($failedCount > 0 ? 'warning' : 'success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            $importLog->update([
                'status' => 'failed',
                'errors' => [$e->getMessage()],
            ]);

            return redirect()->route('imports.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function template(string $type)
    {
        $templates = [
            'items' => [
                'headers' => ['nama', 'kategori', 'lokasi', 'jumlah', 'satuan', 'kondisi', 'catatan'],
                'sample' => ['Kursi Lipat', 'Perabotan', 'Gudang', '10', 'unit', 'baik', 'Kursi untuk acara'],
            ],
            'categories' => [
                'headers' => ['nama'],
                'sample' => ['Perabotan'],
            ],
            'locations' => [
                'headers' => ['nama'],
                'sample' => ['Gudang Utama'],
            ],
        ];

        if (!isset($templates[$type])) {
            abort(404);
        }

        $template = $templates[$type];
        $filename = "template_import_{$type}.csv";

        $content = implode(',', $template['headers']) . "\n";
        $content .= implode(',', $template['sample']) . "\n";

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function show(ImportLog $import): View
    {
        $import->load('user');
        return view('imports.show', compact('import'));
    }

    /**
     * Parse uploaded file (CSV/Excel)
     */
    private function parseFile($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        if ($extension === 'csv' || $extension === 'txt') {
            return $this->parseCsv($path);
        }

        // For Excel files, we'll use a simple CSV conversion approach
        // In production, you'd want to use PhpSpreadsheet
        return $this->parseCsv($path);
    }

    private function parseCsv(string $path): array
    {
        $data = [];
        $headers = [];

        if (($handle = fopen($path, 'r')) !== false) {
            $rowIndex = 0;
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                // Skip empty rows
                if (count(array_filter($row)) === 0) {
                    continue;
                }

                if ($rowIndex === 0) {
                    // First row is headers
                    $headers = array_map('trim', $row);
                    $headers = array_map('strtolower', $headers);
                } else {
                    // Data rows
                    $rowData = [];
                    foreach ($headers as $i => $header) {
                        $rowData[$header] = isset($row[$i]) ? trim($row[$i]) : '';
                    }
                    $data[] = $rowData;
                }
                $rowIndex++;
            }
            fclose($handle);
        }

        return $data;
    }

    private function getExpectedColumns(string $type): array
    {
        return match($type) {
            'items' => [
                'nama' => ['required' => true, 'aliases' => ['name', 'nama_barang', 'barang']],
                'kategori' => ['required' => true, 'aliases' => ['category', 'kategori_barang']],
                'lokasi' => ['required' => true, 'aliases' => ['location', 'lokasi_barang']],
                'jumlah' => ['required' => true, 'aliases' => ['quantity', 'qty', 'stok']],
                'satuan' => ['required' => false, 'aliases' => ['unit', 'uom']],
                'kondisi' => ['required' => false, 'aliases' => ['condition', 'status']],
                'catatan' => ['required' => false, 'aliases' => ['note', 'notes', 'keterangan']],
            ],
            'categories' => [
                'nama' => ['required' => true, 'aliases' => ['name', 'nama_kategori', 'kategori']],
            ],
            'locations' => [
                'nama' => ['required' => true, 'aliases' => ['name', 'nama_lokasi', 'lokasi']],
            ],
            default => [],
        };
    }

    private function mapRowData(array $row, array $mapping): array
    {
        $mapped = [];
        foreach ($mapping as $targetField => $sourceField) {
            if ($sourceField && isset($row[$sourceField])) {
                $mapped[$targetField] = $row[$sourceField];
            }
        }
        return $mapped;
    }

    private function importRow(string $type, array $data, int $rowNumber): array
    {
        return match($type) {
            'items' => $this->importItem($data, $rowNumber),
            'categories' => $this->importCategory($data, $rowNumber),
            'locations' => $this->importLocation($data, $rowNumber),
            default => ['success' => false, 'error' => 'Tipe import tidak valid'],
        };
    }

    private function importItem(array $data, int $rowNumber): array
    {
        $validator = Validator::make($data, [
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string',
            'lokasi' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'error' => $validator->errors()->first()];
        }

        // Find or create category (scoped to current masjid)
        $masjidId = app()->bound('current_masjid_id') ? app('current_masjid_id') : null;
        $category = Category::firstOrCreate(
            ['name' => $data['kategori'], 'masjid_id' => $masjidId]
        );

        // Find or create location (scoped to current masjid)
        $location = Location::firstOrCreate(
            ['name' => $data['lokasi'], 'masjid_id' => $masjidId]
        );

        // Map condition
        $condition = 'baik';
        if (!empty($data['kondisi'])) {
            $conditionMap = [
                'baik' => 'baik',
                'good' => 'baik',
                'perlu perbaikan' => 'perlu_perbaikan',
                'perlu_perbaikan' => 'perlu_perbaikan',
                'need repair' => 'perlu_perbaikan',
                'rusak' => 'rusak',
                'broken' => 'rusak',
                'damaged' => 'rusak',
            ];
            $condition = $conditionMap[strtolower($data['kondisi'])] ?? 'baik';
        }

        // Create item
        Item::create([
            'name' => $data['nama'],
            'category_id' => $category->id,
            'location_id' => $location->id,
            'quantity' => (int) $data['jumlah'],
            'unit' => $data['satuan'] ?? 'unit',
            'condition' => $condition,
            'note' => $data['catatan'] ?? null,
        ]);

        return ['success' => true];
    }

    private function importCategory(array $data, int $rowNumber): array
    {
        $validator = Validator::make($data, [
            'nama' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'error' => $validator->errors()->first()];
        }

        // Check if exists
        if (Category::where('name', $data['nama'])->exists()) {
            return ['success' => false, 'error' => "Kategori '{$data['nama']}' sudah ada"];
        }

        Category::create(['name' => $data['nama']]);
        return ['success' => true];
    }

    private function importLocation(array $data, int $rowNumber): array
    {
        $validator = Validator::make($data, [
            'nama' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'error' => $validator->errors()->first()];
        }

        // Check if exists
        if (Location::where('name', $data['nama'])->exists()) {
            return ['success' => false, 'error' => "Lokasi '{$data['nama']}' sudah ada"];
        }

        Location::create(['name' => $data['nama']]);
        return ['success' => true];
    }
}
