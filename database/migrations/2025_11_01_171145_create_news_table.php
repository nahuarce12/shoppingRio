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
            $table->unsignedInteger('code')->unique()->index();
            
            // News text content
            $table->string('description', 200);
            
            // Date range validity (news auto-expires after fecha_hasta)
            $table->date('start_date');
            $table->date('end_date');
            
            // Target client category (determines visibility based on hierarchy)
            // 'Inicial' visible to all, 'Medium' to Medium+Premium, 'Premium' to Premium only
            $table->enum('target_category', ['Inicial', 'Medium', 'Premium'])
                ->default('Inicial');
            
            // Foreign key to admin user who created the news
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('end_date'); // For auto-expiration queries
            $table->index('target_category');
            $table->index(['end_date', 'target_category']); // Composite index for active news query
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
