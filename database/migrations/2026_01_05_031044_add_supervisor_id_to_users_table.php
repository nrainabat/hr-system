<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            
            // 1. Only add 'supervisor_id' if it doesn't exist yet
            if (!Schema::hasColumn('users', 'supervisor_id')) {
                
                // 2. Check if 'department' column exists for placement
                // (If 'department' is missing, place it after 'email' instead to avoid errors)
                $afterColumn = Schema::hasColumn('users', 'department') ? 'department' : 'email';

                $table->unsignedBigInteger('supervisor_id')->nullable()->after($afterColumn);
                
                // 3. Add the Foreign Key Constraint
                $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'supervisor_id')) {
                // Drop Foreign Key first
                $table->dropForeign(['supervisor_id']);
                // Then Drop Column
                $table->dropColumn('supervisor_id');
            }
        });
    }
};