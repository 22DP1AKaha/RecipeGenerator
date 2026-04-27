<?php

namespace App\Http\Controllers\Api;

use App\Models\Ingredient;
use App\Http\Controllers\Controller;

class IngredientController extends Controller
{
    public function index()
    {
        // Ielasam visas sastāvdaļas un grupējam pēc kategorijas
        $ingredients = Ingredient::with('category')->get()->groupBy(fn($i) => $i->category->name);
        return response()->json($ingredients);
    }
}
