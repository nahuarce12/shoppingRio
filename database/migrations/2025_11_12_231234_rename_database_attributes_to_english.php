<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration adds missing columns if they don't exist.
     * It's safe to run on fresh databases or databases that already have the columns.
     */
    public function up(): void
    {
        // STORES TABLE - Add description if missing
        if (!Schema::hasColumn('stores', 'description')) {
            Schema::table('stores', function (Blueprint $table) {
                $table->text('description')->nullable()->after('category');
            });
        }

        // PROMOTIONS TABLE - Add title if missing
        if (!Schema::hasColumn('promotions', 'title')) {
            Schema::table('promotions', function (Blueprint $table) {
                $table->string('title', 100)->nullable()->after('code');
            });
        }

        // Handle Spanish to English column renames only if Spanish columns exist
        if (Schema::hasColumn('promotions', 'categoria_minima')) {
            DB::statement("ALTER TABLE promotions CHANGE categoria_minima minimum_category ENUM('Inicial', 'Medium', 'Premium') NOT NULL DEFAULT 'Inicial'");
        }
        if (Schema::hasColumn('promotions', 'estado')) {
            DB::statement("ALTER TABLE promotions CHANGE estado status ENUM('pendiente', 'aprobada', 'denegada') NOT NULL DEFAULT 'pendiente'");
        }

        // NEWS TABLE - Add title if missing
        if (!Schema::hasColumn('news', 'title')) {
            Schema::table('news', function (Blueprint $table) {
                $table->string('title', 100)->nullable()->after('code');
            });
        }

        // Handle Spanish to English column renames only if Spanish columns exist
        if (Schema::hasColumn('news', 'categoria_destino')) {
            DB::statement("ALTER TABLE news CHANGE categoria_destino target_category ENUM('Inicial', 'Medium', 'Premium') NOT NULL DEFAULT 'Inicial'");
        }

        // PROMOTION_USAGE TABLE - Handle Spanish to English renames only if needed
        if (Schema::hasColumn('promotion_usage', 'estado')) {
            DB::statement("ALTER TABLE promotion_usage CHANGE estado status ENUM('enviada', 'aceptada', 'rechazada') NOT NULL DEFAULT 'enviada'");
        }

        // USERS TABLE - Handle Spanish to English renames only if needed
        if (Schema::hasColumn('users', 'tipo_usuario')) {
            DB::statement("ALTER TABLE users CHANGE tipo_usuario user_type ENUM('administrador', 'dueÃ±o de local', 'cliente') NOT NULL DEFAULT 'cliente'");
        }
        if (Schema::hasColumn('users', 'categoria_cliente')) {
            DB::statement("ALTER TABLE users CHANGE categoria_cliente client_category ENUM('Inicial', 'Medium', 'Premium') NULL DEFAULT 'Inicial'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only revert if English columns exist
        if (Schema::hasColumn('users', 'user_type')) {
            DB::statement("ALTER TABLE users CHANGE user_type tipo_usuario ENUM('administrador', 'dueÃ±o de local', 'cliente') NOT NULL DEFAULT 'cliente'");
        }
        if (Schema::hasColumn('users', 'client_category')) {
            DB::statement("ALTER TABLE users CHANGE client_category categoria_cliente ENUM('Inicial', 'Medium', 'Premium') NULL DEFAULT 'Inicial'");
        }

        if (Schema::hasColumn('promotion_usage', 'status')) {
            DB::statement("ALTER TABLE promotion_usage CHANGE status estado ENUM('enviada', 'aceptada', 'rechazada') NOT NULL DEFAULT 'enviada'");
        }

        if (Schema::hasColumn('news', 'title')) {
            Schema::table('news', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
        if (Schema::hasColumn('news', 'target_category')) {
            DB::statement("ALTER TABLE news CHANGE target_category categoria_destino ENUM('Inicial', 'Medium', 'Premium') NOT NULL DEFAULT 'Inicial'");
        }

        if (Schema::hasColumn('promotions', 'title')) {
            Schema::table('promotions', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
        if (Schema::hasColumn('promotions', 'minimum_category')) {
            DB::statement("ALTER TABLE promotions CHANGE minimum_category categoria_minima ENUM('Inicial', 'Medium', 'Premium') NOT NULL DEFAULT 'Inicial'");
        }
        if (Schema::hasColumn('promotions', 'status')) {
            DB::statement("ALTER TABLE promotions CHANGE status estado ENUM('pendiente', 'aprobada', 'denegada') NOT NULL DEFAULT 'pendiente'");
        }

        if (Schema::hasColumn('stores', 'description')) {
            Schema::table('stores', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
