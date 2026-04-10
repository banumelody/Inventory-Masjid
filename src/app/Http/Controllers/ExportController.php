<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        
        return view('exports.index', compact('categories', 'locations'));
    }

    public function excel(Request $request): Response
    {
        $items = $this->getFilteredItems($request);
        
        $csvContent = $this->generateCsv($items);
        
        $filename = 'inventaris_' . date('Y-m-d') . '.csv';
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function pdf(Request $request)
    {
        $items = $this->getFilteredItems($request);
        
        $categoryName = 'Semua Kategori';
        $locationName = 'Semua Lokasi';
        
        if ($request->filled('category_id')) {
            $category = Category::find($request->category_id);
            $categoryName = $category ? $category->name : 'Semua Kategori';
        }
        
        if ($request->filled('location_id')) {
            $location = Location::find($request->location_id);
            $locationName = $location ? $location->name : 'Semua Lokasi';
        }

        $pdf = Pdf::loadView('exports.pdf', compact('items', 'categoryName', 'locationName'));
        
        $filename = 'inventaris_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    protected function getFilteredItems(Request $request)
    {
        $query = Item::with(['category', 'location']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        return $query->orderBy('name')->get();
    }

    protected function generateCsv($items): string
    {
        $output = chr(0xEF) . chr(0xBB) . chr(0xBF); // UTF-8 BOM for Excel
        
        // Header
        $output .= "No,Nama Barang,Kategori,Lokasi,Jumlah,Satuan,Kondisi,Catatan\n";
        
        // Data
        $no = 1;
        foreach ($items as $item) {
            $condition = match($item->condition) {
                'baik' => 'Baik',
                'perlu_perbaikan' => 'Perlu Perbaikan',
                'rusak' => 'Rusak',
                default => $item->condition,
            };
            
            $output .= sprintf(
                "%d,\"%s\",\"%s\",\"%s\",%d,\"%s\",\"%s\",\"%s\"\n",
                $no++,
                $this->sanitizeCsvValue($item->name),
                $this->sanitizeCsvValue($item->category->name),
                $this->sanitizeCsvValue($item->location->name),
                $item->quantity,
                $this->sanitizeCsvValue($item->unit),
                $condition,
                $this->sanitizeCsvValue($item->note ?? '')
            );
        }
        
        return $output;
    }

    /**
     * Sanitize CSV value to prevent formula injection.
     * Prefixes dangerous characters with a single quote to neutralize them in Excel.
     */
    protected function sanitizeCsvValue(string $value): string
    {
        $value = str_replace('"', '""', $value);

        if (preg_match('/^[=+\-@\t\r]/', $value)) {
            $value = "'" . $value;
        }

        return $value;
    }
}
