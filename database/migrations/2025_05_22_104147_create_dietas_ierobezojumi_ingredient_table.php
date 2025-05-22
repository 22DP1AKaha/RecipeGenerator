<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dietas_ierobezojumi_ingredient', function (Blueprint $table) {
            $table->foreignId('dietas_ierobezojumi_id')
                ->constrained('dietas_ierobezojumi', 'dietas_ierobezojumi_id')
                ->onDelete('cascade');
            $table->foreignId('ingredient_id')
                ->constrained()
                ->onDelete('cascade');
            $table->primary(['dietas_ierobezojumi_id', 'ingredient_id']);
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dietas_ierobezojumi_ingredient');
    }
};
