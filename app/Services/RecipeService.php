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

class RecipeService // Recepšu galvenā loģika - kontrolieri tikai sauc servisu
{
    public function getRecipes(array $filters = [], ?string $sortBy = null, ?string $sortDirection = 'asc', int $perPage = 10)
    {
        // ielādējam visu, kas vajadzīgs
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
        ->select('recipes.*')
        ->withAvg('ratings as average_rating', 'rating');

        // Filtri, ja kādi ir
        $this->applyFilters($query, $filters);

        // Sortēšana, ja norādīta
        if ($sortBy && $sortDirection) {
            $query = $this->applySorting($query, $sortBy, $sortDirection);
        }

        // sadalam lapās
        return $query->paginate($perPage);
    }

    private function applySorting($query, string $sortBy, string $sortDirection)
    {
        // match - atkarībā no sortBy izvēlamies sortēšanas loģiku
        match ($sortBy) {
            'rating', 'average_rating' => $query->orderBy('average_rating', $sortDirection),
            'cooking_time'             => $query->orderBy('cooking_time', $sortDirection),
            'difficulty'               => $query->leftJoin('difficulty_levels', 'difficulty_levels.id', '=', 'recipes.difficulty_level_id')
                                                ->orderByRaw("FIELD(difficulty_levels.name, 'Viegls', 'Vidējs', 'Grūts') " . ($sortDirection === 'desc' ? 'DESC' : 'ASC')),
            default                    => null,
        };

        return $query;
    }

    public function getRecipeById(int $id): ?Recipe
    {
        // Vienas receptes detaļas
        return Recipe::with([
            'instructions',
            'ingredients.category',
            'ratings.user',
            'favorites',
            'image',
            'images',
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
        // Filtri reti mainās - kešojam stundu
        return Cache::remember('recipe_filters', 3600, function() {
            return [
                'mealTimes'      => MealTime::pluck('name')->values(),
                'nutritionTypes' => NutritionType::pluck('name')->values(),
                'proteinSources' => ProteinSource::pluck('name')->values(),
            ];
        });
    }

    public function getUserFavoriteIds(?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();

        // Anonīmiem lietotājiem favorītu nav
        if (!$userId) {
            return [];
        }

        // Tikai ID, vairāk šeit nekas nav vajadzīgs
        return Favorite::where('user_id', $userId)
            ->pluck('recipe_id')
            ->toArray();
    }

    private function applyFilters($query, array $filters): void
    {
        // Meklēšana pēc nosaukuma
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Brokastis / pusdienas / vakariņas
        if (!empty($filters['meal_time'])) {
            $query->whereHas('mealTime', fn($q) => $q->where('name', $filters['meal_time']));
        }

        // Vegānisks, veģetārs utt.
        if (!empty($filters['nutrition'])) {
            $query->whereHas('nutritionType', fn($q) => $q->where('name', $filters['nutrition']));
        }

        // Olbaltumvielu avots
        if (!empty($filters['protein_source'])) {
            $query->whereHas('proteinSource', fn($q) => $q->where('name', $filters['protein_source']));
        }

        // Diētas tips
        if (!empty($filters['diet_type'])) {
            $query->whereHas('dietType', fn($q) => $q->where('name', $filters['diet_type']));
        }

        // Cik grūta recepte
        if (!empty($filters['difficulty'])) {
            $query->whereHas('difficultyLevel', fn($q) => $q->where('name', $filters['difficulty']));
        }

        // Ja lietotājs grib, lai sistēma ņem vērā viņa profila ierobežojumus - izņemam visu, kas neder
        if (!empty($filters['filter_by_preferences']) && Auth::check()) {
            $user = Auth::user()->load(['dietaryRestrictions.restrictedIngredients', 'allergies.allergicIngredients']);
            $forbiddenIds = $user->getForbiddenIngredientIds();

            if (!empty($forbiddenIds)) {
                $query->whereDoesntHave('ingredients', function($q) use ($forbiddenIds) {
                    $q->whereIn('ingredients.id', $forbiddenIds);
                });
            }
        }

        // "Tikai favorīti" filtrs
        if (!empty($filters['favorites_only']) && Auth::check()) {
            $favoriteIds = $this->getUserFavoriteIds(Auth::id());
            $query->whereIn('id', $favoriteIds);
        }
    }

    public function generateRecipePdf(int $recipeId)
    {
        // Visa receptes info PDF vajadzībām
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

        // Mērvienības tāpat kešojam - tās praktiski nemainās
        $unitNames = Cache::remember('units_map', 3600, fn() => Unit::pluck('name', 'id'));

        // PDF no Blade šablona, A4 portrait
        $pdf = Pdf::loadView('pdfs.recipe', compact('recipe', 'unitNames'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }
}
