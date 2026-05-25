<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function index(Request $request)
    {
        $ids = array_values(array_unique(
            array_filter(array_map('intval', (array) $request->input('ids', [])))
        ));
        $ids = array_slice($ids, 0, 100);

        $cars = collect();
        if (!empty($ids)) {
            $cars = Car::with(['brand', 'images'])
                ->available()
                ->whereIn('id', $ids)
                ->get()
                ->sortBy(fn($car) => array_search($car->id, $ids))
                ->values();
        }

        $validIds = $cars->pluck('id')->all();

        return view('catalog.favorites', compact('cars', 'validIds'));
    }
}
