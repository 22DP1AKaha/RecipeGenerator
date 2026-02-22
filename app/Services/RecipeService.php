<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\Favorite;
use App\Models\MealTime;
use App\Models\NutritionType;
use App\Models\ProteinSource;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;

class RecipeService
{
    public function getRecipes(array $filters = [], ?string $sortBy = null, ?string $sortDirection = 'asc', int $perPage = 10)
    {
        $query = Recipe::with([
            'ingredients.category',
            'ratings',
            'favorites',
            'image',
            'difficultyLevel',
            'mealTime',
            'nutritionType',
            'dietType',
            'proteinSource',
        ])
        ->withAvg('ratings as average_rating', 'rating')
        ->select('recipes.*');

        $this->applyFilters($query, $filters);

        if ($sortBy && $sortDirection) {
            $query = $this->applySorting($query, $sortBy, $sortDirection);
        }

        return $query->paginate($perPage);
    }

    private function applySorting($query, string $sortBy, string $sortDirection)
    {
        if ($sortBy === 'average_rating') {
            return $query->orderBy('average_rating', $sortDirection);
        } elseif ($sortBy === 'cooking_time') {
            return $query->orderBy('cooking_time', $sortDirection);
        }
        return $query;
    }

    public function getRecipeById(int $id): ?Recipe
    {
        return Recipe::with([
            'instructions',
            'ingredients.category',
            'ratings',
            'favorites',
            'image',
            'difficultyLevel',
            'mealTime',
            'nutritionType',
            'dietType',
            'proteinSource',
        ])
        ->withAvg('ratings as average_rating', 'rating')
        ->find($id);
    }

    public function getFilters(): array
    {
        return Cache::remember('recipe_filters', 3600, function() {
            return [
                'mealTimes' => MealTime::pluck('name')->values(),
                'nutritionTypes' => NutritionType::pluck('name')->values(),
                'proteinSources' => ProteinSource::pluck('name')->values(),
            ];
        });
    }

    public function getUserFavoriteIds(?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return [];
        }

        return Favorite::where('user_id', $userId)
            ->pluck('recipe_id')
            ->toArray();
    }

    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['meal_time'])) {
            $query->whereHas('mealTime', fn($q) => $q->where('name', $filters['meal_time']));
        }

        if (!empty($filters['nutrition'])) {
            $query->whereHas('nutritionType', fn($q) => $q->where('name', $filters['nutrition']));
        }

        if (!empty($filters['protein_source'])) {
            $query->whereHas('proteinSource', fn($q) => $q->where('name', $filters['protein_source']));
        }

        if (!empty($filters['diet_type'])) {
            $query->whereHas('dietType', fn($q) => $q->where('name', $filters['diet_type']));
        }

        if (!empty($filters['difficulty'])) {
            $query->whereHas('difficultyLevel', fn($q) => $q->where('name', $filters['difficulty']));
        }

        if (!empty($filters['filter_by_preferences']) && Auth::check()) {
            $user = Auth::user()->load(['dietaryRestrictions.restrictedIngredients', 'allergies.allergicIngredients']);
            $forbiddenIds = $user->getForbiddenIngredientIds();

            if (!empty($forbiddenIds)) {
                $query->whereDoesntHave('ingredients', function($q) use ($forbiddenIds) {
                    $q->whereIn('ingredients.id', $forbiddenIds);
                });
            }
        }

        if (!empty($filters['favorites_only']) && Auth::check()) {
            $favoriteIds = $this->getUserFavoriteIds(Auth::id());
            $query->whereIn('id', $favoriteIds);
        }
    }

    public function generateRecipePdf(int $recipeId)
    {
        $recipe = Recipe::with([
            'instructions',
            'ingredients.category',
            'image',
            'difficultyLevel',
            'mealTime',
            'nutritionType',
            'dietType',
            'proteinSource',
        ])
        ->withAvg('ratings as average_rating', 'rating')
        ->findOrFail($recipeId);

        $unitNames = Cache::remember('units_map', 3600, fn() => Unit::pluck('name', 'id'));

        $pdf = Pdf::loadView('pdfs.recipe', compact('recipe', 'unitNames'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }
}
