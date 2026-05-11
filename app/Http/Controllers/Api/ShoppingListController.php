<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShoppingListController extends Controller // Ģenerē iepirkumu sarakstus no izvēlētajām receptēm
{
    public function generate(Request $request)
    {
        $request->validate([
            'recipes'             => 'required|array|min:1|max:20',
            'recipes.*.recipe_id' => 'required|integer|exists:recipes,id',
            'recipes.*.portions'  => 'required|numeric|min:0.5|max:100',
        ]);

        // Apvienojam visu vajadzīgo vienā sarakstā
        $list = $this->buildList($request->input('recipes'));

        return response()->json(['list' => $list]);
    }

    public function downloadPdf(Request $request)
    {
        $request->validate([
            'recipes'             => 'required|array|min:1|max:20',
            'recipes.*.recipe_id' => 'required|integer|exists:recipes,id',
            'recipes.*.portions'  => 'required|numeric|min:0.5|max:100',
        ]);

        try {
            $recipeItems = $request->input('recipes');
            $list        = $this->buildList($recipeItems);

            // PDFā vēlamies redzēt arī kuras receptes ir iekļautas
            $recipeIds   = collect($recipeItems)->pluck('recipe_id');
            $recipes     = Recipe::whereIn('id', $recipeIds)->get(['id', 'name'])->keyBy('id');

            $recipeNames = collect($recipeItems)->map(fn($item) => [
                'name'     => $recipes[$item['recipe_id']]?->name ?? 'Nezināma recepte',
                'portions' => $item['portions'],
            ]);

            // Renderējam no Blade šablona
            $pdf = Pdf::loadView('pdfs.shopping_list', [
                'list'        => $list,
                'recipeNames' => $recipeNames,
                'generatedAt' => now()->format('d.m.Y H:i'),
            ]);

            // kodējam base64 un atgriežam uz UI, js to uzreiz lejupielādē
            return response()->json([
                'pdf'      => base64_encode($pdf->output()),
                'filename' => 'iepirkumu-saraksts.pdf',
            ]);
        } catch (\Exception $e) {
            Log::error('Shopping list PDF generation error: ' . $e->getMessage());
            return response()->json(['error' => 'Kļūda ģenerējot PDF'], 500);
        }
    }

    private function buildList(array $recipeItems): array
    {
        $recipeIds   = collect($recipeItems)->pluck('recipe_id');
        $portionsMap = collect($recipeItems)->keyBy('recipe_id')->map(fn($i) => floatval($i['portions']));

        // Visas receptes
        $recipes = Recipe::whereIn('id', $recipeIds)
            ->with(['ingredients' => fn($q) => $q->with('category')])
            ->get();

        $combined = [];

        // Reizinām ar porciju skaitu un summējam, ja sastāvdaļa atkārtojas
        foreach ($recipes as $recipe) {
            $portions = $portionsMap[$recipe->id] ?? 1;

            foreach ($recipe->ingredients as $ingredient) {
                $unitId = $ingredient->pivot->unit_id;
                $key    = $ingredient->id . '_' . $unitId;

                if (isset($combined[$key])) {
                    $combined[$key]['quantity'] += floatval($ingredient->pivot->quantity) * $portions;
                } else {
                    $combined[$key] = [
                        'name'     => $ingredient->name,
                        'category' => $ingredient->category?->name ?? 'Cits',
                        'quantity' => floatval($ingredient->pivot->quantity) * $portions,
                        'unit_id'  => $unitId,
                        'unit'     => '',
                    ];
                }
            }
        }

        // Tagad piesaistām mērvienību nosaukumus
        $unitIds = collect($combined)->pluck('unit_id')->unique()->filter();
        $units   = Unit::whereIn('id', $unitIds)->pluck('name', 'id');

        foreach ($combined as &$item) {
            $item['unit'] = $units[$item['unit_id']] ?? '';
            unset($item['unit_id']);
        }
        unset($item);

        // Beigās sagrupējam pa kategorijām
        return collect($combined)
            ->groupBy('category')
            ->map(fn($items, $category) => [
                'category'    => $category,
                'ingredients' => $items
                    ->map(fn($i) => [
                        'name'     => $i['name'],
                        'quantity' => round($i['quantity'], 2),
                        'unit'     => $i['unit'],
                    ])
                    ->sortBy('name')
                    ->values(),
            ])
            ->sortKeys()
            ->values()
            ->toArray();
    }
}
