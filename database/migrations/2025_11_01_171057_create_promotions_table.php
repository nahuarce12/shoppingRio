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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            
            // Sequential unique code for promotion identification
            $table->unsignedInteger('codigo')->unique()->index();
            
            // Promotion description (e.g., '20% pago contado', '2x1 para mismo producto')
            $table->string('texto', 200);
            
            // Date range validity
            $table->date('fecha_desde');
            $table->date('fecha_hasta');
            
            // Days of week validity (JSON array of 7 booleans: Monday=0 to Sunday=6)
            // Example: [true, true, false, false, false, true, true] = Mon, Tue, Sat, Sun
            $table->json('dias_semana');
            
            // Minimum client category required to access this promotion
            $table->enum('categoria_minima', ['Inicial', 'Medium', 'Premium'])
                ->default('Inicial');
            
            // Admin approval status
            $table->enum('estado', ['pendiente', 'aprobada', 'denegada'])
                ->default('pendiente')
                ->index();
            
            // Foreign key to store that owns this promotion
            $table->foreignId('store_id')
                ->constrained('stores')
                ->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes(); // Soft delete to preserve historical usage data
            
            // Indexes for performance
            $table->index('fecha_desde');
            $table->index('fecha_hasta');
            $table->index('categoria_minima');
            $table->index(['estado', 'fecha_desde', 'fecha_hasta']); // Composite index for active promotions query
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
