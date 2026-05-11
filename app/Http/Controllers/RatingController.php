<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller // Recepšu vērtēšana un komentāri
{
    public function store(Request $request)
    {
        // Vērtējumam jābūt no 1 līdz 5, komentārs neobligāts
        $data = $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string',
        ]);

        // updateOrCreate - ja jau bija novērtējis, atjaunina; ja nē, izveido jaunu
        $rating = Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'recipe_id' => $data['recipe_id']],
            ['rating'  => $data['rating'], 'comment' => $data['comment'] ?? null]
        );

        // Pārrēķinām vidējo, lai uzreiz var parādīt jauno vērtējumu
        $average = Rating::where('recipe_id', $data['recipe_id'])->avg('rating');

        return response()->json([
            'rating'  => $rating,
            'average' => (float) number_format($average, 1)
        ], 201);
    }

    public function destroy(int $recipeId)
    {
        // Lietotājs var atsaukt savu vērtējumu
        $deleted = Rating::where('user_id', Auth::id())
            ->where('recipe_id', $recipeId)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Vērtējums nav atrasts.'], 404);
        }

        // Bez šī vērtējuma vidējais var mainīties - pārrēķinām
        $average = Rating::where('recipe_id', $recipeId)->avg('rating');

        return response()->json([
            'average' => $average ? (float) number_format($average, 1) : 0.0
        ]);
    }
}
