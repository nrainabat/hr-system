<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('intern_documents', function (Blueprint $table) {
            $table->string('signed_file_path')->nullable()->after('file_path');
            $table->text('supervisor_comment')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('intern_documents', function (Blueprint $table) {
            $table->dropColumn(['signed_file_path', 'supervisor_comment']);
        });
    }
};