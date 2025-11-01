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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            
            // Sequential unique code for store identification
            $table->unsignedInteger('codigo')->unique()->index();
            
            // Store basic information
            $table->string('nombre', 100);
            $table->string('ubicacion', 50);
            $table->string('rubro', 20); // e.g., 'indumentaria', 'perfumeria', 'óptica', 'comida'
            
            // Foreign key to store owner (user with tipo_usuario = 'dueño de local')
            $table->foreignId('owner_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes(); // Soft delete to preserve historical data
            
            // Indexes for performance
            $table->index('rubro');
            $table->index('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
