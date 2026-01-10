<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The Employee
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade'); // The Supervisor
            $table->date('review_date');
            
            // Core Metrics (1-5 Scale)
            $table->integer('rating_quality');
            $table->integer('rating_efficiency');
            $table->integer('rating_teamwork');
            $table->integer('rating_punctuality');
            
            $table->decimal('average_score', 3, 1); // e.g., 4.5
            $table->text('comments')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};