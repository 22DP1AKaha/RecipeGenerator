<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'receptes_id' => 'required|exists:recipes,id',
            'vertejums'   => 'required|integer|min:1|max:5',
            'komentars'   => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();

        $komentars = $data['komentars'] ?? null;

        $rating = Rating::updateOrCreate(
            ['user_id' => $data['user_id'], 'receptes_id' => $data['receptes_id']],
            ['vertejums' => $data['vertejums'], 'komentars' => $komentars]
        );

        // Calculate new average rating
        $average = Rating::where('receptes_id', $data['receptes_id'])
            ->avg('vertejums');

        return response()->json([
            'rating' => $rating,
            'average' => (float) number_format($average, 1)
        ], 201);
    }
}