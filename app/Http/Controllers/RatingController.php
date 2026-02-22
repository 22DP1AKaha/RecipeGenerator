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
        $data = $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string',
        ]);

        $rating = Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'recipe_id' => $data['recipe_id']],
            ['rating' => $data['rating'], 'comment' => $data['comment'] ?? null]
        );

        $average = Rating::where('recipe_id', $data['recipe_id'])->avg('rating');

        return response()->json([
            'rating' => $rating,
            'average' => (float) number_format($average, 1)
        ], 201);
    }
}
