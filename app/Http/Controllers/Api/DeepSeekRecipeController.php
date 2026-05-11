<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIRecipeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Šeit nokļūst pieprasījumi, kad lietotājs grib, lai AI izdomā recepti
class DeepSeekRecipeController extends Controller
{
    private AIRecipeGeneratorService $aiService;

    public function __construct(AIRecipeGeneratorService $aiService)
    {
        // Servisu Laravel injecto pats tā ērtāk testēt
        $this->aiService = $aiService;
    }

    public function generateRecipe(Request $request)
    {
        try {
            // Sastāvdaļas obligātas, preferences pēc vēlēšanās
            $request->validate([
                'ingredients'     => 'required|string',
                'use_preferences' => 'boolean',
            ]);

            $options = [];

            // Ja lietotājs ielogojies un grib, lai AI ņem vērā viņa diētas vai alerģijas
            if (auth()->check() && $request->boolean('use_preferences', true)) {
                $user = auth()->user()->load(['dietaryRestrictions', 'allergies']);

                // ja ir diētas, paņemam tās
                if ($user->dietaryRestrictions->isNotEmpty()) {
                    $options['dietary_restrictions'] = $user->dietaryRestrictions->pluck('name')->toArray();
                }

                // Tāpat ar alerģijām, lai AI nesabojā
                if ($user->allergies->isNotEmpty()) {
                    $options['allergies'] = $user->allergies->pluck('name')->toArray();
                }
            }

            // Pasaucam servisu un gaidām atbildi
            $result = $this->aiService->generateRecipe($request->ingredients, $options);

            // Ja kaut kas nogāja greizi - pasakām lietotājam
            if (!$result['success']) {
                return response()->json([
                    'error'   => 'Recipe generation failed',
                    'message' => $result['error'] ?? 'Unknown error'
                ], 500);
            }

            // Visi laimīgi, sūtam atpakaļ recepti
            return response()->json([
                'recipe' => $result['recipe']
            ]);

        } catch (\Throwable $e) {
            // Kaut kas pavisam slikts, rakstam logā un atbildam ar kļūdu
            Log::error('Recipe generation error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return response()->json([
                'error'   => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
