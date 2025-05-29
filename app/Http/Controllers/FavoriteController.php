<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    // Store a new favorite
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'receptes_id' => 'required|exists:recipes,id',
            ]);
            
            $userId = Auth::user()->user_id;
            
            // Check if already exists
            $exists = Favorite::where('user_id', $userId)
                ->where('receptes_id', $data['receptes_id'])
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'error' => 'Recipe is already saved'
                ], 409);
            }
            
            Favorite::create([
                'user_id'     => $userId,
                'receptes_id' => $data['receptes_id'],
            ]);
            
            return response()->json(['saved' => true], 201);
            
        } catch (\Exception $e) {
            Log::error('Favorite store error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // Remove a favorite
    public function destroy($recipeId)
    {
        try {
            $userId = Auth::user()->user_id;
            
            $favorite = Favorite::where('user_id', $userId)
                ->where('receptes_id', $recipeId)
                ->first();
                
            if (!$favorite) {
                return response()->json([
                    'error' => 'Favorite not found'
                ], 404);
            }
            
            $favorite->delete();
            
            return response()->json(['saved' => false], 200);
            
        } catch (\Exception $e) {
            Log::error('Favorite destroy error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}