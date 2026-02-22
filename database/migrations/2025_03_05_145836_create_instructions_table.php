<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->unsignedSmallInteger('step_number');
            $table->text('description');
            $table->unique(['recipe_id', 'step_number']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructions');
    }
};
