<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_dietary_restrictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dietary_restriction_id')->constrained('dietary_restrictions')->cascadeOnDelete();
            $table->unique(['user_id', 'dietary_restriction_id'], 'user_dietary_restriction_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_dietary_restrictions');
    }
};
