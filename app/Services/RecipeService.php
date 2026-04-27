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
        // Ielasam receptes ar visiem saistītajiem datiem
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

        // Pielietojam meklēšanas filtrus
        $this->applyFilters($query, $filters);

        // Kārtojam rezultātus pēc izvēlētā parametra
        if ($sortBy && $sortDirection) {
            $query = $this->applySorting($query, $sortBy, $sortDirection);
        }

        // Atgriežam lapotos rezultātus
        return $query->paginate($perPage);
    }

    private function applySorting($query, string $sortBy, string $sortDirection)
    {
        // Izvēlamies kārtošanas veidu atbilstoši pieprasītajam laukam
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
        // Ielasam recepti pēc ID ar visiem saistītajiem datiem
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
        // Ielasam filtru opcijas no kešatmiņas (vai datubāzes, ja kešs nav pieejams)
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

        // Ja lietotājs nav pierakstījies, atgriežam tukšu masīvu
        if (!$userId) {
            return [];
        }

        // Ielasam lietotāja saglabāto recepšu ID sarakstu
        return Favorite::where('user_id', $userId)
            ->pluck('recipe_id')
            ->toArray();
    }

    private function applyFilters($query, array $filters): void
    {
        // Filtrējam pēc nosaukuma
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Filtrējam pēc ēdienreizes
        if (!empty($filters['meal_time'])) {
            $query->whereHas('mealTime', fn($q) => $q->where('name', $filters['meal_time']));
        }

        // Filtrējam pēc uztura veida
        if (!empty($filters['nutrition'])) {
            $query->whereHas('nutritionType', fn($q) => $q->where('name', $filters['nutrition']));
        }

        // Filtrējam pēc olbaltumvielu avota
        if (!empty($filters['protein_source'])) {
            $query->whereHas('proteinSource', fn($q) => $q->where('name', $filters['protein_source']));
        }

        // Filtrējam pēc diētas veida
        if (!empty($filters['diet_type'])) {
            $query->whereHas('dietType', fn($q) => $q->where('name', $filters['diet_type']));
        }

        // Filtrējam pēc grūtības pakāpes
        if (!empty($filters['difficulty'])) {
            $query->whereHas('difficultyLevel', fn($q) => $q->where('name', $filters['difficulty']));
        }

        // Izslēdzam receptes ar aizliegtajām sastāvdaļām atbilstoši lietotāja preferencēm
        if (!empty($filters['filter_by_preferences']) && Auth::check()) {
            $user = Auth::user()->load(['dietaryRestrictions.restrictedIngredients', 'allergies.allergicIngredients']);
            $forbiddenIds = $user->getForbiddenIngredientIds();

            if (!empty($forbiddenIds)) {
                $query->whereDoesntHave('ingredients', function($q) use ($forbiddenIds) {
                    $q->whereIn('ingredients.id', $forbiddenIds);
                });
            }
        }

        // Rādām tikai lietotāja saglabātās receptes
        if (!empty($filters['favorites_only']) && Auth::check()) {
            $favoriteIds = $this->getUserFavoriteIds(Auth::id());
            $query->whereIn('id', $favoriteIds);
        }
    }

    public function generateRecipePdf(int $recipeId)
    {
        // Ielasam recepti ar visiem PDF renderēšanai nepieciešamajiem datiem
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

        // Ielasam mērvienību nosaukumus no kešatmiņas
        $unitNames = Cache::remember('units_map', 3600, fn() => Unit::pluck('name', 'id'));

        // Ģenerējam PDF no Blade šablona
        $pdf = Pdf::loadView('pdfs.recipe', compact('recipe', 'unitNames'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }
}
