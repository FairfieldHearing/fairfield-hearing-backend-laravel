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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_submission_id')->nullable()->constrained('form_submissions')->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name');
            $table->string('mobile_number');
            $table->string('email')->nullable();
            $table->string('hearing_problem')->nullable();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->string('preferred_day_time')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new'); // new, contacted, in_progress, won, lost
            $table->json('logs')->nullable(); // History of updates
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
