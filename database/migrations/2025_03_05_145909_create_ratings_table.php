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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lietotaja_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receptes_id')->constrained('recipes')->onDelete('cascade');
            $table->integer('vertejums')->check('vertejums BETWEEN 1 AND 5');
            $table->text('komentars')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};