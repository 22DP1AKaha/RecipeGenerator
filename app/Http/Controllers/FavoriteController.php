<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validējam pieprasījuma datus
            $data = $request->validate([
                'recipe_id' => 'required|exists:recipes,id',
            ]);

            $userId = Auth::id();

            // Pārbaudām vai recepte jau ir saglabāta
            $exists = Favorite::where('user_id', $userId)
                ->where('recipe_id', $data['recipe_id'])
                ->exists();

            if ($exists) {
                return response()->json(['error' => 'Recipe is already saved'], 409);
            }

            // Pievienojam recepti favorītiem
            Favorite::create([
                'user_id'   => $userId,
                'recipe_id' => $data['recipe_id'],
            ]);

            return response()->json(['saved' => true, 'has_favorites' => true], 201);

        } catch (\Exception $e) {
            Log::error('Favorite store error: ' . $e->getMessage());
            return response()->json([
                'error'   => 'Server error',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($recipeId)
    {
        try {
            $userId = Auth::id();

            // Meklējam favorītu ierakstu
            $favorite = Favorite::where('user_id', $userId)
                ->where('recipe_id', $recipeId)
                ->first();

            if (!$favorite) {
                return response()->json(['error' => 'Favorite not found'], 404);
            }

            // Dzēšam recepti no favorītiem
            $favorite->delete();

            // Pārbaudām vai lietotājam vēl ir saglabātas receptes
            $hasFavorites = Favorite::where('user_id', $userId)->exists();

            return response()->json(['saved' => false, 'has_favorites' => $hasFavorites], 200);

        } catch (\Exception $e) {
            Log::error('Favorite destroy error: ' . $e->getMessage());
            return response()->json([
                'error'   => 'Server error',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
