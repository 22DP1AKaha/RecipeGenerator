<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecipeResource;
use App\Services\RecipeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecipeController extends Controller
{
    private RecipeService $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    public function index(Request $request)
    {
        try {
            $filters = $request->only([
                'meal_time',
                'nutrition',
                'protein_source',
                'diet_type',
                'difficulty',
                'filter_by_preferences',
                'favorites_only'
            ]);
            $sortBy = $request->input('sort_by');
            $sortDirection = $request->input('sort_direction', 'asc');
            $perPage = $request->input('per_page', 10);

            $recipes = $this->recipeService->getRecipes($filters, $sortBy, $sortDirection, $perPage);

            return RecipeResource::collection($recipes);
        } catch (\Exception $e) {
            Log::error('Recipe fetch error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function getFilters()
    {
        try {
            return response()->json($this->recipeService->getFilters());
        } catch (\Exception $e) {
            Log::error('Filter fetch error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function show($id)
    {
        try {
            $recipe = $this->recipeService->getRecipeById($id);

            if (!$recipe) {
                return response()->json(['message' => 'Recepte nav atrasta!'], 404);
            }

            return new RecipeResource($recipe);
        } catch (\Exception $e) {
            Log::error('Recipe detail error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function downloadPdf($id)
    {
        try {
            $pdf = $this->recipeService->generateRecipePdf($id);

            return $pdf->download('recepte-' . $id . '.pdf');
        } catch (\Exception $e) {
            Log::error('PDF generation error: ' . $e->getMessage());
            return response()->json(['error' => 'Kļūda ģenerējot PDF'], 500);
        }
    }
}