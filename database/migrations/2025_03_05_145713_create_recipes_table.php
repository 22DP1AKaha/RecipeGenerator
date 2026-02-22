<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->unsignedSmallInteger('cooking_time');
            $table->foreignId('difficulty_level_id')->constrained('difficulty_levels')->onDelete('cascade');
            $table->foreignId('meal_time_id')->constrained('meal_times')->onDelete('cascade');
            $table->foreignId('nutrition_type_id')->constrained('nutrition_types')->onDelete('cascade');
            $table->foreignId('diet_type_id')->constrained('diet_types')->onDelete('cascade');
            $table->foreignId('protein_source_id')->nullable()->constrained('protein_sources')->onDelete('cascade');
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
