<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add new fields that were missing (title and description)
     */
    public function up(): void
    {
        // Add description field to stores if it doesn't exist
        if (!Schema::hasColumn('stores', 'description')) {
            Schema::table('stores', function (Blueprint $table) {
                $table->text('description')->nullable()->after('category');
            });
        }

        // Add title field to promotions if it doesn't exist
        if (!Schema::hasColumn('promotions', 'title')) {
            Schema::table('promotions', function (Blueprint $table) {
                $table->string('title', 100)->nullable()->after('code');
            });
        }

        // Add title field to news if it doesn't exist
        if (!Schema::hasColumn('news', 'title')) {
            Schema::table('news', function (Blueprint $table) {
                $table->string('title', 100)->nullable()->after('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            if (Schema::hasColumn('news', 'title')) {
                $table->dropColumn('title');
            }
        });

        Schema::table('promotions', function (Blueprint $table) {
            if (Schema::hasColumn('promotions', 'title')) {
                $table->dropColumn('title');
            }
        });

        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
