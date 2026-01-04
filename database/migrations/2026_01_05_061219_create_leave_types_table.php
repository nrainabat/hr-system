<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'Annual Leave'
            $table->integer('days_allowed')->default(14); // e.g., 14 days
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_types');
    }
};