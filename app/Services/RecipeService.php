<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class RecipeService
{
    public function getRecipes(array $filters = [], ?string $sortBy = null, ?string $sortDirection = 'asc', int $perPage = 10)
    {
        $query = Recipe::with([
            'ingredients' => fn($q) => $q->select('ingredients.id', 'name', 'category'),
            'ratings',
            'image'
        ])
        ->withAvg('ratings as average_rating', 'rating')
        ->select(
            'id',
            'name',
            'image_id',
            'meal_time',
            'nutrition',
            'diet_type',
            'protein_source',
            'difficulty',
            'cooking_time'
        );

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
        return Recipe::with(['instructions', 'ingredients', 'image'])
            ->withAvg('ratings as average_rating', 'rating')
            ->find($id);
    }

    public function getFilters(): array
    {
        return Cache::remember('recipe_filters', 3600, function() {
            return [
                'mealTimes' => Recipe::distinct()->pluck('meal_time')->filter()->values(),
                'nutritionTypes' => Recipe::distinct()->pluck('nutrition')->filter()->values(),
                'proteinSources' => Recipe::distinct()->pluck('protein_source')->filter()->values()
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
            $query->where('meal_time', $filters['meal_time']);
        }

        if (!empty($filters['nutrition'])) {
            $query->where('nutrition', $filters['nutrition']);
        }

        if (!empty($filters['protein_source'])) {
            $query->where('protein_source', $filters['protein_source']);
        }

        if (!empty($filters['diet_type'])) {
            $query->where('diet_type', $filters['diet_type']);
        }

        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        // Filter by dietary preferences (forbidden ingredients)
        if (!empty($filters['filter_by_preferences']) && Auth::check()) {
            $user = Auth::user()->load(['dietaryRestrictions.restrictedIngredients', 'allergies.allergicIngredients']);
            $forbiddenIds = $user->getForbiddenIngredientIds();

            if (!empty($forbiddenIds)) {
                $query->whereDoesntHave('ingredients', function($q) use ($forbiddenIds) {
                    $q->whereIn('ingredients.id', $forbiddenIds);
                });
            }
        }

        // Filter by favorites only
        if (!empty($filters['favorites_only']) && Auth::check()) {
            $favoriteIds = $this->getUserFavoriteIds(Auth::id());
            $query->whereIn('id', $favoriteIds);
        }
    }

    private function sortRecipes(Collection $recipes, string $sortBy, string $sortDirection): Collection
    {
        return $recipes->sortBy(function($recipe) use ($sortBy) {
            switch ($sortBy) {
                case 'rating':
                    return (float) $recipe->average_rating;
                case 'difficulty':
                    $difficultyMap = [
                        'Viegls' => 1,
                        'Vidējs' => 2,
                        'Grūts' => 3
                    ];
                    return $difficultyMap[$recipe->difficulty] ?? 4;
                case 'cooking_time':
                    return $recipe->cooking_time;
                default:
                    return 0;
            }
        }, SORT_REGULAR, $sortDirection === 'desc')->values();
    }

    public function generateRecipePdf(int $recipeId)
    {
        $recipe = Recipe::with(['instructions', 'ingredients', 'image'])
            ->withAvg('ratings as average_rating', 'rating')
            ->findOrFail($recipeId);

        $pdf = Pdf::loadView('pdfs.recipe', compact('recipe'));

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }
}
