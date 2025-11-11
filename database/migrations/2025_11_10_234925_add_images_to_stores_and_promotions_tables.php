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
        // Add logo column to stores table
        Schema::table('stores', function (Blueprint $table) {
            $table->string('logo', 255)->nullable()->after('rubro');
        });

        // Add imagen column to promotions table
        Schema::table('promotions', function (Blueprint $table) {
            $table->string('imagen', 255)->nullable()->after('categoria_minima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('logo');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('imagen');
        });
    }
};
