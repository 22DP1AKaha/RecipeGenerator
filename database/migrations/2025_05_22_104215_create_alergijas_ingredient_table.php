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
        Schema::create('allergy_ingredient', function (Blueprint $table) {
            $table->foreignId('allergy_id')
                ->constrained('allergies')
                ->onDelete('cascade');
            $table->foreignId('ingredient_id')
                ->constrained()
                ->onDelete('cascade');
            $table->primary(['allergy_id', 'ingredient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allergy_ingredient');
    }
};
