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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('nosaukums');
            $table->text('apraksts');
            $table->integer('gatavosanas_laiks');
            $table->string('grutibas_pakape');
            $table->string('edienreize');
            $table->string('dietas_tips');
            $table->string('galvenais_olbaltumvielu_avots');
            $table->string('attels')->nullable();  // Changed from text to string
            $table->timestamp('datums_izveidots')->useCurrent();
            $table->timestamp('datums_atjauninats')->useCurrent();
            $table->boolean('ir_publiska')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};