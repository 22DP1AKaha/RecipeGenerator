<?php

// app/Models/Alergijas.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alergijas extends Model
{
    protected $table = 'alergijas';
    protected $primaryKey = 'alergijas_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['nosaukums'];


    public function run()
    {
        $allergies = [
            'Rieksti' => [
                'Zemesriekstu sviests', // While technically a legume, often grouped with nuts for allergies
                'Kokosriekstu eļļa',     // Sometimes flagged for nut allergies
                'Kokosriekstu cukurs',
                'Kokosriekstu ūdens',
                'Kokosriekstu piens',
            ],
            'Piens' => [
                'Piens',
                'Siers',
                'Jogurts',
                'Krējums',
                'Biezpiens',
                'Sviests',
                'Kefīrs',
                'Saldais krējums',
                'Siera krems',
            ],
            'Olas' => [
                'Olas',
            ],
            'Soja' => [
                'Tofu',
                'Tempe',
            ],
            'Gliemenes' => [
                // No matching ingredients in your list, unless you add seafood like 'Mīdijas', 'Austeres', etc.
            ],
            'Kvieši' => [
                'Kvieši',
                'Milti',
                'Baltmaize',
                'Rudzu maize',
                'Makaroni',
                'Cepamais pulveris', // Often contains wheat starch or flour as filler
            ],
            'Zivis' => [
                'Zivis',
            ],
            'Sezama sēklas' => [
                // No explicit ingredient, but may be included in unlisted ingredients like "tahini" or certain oils
            ],
            'Selerijas' => [
                // No matching ingredient found
            ],
        ];

        foreach ($allergies as $allergyName => $ingredients) {
            $allergy = Alergijas::firstOrCreate(['nosaukums' => $allergyName]);
            $allergy->allergicIngredients()->sync(
                Ingredient::whereIn('nosaukums', $ingredients)->pluck('id')
            );
        }
    }

    public function users()
    {
        return $this->belongsToMany(
            \app\Models\User::class,
            'alergijas_user',            // Pivot table
            'alergijas_id',              // Foreign key in the pivot table
            'user_id'                    // Related foreign key
        );
    }

    public function allergicIngredients()
    {
        return $this->belongsToMany(Ingredient::class, 'alergijas_ingredient', 'alergijas_id', 'ingredient_id');
    }

}
