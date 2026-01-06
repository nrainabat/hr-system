<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table exists to prevent "Table already exists" error
        if (!Schema::hasTable('intern_documents')) {
            Schema::create('intern_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('filename');
                $table->string('file_path');
                $table->string('description')->nullable();
                $table->enum('status', ['pending', 'signed', 'rejected'])->default('pending');
                
                // Add the supervisor fields directly here
                $table->string('signed_file_path')->nullable();
                $table->text('supervisor_comment')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('intern_documents');
    }
};