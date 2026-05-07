<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('cars')->orderBy('name')->paginate(30);
        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:brands,name']);
        Brand::create(['name' => $request->name]);
        Cache::forget('catalog.filters');
        return back()->with('success', 'Marka została dodana.');
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate(['name' => 'required|string|max:255|unique:brands,name,' . $brand->id]);
        $brand->update(['name' => $request->name, 'slug' => null]);
        Cache::forget('catalog.filters');
        return back()->with('success', 'Marka została zaktualizowana.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->cars()->count() > 0) {
            return back()->with('error', 'Nie można usunąć marki z przypisanymi samochodami.');
        }
        $brand->delete();
        Cache::forget('catalog.filters');
        return back()->with('success', 'Marka została usunięta.');
    }
}
