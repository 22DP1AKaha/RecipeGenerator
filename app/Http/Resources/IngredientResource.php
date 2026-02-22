<?php

namespace App\Http\Resources;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class IngredientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category?->name,
            'quantity' => $this->when(isset($this->pivot), function () {
                $qty = $this->pivot->quantity;
                $units = Cache::remember('units_map', 3600, fn() => Unit::pluck('name', 'id'));
                $unit = $units[$this->pivot->unit_id] ?? null;
                return $unit ? "{$qty} {$unit}" : (string) $qty;
            }),
        ];
    }
}
