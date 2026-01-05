<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_counts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('leave_type'); // e.g., 'Annual', 'Medical'
            $table->integer('balance')->default(0); // Total days available
            $table->integer('year')->default(date('Y')); // To track per year
            $table->timestamps();
            
            // Prevent duplicate entries for same user/type/year
            $table->unique(['user_id', 'leave_type', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_counts');
    }
};