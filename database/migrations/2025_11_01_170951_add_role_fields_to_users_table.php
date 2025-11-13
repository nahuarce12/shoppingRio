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
        Schema::table('users', function (Blueprint $table) {
            // Add tipo_usuario enum field
            $table->enum('user_type', ['administrador', 'dueño de local', 'cliente'])
                ->default('cliente')
                ->after('password')
                ->index();
            
            // Add categoria_cliente enum field (only for clients)
            $table->enum('client_category', ['Inicial', 'Medium', 'Premium'])
                ->nullable()
                ->default('Inicial')
                ->after('user_type')
                ->index();
            
            // Add approval tracking for store owners
            $table->timestamp('approved_at')->nullable()->after('email_verified_at');
            
            // Add foreign key to track who approved the user (admin)
            $table->foreignId('approved_by')
                ->nullable()
                ->after('approved_at')
                ->constrained('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['user_type', 'client_category', 'approved_at', 'approved_by']);
        });
    }
};
