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
        
            // Explicitly define foreign key for 'users' table using user_id
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        
            // Explicitly define foreign key for 'recipes' table
            $table->unsignedBigInteger('receptes_id');
            $table->foreign('receptes_id')->references('id')->on('recipes')->onDelete('cascade');
        
            $table->integer('vertejums');
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
