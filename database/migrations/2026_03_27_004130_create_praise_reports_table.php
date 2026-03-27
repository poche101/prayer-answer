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
        Schema::create('praise_reports', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('church');
            $table->string('prayer_link');
            $table->integer('attendance'); // Added for the new form field
            $table->date('meeting_date');
            $table->text('testimony')->nullable();
            $table->string('status')->default('pending'); // Useful for admin dashboard tracking
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('praise_reports');
    }
};
