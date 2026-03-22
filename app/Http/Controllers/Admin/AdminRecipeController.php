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

class AdminRecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with([
            'difficultyLevel',
            'mealTime',
            'nutritionType',
            'dietType',
            'proteinSource',
            'ingredients',
            'instructions' => fn($q) => $q->orderBy('step_number'),
            'images',
        ])->orderBy('name')->get();

        return Inertia::render('Admin/Receptes', [
            'recipes'          => $recipes,
            'difficultyLevels' => DifficultyLevel::all(),
            'mealTimes'        => MealTime::all(),
            'nutritionTypes'   => NutritionType::all(),
            'dietTypes'        => DietType::all(),
            'proteinSources'   => ProteinSource::all(),
            'ingredients'      => Ingredient::orderBy('name')->get(),
            'units'            => Unit::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'description'        => 'required|string',
            'cooking_time'       => 'required|integer|min:1',
            'difficulty_level_id'=> 'required|exists:difficulty_levels,id',
            'meal_time_id'       => 'required|exists:meal_times,id',
            'nutrition_type_id'  => 'required|exists:nutrition_types,id',
            'diet_type_id'       => 'required|exists:diet_types,id',
            'protein_source_id'  => 'nullable|exists:protein_sources,id',
            'is_public'          => 'boolean',
            'ingredients'        => 'array',
            'ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity'      => 'required|numeric|min:0',
            'ingredients.*.unit_id'       => 'required|exists:units,id',
            'instructions'       => 'array',
            'instructions.*.description'  => 'required|string',
            'images'             => 'nullable|array|max:5',
            'images.*'           => 'image|max:5120',
        ]);

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

        foreach ($data['ingredients'] ?? [] as $ing) {
            $recipe->ingredients()->attach($ing['ingredient_id'], [
                'quantity' => $ing['quantity'],
                'unit_id'  => $ing['unit_id'],
            ]);
        }

        foreach ($data['instructions'] ?? [] as $i => $step) {
            $recipe->instructions()->create([
                'step_number' => $i + 1,
                'description' => $step['description'],
            ]);
        }

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
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'description'        => 'required|string',
            'cooking_time'       => 'required|integer|min:1',
            'difficulty_level_id'=> 'required|exists:difficulty_levels,id',
            'meal_time_id'       => 'required|exists:meal_times,id',
            'nutrition_type_id'  => 'required|exists:nutrition_types,id',
            'diet_type_id'       => 'required|exists:diet_types,id',
            'protein_source_id'  => 'nullable|exists:protein_sources,id',
            'is_public'          => 'boolean',
            'ingredients'        => 'array',
            'ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity'      => 'required|numeric|min:0',
            'ingredients.*.unit_id'       => 'required|exists:units,id',
            'instructions'       => 'array',
            'instructions.*.description'  => 'required|string',
            'images'             => 'nullable|array|max:5',
            'images.*'           => 'image|max:5120',
            'deleted_image_ids'  => 'nullable|array',
            'deleted_image_ids.*'=> 'integer',
        ]);

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

        $syncData = [];
        foreach ($data['ingredients'] ?? [] as $ing) {
            $syncData[$ing['ingredient_id']] = [
                'quantity' => $ing['quantity'],
                'unit_id'  => $ing['unit_id'],
            ];
        }
        $recipe->ingredients()->sync($syncData);

        $recipe->instructions()->delete();
        foreach ($data['instructions'] ?? [] as $i => $step) {
            $recipe->instructions()->create([
                'step_number' => $i + 1,
                'description' => $step['description'],
            ]);
        }

        if (!empty($data['deleted_image_ids'])) {
            $recipe->images()->whereIn('id', $data['deleted_image_ids'])->delete();
        }

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
        $recipe->delete();

        return redirect()->route('admin.recipes.index')
            ->with('status', 'recepte-dzesta');
    }
}
