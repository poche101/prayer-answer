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
    Schema::create('prayer_reports', function (Blueprint $table) {
        $table->id();

        // Core Identity
        $table->string('group')->index();
        $table->string('church')->index();

        // Content
        $table->string('prayer_link');
        $table->unsignedInteger('attendance')->default(0); // Added attendance field
        $table->date('meeting_date');
        $table->text('testimony')->nullable();

        // Metadata
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayer_reports');
    }
};
