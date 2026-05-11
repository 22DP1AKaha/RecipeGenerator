<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DietType;
use App\Models\DifficultyLevel;
use App\Models\Image;
use App\Models\Ingredient;
use App\Models\MealTime;
use App\Models\NutritionType;
use App\Models\ProteinSource;
use App\Models\Recipe;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminRecipeController extends Controller // Šeit notiek visa recepšu administrēšana
{
    public function index(Request $request)
    {
        // Sākumā paņemam receptes kopā ar visu saistīto, lai pēc tam neslogotu DB ar n+1
        $query = Recipe::with([
            'difficultyLevel',
            'mealTime',
            'nutritionType',
            'dietType',
            'proteinSource',
            'ingredients',
            'instructions' => fn($q) => $q->orderBy('step_number'),
            'images',
        ]);

        // Ja lietotājs kaut ko meklē vai filtrē ņemam to vērā
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('meal_time_id')) {
            $query->where('meal_time_id', $request->meal_time_id);
        }

        if ($request->filled('nutrition_type_id')) {
            $query->where('nutrition_type_id', $request->nutrition_type_id);
        }

        if ($request->filled('protein_source_id')) {
            $query->where('protein_source_id', $request->protein_source_id);
        }

        // Pārbaudām, vai sortēšanas lauks ir atļauts
        $allowedSorts = ['name', 'cooking_time', 'created_at'];
        $sortBy  = in_array($request->get('sort_by'), $allowedSorts) ? $request->get('sort_by') : 'name';
        $sortDir = $request->get('sort_direction') === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortBy, $sortDir);

        // Pa 12 receptēm uz lapu; withQueryString lai filtri saglabājas, kad pārslēdzas lapas
        $recipes = $query->paginate(12)->withQueryString();

        // Sūtam visu uz frontu - gan pašas receptes, gan filtru opcijas
        return Inertia::render('Admin/Receptes', [
            'recipes'          => $recipes,
            'difficultyLevels' => DifficultyLevel::all(),
            'mealTimes'        => MealTime::all(),
            'nutritionTypes'   => NutritionType::all(),
            'dietTypes'        => DietType::all(),
            'proteinSources'   => ProteinSource::all(),
            'ingredients'      => Ingredient::orderBy('name')->get(),
            'units'            => Unit::all(),
            'filters'          => $request->only(['search', 'meal_time_id', 'nutrition_type_id', 'protein_source_id', 'sort_by', 'sort_direction']),
        ]);
    }

    public function store(Request $request)
    {
        // Pārbaudām ievadīto info pirms saglabāšanas
        $data = $request->validate([
            'name'                        => 'required|string|max:255',
            'description'                 => 'required|string',
            'cooking_time'                => 'required|integer|min:1',
            'difficulty_level_id'         => 'required|exists:difficulty_levels,id',
            'meal_time_id'                => 'required|exists:meal_times,id',
            'nutrition_type_id'           => 'required|exists:nutrition_types,id',
            'diet_type_id'                => 'required|exists:diet_types,id',
            'protein_source_id'           => 'nullable|exists:protein_sources,id',
            'is_public'                   => 'boolean',
            'ingredients'                 => 'array',
            'ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity'      => 'required|numeric|min:0',
            'ingredients.*.unit_id'       => 'required|exists:units,id',
            'instructions'                => 'array',
            'instructions.*.description'  => 'required|string',
            'images'                      => 'nullable|array|max:5',
            'images.*'                    => 'image|max:5120',
        ]);

        // Vispirms pati recepte
        $recipe = Recipe::create([
            'name'               => $data['name'],
            'description'        => $data['description'],
            'cooking_time'       => $data['cooking_time'],
            'difficulty_level_id'=> $data['difficulty_level_id'],
            'meal_time_id'       => $data['meal_time_id'],
            'nutrition_type_id'  => $data['nutrition_type_id'],
            'diet_type_id'       => $data['diet_type_id'],
            'protein_source_id'  => $data['protein_source_id'] ?? null,
            'is_public'          => $data['is_public'] ?? true,
        ]);

        // Tagad sastāvdaļas, katrai jāzina cik daudz un kādā mērvienībā
        foreach ($data['ingredients'] ?? [] as $ing) {
            $recipe->ingredients()->attach($ing['ingredient_id'], [
                'quantity' => $ing['quantity'],
                'unit_id'  => $ing['unit_id'],
            ]);
        }

        // Soļus numurējam automātiski pēc kārtas
        foreach ($data['instructions'] ?? [] as $i => $step) {
            $recipe->instructions()->create([
                'step_number' => $i + 1,
                'description' => $step['description'],
            ]);
        }

        // Bildes glabājam datubāzē base64 formātā
        foreach ($request->file('images') ?? [] as $file) {
            $recipe->images()->create([
                'base64_data'       => base64_encode($file->get()),
                'mime_type'         => $file->getMimeType(),
                'original_filename' => $file->getClientOriginalName(),
                'file_size'         => $file->getSize(),
            ]);
        }

        return redirect()->route('admin.recipes.index')
            ->with('status', 'recepte-pievienota');
    }

    public function update(Request $request, Recipe $recipe)
    {
        // Tādi paši validācijas noteikumi kā veidojot, plus ID dzēšamajām bildēm
        $data = $request->validate([
            'name'                        => 'required|string|max:255',
            'description'                 => 'required|string',
            'cooking_time'                => 'required|integer|min:1',
            'difficulty_level_id'         => 'required|exists:difficulty_levels,id',
            'meal_time_id'                => 'required|exists:meal_times,id',
            'nutrition_type_id'           => 'required|exists:nutrition_types,id',
            'diet_type_id'                => 'required|exists:diet_types,id',
            'protein_source_id'           => 'nullable|exists:protein_sources,id',
            'is_public'                   => 'boolean',
            'ingredients'                 => 'array',
            'ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity'      => 'required|numeric|min:0',
            'ingredients.*.unit_id'       => 'required|exists:units,id',
            'instructions'                => 'array',
            'instructions.*.description'  => 'required|string',
            'images'                      => 'nullable|array|max:5',
            'images.*'                    => 'image|max:5120',
            'deleted_image_ids'           => 'nullable|array',
            'deleted_image_ids.*'         => 'integer',
        ]);

        // Vispirms atjauninām pamata info
        $recipe->update([
            'name'               => $data['name'],
            'description'        => $data['description'],
            'cooking_time'       => $data['cooking_time'],
            'difficulty_level_id'=> $data['difficulty_level_id'],
            'meal_time_id'       => $data['meal_time_id'],
            'nutrition_type_id'  => $data['nutrition_type_id'],
            'diet_type_id'       => $data['diet_type_id'],
            'protein_source_id'  => $data['protein_source_id'] ?? null,
            'is_public'          => $data['is_public'] ?? true,
        ]);

        // Sastāvdaļas pārtaisām pilnībā, sync paņem ko atstāt un ko izsviest
        $syncData = [];
        foreach ($data['ingredients'] ?? [] as $ing) {
            $syncData[$ing['ingredient_id']] = [
                'quantity' => $ing['quantity'],
                'unit_id'  => $ing['unit_id'],
            ];
        }
        $recipe->ingredients()->sync($syncData);

        // Soļiem nav īpaši ko atjaunināt, vienkāršāk dzēst un izveidot no jauna
        $recipe->instructions()->delete();
        foreach ($data['instructions'] ?? [] as $i => $step) {
            $recipe->instructions()->create([
                'step_number' => $i + 1,
                'description' => $step['description'],
            ]);
        }

        // Ja lietotājs grib kādas bildes izņemt
        if (!empty($data['deleted_image_ids'])) {
            $recipe->images()->whereIn('id', $data['deleted_image_ids'])->delete();
        }

        // Un pievienojam jaunās, ja tādas ir
        foreach ($request->file('images') ?? [] as $file) {
            $recipe->images()->create([
                'base64_data'       => base64_encode($file->get()),
                'mime_type'         => $file->getMimeType(),
                'original_filename' => $file->getClientOriginalName(),
                'file_size'         => $file->getSize(),
            ]);
        }

        return redirect()->route('admin.recipes.index')
            ->with('status', 'recepte-atjauninata');
    }

    public function destroy(Recipe $recipe)
    {
        // dzēš no DB
        $recipe->delete();

        return redirect()->route('admin.recipes.index')
            ->with('status', 'recepte-dzesta');
    }
}
