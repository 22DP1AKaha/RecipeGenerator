<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'description' => $this->when($request->routeIs('*.show'), $this->description),
            'image' => $this->image?->url,
            'images' => $this->when(
                $request->routeIs('*.show') && $this->relationLoaded('images'),
                fn() => $this->images->map(fn($img) => [
                    'id'  => $img->id,
                    'url' => $img->url,
                ])->values()
            ),
            'cooking_time' => $this->cooking_time,
            'difficulty' => $this->difficultyLevel?->name,
            'meal_time' => $this->mealTime?->name,
            'nutrition' => $this->nutritionType?->name,
            'diet_type' => $this->dietType?->name,
            'protein_source' => $this->proteinSource?->name,
            'average_rating' => round((float) ($this->average_rating ?? 0), 1),
            'user_rating' => auth()->check() && $this->relationLoaded('ratings')
                ? (int) ($this->ratings->where('user_id', auth()->id())->first()?->rating ?? 0)
                : 0,
            'is_saved' => auth()->check() && $this->relationLoaded('favorites')
                ? $this->favorites->where('user_id', auth()->id())->isNotEmpty()
                : false,
            'reviews' => $this->when(
                $request->routeIs('*.show') && $this->relationLoaded('ratings'),
                fn() => $this->ratings
                    ->whereNotNull('comment')
                    ->where('comment', '!=', '')
                    ->sortByDesc('created_at')
                    ->map(fn($r) => [
                        'user'       => $r->user?->vards ?? 'Anonīms',
                        'rating'     => $r->rating,
                        'comment'    => $r->comment,
                        'created_at' => $r->created_at?->format('d.m.Y'),
                        'is_own'     => auth()->id() === $r->user_id,
                    ])->values()
            ),
            'ingredients' => IngredientResource::collection($this->whenLoaded('ingredients')),
            'instructions' => $this->when(
                $request->routeIs('*.show') && $this->relationLoaded('instructions'),
                fn() => $this->instructions->map(fn($inst) => [
                    'step_number' => $inst->step_number,
                    'description' => $inst->description
                ])
            ),
        ];
    }
}
