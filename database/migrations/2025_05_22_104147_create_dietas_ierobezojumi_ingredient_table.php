<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredient_dietary_restriction', function (Blueprint $table) {
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->foreignId('dietary_restriction_id')->constrained('dietary_restrictions')->onDelete('cascade');
            $table->primary(['ingredient_id', 'dietary_restriction_id'], 'ingredient_dietary_restriction_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_dietary_restriction');
    }
};
