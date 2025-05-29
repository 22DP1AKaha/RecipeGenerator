<?php

namespace App\Http\Controllers\Api;

use App\Models\Ingredient;
use App\Http\Controllers\Controller;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::all()->groupBy('kategorija');
        return response()->json($ingredients);
    }
}