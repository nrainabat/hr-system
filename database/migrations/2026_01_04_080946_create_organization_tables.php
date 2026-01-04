<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create Departments Table
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('manager_name')->nullable();
                $table->timestamps();
            });
        }

        // Create Job Positions Table
        if (!Schema::hasTable('job_positions')) {
            Schema::create('job_positions', function (Blueprint $table) {
                $table->id();
                $table->string('title')->unique();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_positions');
        Schema::dropIfExists('departments');
    }
};