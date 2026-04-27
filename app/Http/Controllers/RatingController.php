<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        // Validējam vērtējuma datus
        $data = $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string',
        ]);

        // Saglabājam vai atjauninām lietotāja vērtējumu
        $rating = Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'recipe_id' => $data['recipe_id']],
            ['rating'  => $data['rating'], 'comment' => $data['comment'] ?? null]
        );

        // Aprēķinam jauno vidējo vērtējumu
        $average = Rating::where('recipe_id', $data['recipe_id'])->avg('rating');

        return response()->json([
            'rating'  => $rating,
            'average' => (float) number_format($average, 1)
        ], 201);
    }

    public function destroy(int $recipeId)
    {
        // Dzēšam lietotāja vērtējumu
        $deleted = Rating::where('user_id', Auth::id())
            ->where('recipe_id', $recipeId)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Vērtējums nav atrasts.'], 404);
        }

        // Aprēķinam jauno vidējo vērtējumu pēc dzēšanas
        $average = Rating::where('recipe_id', $recipeId)->avg('rating');

        return response()->json([
            'average' => $average ? (float) number_format($average, 1) : 0.0
        ]);
    }
}
