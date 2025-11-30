<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->decimal('quantity_numeric', 8, 2)->nullable()->after('quantity');
            $table->string('unit', 20)->nullable()->after('quantity_numeric');
            $table->text('notes')->nullable()->after('unit');
        });
    }

    public function down(): void
    {
        Schema::table('recipe_ingredients', function (Blueprint $table) {
            $table->dropColumn(['quantity_numeric', 'unit', 'notes']);
        });
    }
};
