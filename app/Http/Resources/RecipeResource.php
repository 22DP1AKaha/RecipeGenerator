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
            'image' => $this->image?->data_url,
            'cooking_time' => $this->cooking_time,
            'difficulty' => $this->difficulty,
            'meal_time' => $this->meal_time,
            'nutrition' => $this->nutrition,
            'diet_type' => $this->diet_type,
            'protein_source' => $this->protein_source,
            'average_rating' => $this->when(
                isset($this->average_rating),
                fn() => round((float) $this->average_rating, 1)
            ) ?? 0,
            'user_rating' => $this->when(
                auth()->check() && $request->routeIs('*.show'),
                fn() => (int) ($this->ratings()->where('user_id', auth()->id())->value('rating') ?? 0)
            ),
            'is_saved' => $this->when(
                auth()->check(),
                fn() => $this->favorites()->where('user_id', auth()->id())->exists()
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
