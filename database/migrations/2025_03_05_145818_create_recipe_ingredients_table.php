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
    Schema::create('recipe_ingredients', function (Blueprint $table) {
        $table->id();
        $table->foreignId('receptes_id')->constrained('recipes')->onDelete('cascade');
        $table->foreignId('sastavdalas_id')->constrained('ingredients')->onDelete('cascade');
        $table->string('daudzums');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};