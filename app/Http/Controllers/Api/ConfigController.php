<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function getAppConfig()
    {
        return response()->json([
            'portionSizes' => [
                ['value' => 1, 'label' => '1 porcija'],
                ['value' => 2, 'label' => '2 porcijas'],
                ['value' => 3, 'label' => '3 porcijas'],
                ['value' => 4, 'label' => '4 porcijas'],
                ['value' => 5, 'label' => '5 porcijas'],
            ],
            'sortOptions' => [
                ['value' => 'rating', 'label' => 'Vērtējuma'],
                ['value' => 'difficulty', 'label' => 'Grūtības pakāpes'],
                ['value' => 'cooking_time', 'label' => 'Gatavošanas laika'],
            ],
            'sortDirections' => [
                ['value' => 'asc', 'label' => 'Augoši (A-Z)'],
                ['value' => 'desc', 'label' => 'Dilstoši (Z-A)'],
            ],
            'filterLabels' => [
                'allMealTimes' => 'Visas edienreizes',
                'allNutritionTypes' => 'Visi uztura veidi',
                'allProteinSources' => 'Visi olbaltumvielu avoti',
                'clearFilters' => 'Notīrīt filtrus',
                'sortBy' => 'Kārtot pēc...',
                'searchPlaceholder' => 'Meklēt recepti...',
            ],
        ]);
    }
}
