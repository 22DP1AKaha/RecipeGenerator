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
        Schema::create('alergijas_ingredient', function (Blueprint $table) {
            $table->foreignId('alergijas_id')
                ->constrained('alergijas', 'alergijas_id')
                ->onDelete('cascade');
            $table->foreignId('ingredient_id')
                ->constrained()
                ->onDelete('cascade');
            $table->primary(['alergijas_id', 'ingredient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alergijas_ingredient');
    }
};
