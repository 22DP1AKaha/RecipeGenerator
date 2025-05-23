<?php

// app/Models/DietasIerobezojumi.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DietasIerobezojumi extends Model
{
    protected $table = 'dietas_ierobezojumi';
    protected $primaryKey = 'dietas_ierobezojumi_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['nosaukums'];

    public function users()
    {
        return $this->belongsToMany(
            \app\Models\User::class,
            'dietas_ierobezojumi_user', // Pivot table
            'dietas_ierobezojumi_id',    // Foreign key in the pivot table
            'user_id'                    // Related foreign key
        );
    }

    public function restrictedIngredients()
    {
        return $this->belongsToMany(Ingredient::class, 'dietas_ierobezojumi_ingredient', 'dietas_ierobezojumi_id', 'ingredient_id');
    }

    public function run()
    {
        $diets = [
            'Bezglutēna' => [
                'Kvieši', 'Milti', 'Makaroni',
                'Baltmaize', 'Rudzu maize',
                'Mieži', 'Auzu pārslas'
            ],

            'Bez piena produktiem' => [
                'Siers', 'Piens', 'Jogurts',
                'Krējums', 'Biezpiens',
                'Sviests', 'Kefīrs',
                'Saldais krējums', 'Siera krems'
            ],

            'Veģetāra' => [
                'Vistas gaļa', 'Liellopu gaļa',
                'Zivis', 'Cūkgaļa', 'Jēra gaļa',
                'Vistas desa', 'Vistas fileja',
                'Maltā gaļa'
            ],

            'Vegāna' => [
                'Olas', 'Vistas gaļa', 'Liellopu gaļa',
                'Zivis', 'Cūkgaļa', 'Jēra gaļa',
                'Vistas desa', 'Vistas fileja',
                'Maltā gaļa', 'Siers', 'Piens',
                'Jogurts', 'Krējums', 'Biezpiens',
                'Sviests', 'Kefīrs', 'Saldais krējums',
                'Siera krems', 'Medus'
            ],

            'Zema ogļhidrātu' => [
                'Rīsi', 'Makaroni', 'Kvinoja',
                'Auzu pārslas', 'Milti', 'Baltmaize',
                'Rudzu maize', 'Griķi', 'Kartupelī',
                'Kukurūza', 'Burkāns', 'Banāns',
                'Vīnogas'
            ],

            'Zema tauku satura' => [
                'Sviests', 'Krējums', 'Biezpiens',
                'Sviests', 'Olīveļļa', 'Augu eļļa',
                'Kokosriekstu eļļa', 'Sēklu eļļa',
                'Rapsu eļļa', 'Zemesriekstu sviests',
                'Linsēklu eļļa', 'Saulespuķu eļļa',
                'Kokosriekstu piens'
            ],

            'Ketogēnā' => [
                'Rīsi', 'Makaroni', 'Kvieši',
                'Milti', 'Baltmaize', 'Rudzu maize',
                'Griķi', 'Kartupelī', 'Kukurūza',
                'Banāns', 'Apelsīns', 'Mandarīns',
                'Ananāss', 'Mango', 'Vīnogas',
                'Burkāns'
            ],

            'Paleo' => [
                // graudi
                'Kvieši', 'Milti', 'Makaroni',
                'Baltmaize', 'Rudzu maize',
                'Mieži', 'Auzu pārslas',
                'Kvinoja', 'Lēcas', 'Griķi',
                // piena produkti
                'Siers', 'Piens', 'Jogurts',
                'Krējums', 'Biezpiens',
                'Sviests', 'Kefīrs',
                'Saldais krējums', 'Siera krems'
            ],

            'Bez cukura' => [
                'Cukurs', 'Medus', 'Kokosriekstu cukurs',
                'Stevija', 'Kļavu sīrups', 'Agaves sīrups',
                'Bērzu sīrups', 'Rīsu sīrups'
            ],
        ];


        foreach ($diets as $dietName => $ingredients) {
            $diet = DietasIerobezojumi::firstOrCreate(['nosaukums' => $dietName]);
            $diet->restrictedIngredients()->sync(
                Ingredient::whereIn('nosaukums', $ingredients)->pluck('id')
            );
        }
    }
}

