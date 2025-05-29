<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function index()
    {
        try {
            // Get user's favorite recipe IDs
            $favoriteIds = [];
            if (Auth::check()) {
                $favoriteIds = Favorite::where('user_id', Auth::id())
                    ->pluck('receptes_id')
                    ->toArray();
            }

            $recipes = Recipe::with('ingredients')
                ->withAvg('ratings as average_rating', 'vertejums')
                ->select(
                    'id',
                    'nosaukums as title',
                    'attels as image',
                    'edienreize',
                    'uzturs',
                    'dietas_tips',
                    'galvenais_olbaltumvielu_avots'
                )
                ->get();

            $recipes = $recipes->map(function ($recipe) use ($favoriteIds) {
                return [
                    'id'                         => $recipe->id,
                    'title'                      => $recipe->title,
                    'image'                      => $recipe->image,
                    'edienreize'                 => $recipe->edienreize,
                    'uzturs'                     => $recipe->uzturs,
                    'dietas_tips'                => $recipe->dietas_tips,
                    'galvenais_olbaltumvielu_avots' => $recipe->galvenais_olbaltumvielu_avots,
                    'ingredients'                => $recipe->ingredients->pluck('id')->toArray(),
                    'average_rating'             => $recipe->average_rating ? (float) number_format($recipe->average_rating, 1) : 0,
                    'is_saved'                   => in_array($recipe->id, $favoriteIds),
                ];
            });

            return response()->json($recipes);
        } catch (\Exception $e) {
            Log::error('Recipe fetch error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function getFilters()
    {
        try {
            return response()->json([
                'mealTimes' => Recipe::distinct()->pluck('edienreize')->filter()->values(),
                'nutritionTypes' => Recipe::distinct()->pluck('uzturs')->filter()->values(),
                'proteinSources' => Recipe::distinct()->pluck('galvenais_olbaltumvielu_avots')->filter()->values()
            ]);
        } catch (\Exception $e) {
            Log::error('Filter fetch error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function show($id)
    {
        try {
            $recipe = Recipe::with(['instructions', 'ingredients'])
                ->withAvg('ratings as average_rating', 'vertejums')
                ->find($id);

            if (!$recipe) {
                return response()->json(['message' => 'Recepte nav atrasta!'], 404);
            }

            $userRating = 0;
            $isSaved = false;
            
            if (Auth::check()) {
                // Get authenticated user's rating if exists
                $userRating = $recipe->ratings()
                    ->where('user_id', Auth::id())
                    ->value('vertejums') ?? 0;
                    
                // Check if recipe is favorited
                $isSaved = Favorite::where('user_id', Auth::id())
                    ->where('receptes_id', $recipe->id)
                    ->exists();
            }

            return response()->json([
                'id' => $recipe->id,
                'nosaukums' => $recipe->nosaukums,
                'attels' => $recipe->attels,
                'gatavosanas_laiks' => $recipe->gatavosanas_laiks,
                'grutibas_pakape' => $recipe->grutibas_pakape,
                'apraksts' => $recipe->apraksts,
                'average_rating' => $recipe->average_rating ? (float) number_format($recipe->average_rating, 1) : 0,
                'user_rating' => (int) $userRating,
                'is_saved' => $isSaved,
                'instructions' => $recipe->instructions->map(function ($instruction) {
                    return [
                        'solis_numurs' => $instruction->solis_numurs,
                        'apraksts' => $instruction->apraksts
                    ];
                }),
                'ingredients' => $recipe->ingredients->map(function ($ingredient) {
                    return [
                        'id' => $ingredient->ingredient_id,
                        'nosaukums' => $ingredient->nosaukums,
                        'daudzums' => $ingredient->pivot->daudzums,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Recipe detail error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}