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
        Schema::table('intern_documents', function (Blueprint $table) {
            // Check if column exists before adding to prevent errors if re-running
            if (!Schema::hasColumn('intern_documents', 'signed_file_path')) {
                $table->string('signed_file_path')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('intern_documents', 'supervisor_comment')) {
                $table->text('supervisor_comment')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intern_documents', function (Blueprint $table) {
            $table->dropColumn(['signed_file_path', 'supervisor_comment']);
        });
    }
};