<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename all Spanish attribute names to English and add new fields
     */
    public function up(): void
    {
        // STORES TABLE - Simple renames first
        Schema::table('stores', function (Blueprint $table) {
            $table->renameColumn('code', 'code');
            $table->renameColumn('name', 'name');
            $table->renameColumn('location', 'location');
            $table->renameColumn('category', 'category');
        });
        
        Schema::table('stores', function (Blueprint $table) {
            // Add description field
            $table->text('description')->nullable()->after('category');
        });

        // PROMOTIONS TABLE - Use raw SQL for enum columns
        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('code', 'code');
            $table->renameColumn('description', 'description');
            $table->renameColumn('start_date', 'start_date');
            $table->renameColumn('end_date', 'end_date');
            $table->renameColumn('weekdays', 'weekdays');
        });
        
        // Rename enum columns using raw SQL to avoid default value issues
        DB::statement("ALTER TABLE promotions CHANGE categoria_minima minimum_category ENUM('Inicial', 'Medium', 'Premium') NOT NULL DEFAULT 'Inicial'");
        DB::statement("ALTER TABLE promotions CHANGE estado status ENUM('pendiente', 'aprobada', 'denegada') NOT NULL DEFAULT 'pendiente'");
        
        Schema::table('promotions', function (Blueprint $table) {
            // Add title field
            $table->string('title', 100)->nullable()->after('code');
        });

        // NEWS TABLE - Use raw SQL for enum
        Schema::table('news', function (Blueprint $table) {
            $table->renameColumn('code', 'code');
            $table->renameColumn('description', 'description');
            $table->renameColumn('start_date', 'start_date');
            $table->renameColumn('end_date', 'end_date');
        });
        
        // Rename enum column using raw SQL
        DB::statement("ALTER TABLE news CHANGE categoria_destino target_category ENUM('Inicial', 'Medium', 'Premium') NOT NULL DEFAULT 'Inicial'");
        
        Schema::table('news', function (Blueprint $table) {
            // Add title field
            $table->string('title', 100)->nullable()->after('code');
        });

        // PROMOTION_USAGE TABLE - Use raw SQL for enum
        Schema::table('promotion_usage', function (Blueprint $table) {
            $table->renameColumn('usage_date', 'usage_date');
        });
        
        // Rename enum column using raw SQL
        DB::statement("ALTER TABLE promotion_usage CHANGE estado status ENUM('enviada', 'aceptada', 'rechazada') NOT NULL DEFAULT 'enviada'");

        // USERS TABLE - Use raw SQL for enum columns
        // Rename enum columns using raw SQL
        DB::statement("ALTER TABLE users CHANGE tipo_usuario user_type ENUM('administrador', 'dueño de local', 'cliente') NOT NULL DEFAULT 'cliente'");
        DB::statement("ALTER TABLE users CHANGE categoria_cliente client_category ENUM('Inicial', 'Medium', 'Premium') NULL DEFAULT 'Inicial'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // USERS TABLE - Use raw SQL for enum
        DB::statement("ALTER TABLE users CHANGE client_category categoria_cliente ENUM('Inicial', 'Medium', 'Premium') NULL DEFAULT 'Inicial'");
        DB::statement("ALTER TABLE users CHANGE user_type tipo_usuario ENUM('administrador', 'dueño de local', 'cliente') NOT NULL DEFAULT 'cliente'");

        // PROMOTION_USAGE TABLE
        DB::statement("ALTER TABLE promotion_usage CHANGE status estado ENUM('enviada', 'aceptada', 'rechazada') NOT NULL DEFAULT 'enviada'");
        
        Schema::table('promotion_usage', function (Blueprint $table) {
            $table->renameColumn('usage_date', 'usage_date');
        });

        // NEWS TABLE
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('title');
        });
        
        DB::statement("ALTER TABLE news CHANGE target_category categoria_destino ENUM('Inicial', 'Medium', 'Premium') NOT NULL DEFAULT 'Inicial'");
        
        Schema::table('news', function (Blueprint $table) {
            $table->renameColumn('end_date', 'end_date');
            $table->renameColumn('start_date', 'start_date');
            $table->renameColumn('description', 'description');
            $table->renameColumn('code', 'code');
        });

        // PROMOTIONS TABLE
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('title');
        });
        
        DB::statement("ALTER TABLE promotions CHANGE status estado ENUM('pendiente', 'aprobada', 'denegada') NOT NULL DEFAULT 'pendiente'");
        DB::statement("ALTER TABLE promotions CHANGE minimum_category categoria_minima ENUM('Inicial', 'Medium', 'Premium') NOT NULL DEFAULT 'Inicial'");
        
        Schema::table('promotions', function (Blueprint $table) {
            $table->renameColumn('weekdays', 'weekdays');
            $table->renameColumn('end_date', 'end_date');
            $table->renameColumn('start_date', 'start_date');
            $table->renameColumn('description', 'description');
            $table->renameColumn('code', 'code');
        });

        // STORES TABLE
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        
        Schema::table('stores', function (Blueprint $table) {
            $table->renameColumn('category', 'category');
            $table->renameColumn('location', 'location');
            $table->renameColumn('name', 'name');
            $table->renameColumn('code', 'code');
        });
    }
};
