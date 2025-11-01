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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            
            // Sequential unique code for news identification
            $table->unsignedInteger('codigo')->unique()->index();
            
            // News text content
            $table->string('texto', 200);
            
            // Date range validity (news auto-expires after fecha_hasta)
            $table->date('fecha_desde');
            $table->date('fecha_hasta');
            
            // Target client category (determines visibility based on hierarchy)
            // 'Inicial' visible to all, 'Medium' to Medium+Premium, 'Premium' to Premium only
            $table->enum('categoria_destino', ['Inicial', 'Medium', 'Premium'])
                ->default('Inicial');
            
            // Foreign key to admin user who created the news
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('fecha_hasta'); // For auto-expiration queries
            $table->index('categoria_destino');
            $table->index(['fecha_hasta', 'categoria_destino']); // Composite index for active news query
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
