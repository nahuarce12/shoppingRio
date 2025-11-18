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
            $table->unsignedInteger('code')->unique()->index();
            
            // Promotion description (e.g., '20% pago contado', '2x1 para mismo producto')
            $table->string('description', 200);
            
            // Date range validity
            $table->date('start_date');
            $table->date('end_date');
            
            // Days of week validity (JSON array of 7 booleans: Monday=0 to Sunday=6)
            // Example: [true, true, false, false, false, true, true] = Mon, Tue, Sat, Sun
            $table->json('weekdays');
            
            // Minimum client category required to access this promotion
            $table->enum('minimum_category', ['Inicial', 'Medium', 'Premium'])
                ->default('Inicial');
            
            // Admin approval status
            $table->enum('status', ['pendiente', 'aprobada', 'denegada'])
                ->default('pendiente')
                ->index();
            
            // Foreign key to store that owns this promotion
            $table->foreignId('store_id')
                ->constrained('stores')
                ->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes(); // Soft delete to preserve historical usage data
            
            // Indexes for performance
            $table->index('start_date');
            $table->index('end_date');
            $table->index('minimum_category');
            $table->index(['status', 'start_date', 'end_date']); // Composite index for active promotions query
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
