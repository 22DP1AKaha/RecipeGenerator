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
        Schema::create('dietary_restriction_ingredient', function (Blueprint $table) {
            $table->foreignId('dietary_restriction_id')
                ->constrained('dietary_restrictions')
                ->onDelete('cascade');
            $table->foreignId('ingredient_id')
                ->constrained()
                ->onDelete('cascade');
            $table->primary(['dietary_restriction_id', 'ingredient_id'], 'dietary_restriction_ingredient_primary');
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dietary_restriction_ingredient');
    }
};
