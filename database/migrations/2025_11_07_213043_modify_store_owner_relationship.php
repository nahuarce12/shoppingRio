<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Change relationship: 
     * - Remove owner_id from stores table
     * - Add store_id to users table (for tipo_usuario = 'dueño de local')
     * - A store can have many owners, an owner has one store
     */
    public function up(): void
    {
        // Remove owner_id from stores
        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropIndex(['owner_id']);
            $table->dropColumn('owner_id');
        });

        // Add store_id to users (only for store owners)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('store_id')
                ->nullable()
                ->after('user_type')
                ->constrained('stores')
                ->onDelete('cascade');
            
            $table->index('store_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore original structure
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropIndex(['store_id']);
            $table->dropColumn('store_id');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->foreignId('owner_id')
                ->after('category')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->index('owner_id');
        });
    }
};
