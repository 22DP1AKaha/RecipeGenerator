<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    // Fetch all recipes (already exists)
    public function index()
    {
        $recipes = Recipe::select('id', 'nosaukums as title', 'attels as image')->get();
        return response()->json($recipes);
    }

    // Fetch a single recipe with instructions
    public function show($id)
{
    // Fetch the recipe with its instructions and ingredients (including pivot data)
    $recipe = Recipe::with(['instructions', 'ingredients'])->find($id);

    if (!$recipe) {
        return response()->json(['message' => 'Recepte nav atrasta!'], 404);
    }

    return response()->json([
        'id' => $recipe->id,
        'nosaukums' => $recipe->nosaukums,
        'attels' => $recipe->attels,
        'gatavosanas_laiks' => $recipe->gatavosanas_laiks,
        'grutibas_pakape' => $recipe->grutibas_pakape,
        'apraksts' => $recipe->apraksts,
        'instructions' => $recipe->instructions->map(function ($instruction) {
            return [
                'solis_numurs' => $instruction->solis_numurs,
                'apraksts' => $instruction->apraksts
            ];
        }),
        'ingredients' => $recipe->ingredients->map(function ($ingredient) {
            return [
                'id' => $ingredient->id,
                'nosaukums' => $ingredient->nosaukums,
                'daudzums' => $ingredient->pivot->daudzums, // Pivot data
                'piezimes' => $ingredient->pivot->piezimes // Pivot data
            ];
        })
    ]);
}
}