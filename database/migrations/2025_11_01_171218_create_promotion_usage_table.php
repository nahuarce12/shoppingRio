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
        Schema::create('promotion_usage', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to client user
            $table->foreignId('client_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Foreign key to promotion
            $table->foreignId('promotion_id')
                ->constrained('promotions')
                ->onDelete('cascade');
            
            // Date when the promotion usage was requested/accepted
            $table->date('fecha_uso');
            
            // Status of the usage request
            // 'enviada' = pending store owner review
            // 'aceptada' = accepted by store owner
            // 'rechazada' = rejected by store owner
            $table->enum('estado', ['enviada', 'aceptada', 'rechazada'])
                ->default('enviada')
                ->index();
            
            $table->timestamps();
            
            // Unique constraint to enforce single-use rule per client per promotion
            $table->unique(['client_id', 'promotion_id']);
            
            // Indexes for performance
            $table->index('fecha_uso');
            $table->index(['promotion_id', 'estado']); // For store owner to see pending requests
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_usage');
    }
};
