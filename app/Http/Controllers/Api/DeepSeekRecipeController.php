<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIRecipeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeepSeekRecipeController extends Controller
{
    private AIRecipeGeneratorService $aiService;

    public function __construct(AIRecipeGeneratorService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generateRecipe(Request $request)
    {
        try {
            $request->validate([
                'ingredients' => 'required|string',
            ]);

            $options = [];

            if (auth()->check()) {
                $user = auth()->user()->load(['dietaryRestrictions', 'allergies']);

                if ($user->dietaryRestrictions->isNotEmpty()) {
                    $options['dietary_restrictions'] = $user->dietaryRestrictions->pluck('name')->toArray();
                }

                if ($user->allergies->isNotEmpty()) {
                    $options['allergies'] = $user->allergies->pluck('name')->toArray();
                }
            }

            $result = $this->aiService->generateRecipe($request->ingredients, $options);

            if (!$result['success']) {
                return response()->json([
                    'error' => 'Recipe generation failed',
                    'message' => $result['error'] ?? 'Unknown error'
                ], 500);
            }

            return response()->json([
                'recipe' => $result['recipe']
            ]);

        } catch (\Throwable $e) {
            Log::error('Recipe generation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}