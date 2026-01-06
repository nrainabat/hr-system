<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            
            // 1. Ensure 'position' column exists (to prevent errors with ->after('position'))
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('email');
            }

            // 2. Add 'phone_number' if it doesn't exist
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('email');
            }

            // 3. Add 'gender'
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('phone_number');
            }

            // 4. Add 'about'
            if (!Schema::hasColumn('users', 'about')) {
                $table->text('about')->nullable()->after('position');
            }

            // 5. Add 'address'
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('about');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop columns only if they exist
            if (Schema::hasColumn('users', 'phone_number')) $table->dropColumn('phone_number');
            if (Schema::hasColumn('users', 'gender')) $table->dropColumn('gender');
            if (Schema::hasColumn('users', 'about')) $table->dropColumn('about');
            if (Schema::hasColumn('users', 'address')) $table->dropColumn('address');
            // Note: We generally don't drop 'position' here if it might be used by other features
        });
    }
};