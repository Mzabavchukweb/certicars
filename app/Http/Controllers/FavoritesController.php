<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function index(Request $request)
    {
        $ids = $request->input('ids', []);
        $ids = array_filter(array_map('intval', (array) $ids));

        $cars = collect();
        if (!empty($ids)) {
            $cars = Car::with(['brand', 'images'])
                ->available()
                ->whereIn('id', $ids)
                ->get()
                ->sortBy(fn($car) => array_search($car->id, $ids))
                ->values();
        }

        return view('catalog.favorites', compact('cars'));
    }
}
