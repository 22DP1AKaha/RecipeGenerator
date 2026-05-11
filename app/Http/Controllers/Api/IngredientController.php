<?php

namespace App\Http\Controllers\Api;

use App\Models\Ingredient;
use App\Http\Controllers\Controller;

class IngredientController extends Controller // Vienkāršs API endpoints sastāvdaļu sarakstam
{
    public function index()
    {
        // Visas sastāvdaļas, sagrupētas pa kategorijām (gaļa, dārzeņi utt.) frontendā tā ērtāk parādīt
        $ingredients = Ingredient::with('category')->get()->groupBy(fn($i) => $i->category->name);
        return response()->json($ingredients);
    }
}
