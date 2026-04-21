<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecipeResource;
use App\Models\Image;
use App\Services\RecipeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                'search',
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

    public function serveImage($id)
    {
        $image = Image::findOrFail($id);

        return response($image->base64_data_raw, 200)
            ->header('Content-Type', $image->mime_type)
            ->header('Cache-Control', 'public, max-age=31536000, immutable');
    }

    public function downloadPdf($id)
    {
        try {
            $pdf   = $this->recipeService->generateRecipePdf($id);
            $token = (string) Str::uuid();

            Storage::put("pdf_temp/{$token}.pdf", $pdf->output());

            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            Log::error('PDF generation error: ' . $e->getMessage());
            return response()->json(['error' => 'Kļūda ģenerējot PDF'], 500);
        }
    }

    public function servePdf(Request $request, string $token)
    {
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $token)) {
            abort(404);
        }

        $path = Storage::path("pdf_temp/{$token}.pdf");

        if (!file_exists($path) || filemtime($path) < time() - 600) {
            @unlink($path);
            abort(404);
        }

        $raw      = $request->query('fn', 'dokuments.pdf');
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '-', $raw);
        if (!str_ends_with(strtolower($filename), '.pdf')) {
            $filename .= '.pdf';
        }

        return response()->download($path, $filename, [
            'Content-Type'  => 'application/pdf',
            'Cache-Control' => 'no-store',
        ])->deleteFileAfterSend(true);
    }
}
