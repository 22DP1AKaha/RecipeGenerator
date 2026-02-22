<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredient_allergy', function (Blueprint $table) {
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->foreignId('allergy_id')->constrained('allergies')->onDelete('cascade');
            $table->primary(['ingredient_id', 'allergy_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_allergy');
    }
};
