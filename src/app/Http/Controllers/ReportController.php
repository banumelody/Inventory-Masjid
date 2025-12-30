<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Item::with(['category', 'location']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $items = $query->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('reports.index', compact('items', 'categories', 'locations'));
    }

    public function print(Request $request): View
    {
        $query = Item::with(['category', 'location']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $items = $query->orderBy('name')->get();
        
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

        return view('reports.print', compact('items', 'categoryName', 'locationName'));
    }
}
