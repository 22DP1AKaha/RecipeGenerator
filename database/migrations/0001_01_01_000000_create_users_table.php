<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) USERS (LIETOTAJS) TABLE
        Schema::create('users', function (Blueprint $table) {
            // Primary key changed to user_id
            $table->id('user_id'); // Ensure this matches the model primary key

            // Your custom columns
            $table->string('epasts')->unique();
            $table->string('parole_hash');
            $table->date('registracijas_datums');
            $table->dateTime('pedeja_pieteiksanas')->nullable();
            $table->json('dietas_ierobezojumi')->nullable();
            $table->json('alergijas')->nullable();

            // Laravel-specific columns
            $table->rememberToken();
            $table->timestamps();
        });

        // 2) PASSWORD RESET TOKENS TABLE
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3) SESSIONS TABLE
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // Foreign key to the users table using user_id
            $table->foreignId('user_id')
                  ->nullable()
                  ->index();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            // Foreign key constraint for user_id
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};

