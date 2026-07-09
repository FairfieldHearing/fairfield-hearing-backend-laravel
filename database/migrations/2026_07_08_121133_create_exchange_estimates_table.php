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
        Schema::create('exchange_estimates', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->index();
            $table->string('unique_hash')->unique()->index();
            $table->foreignId('hearing_aid_model_id')->nullable()->constrained('hearing_aid_models')->onDelete('set null');
            $table->boolean('want_exchange')->default(false);
            $table->string('old_brand')->nullable();
            $table->string('old_model')->nullable();
            $table->string('old_price_band')->nullable();
            $table->string('old_age_band')->nullable();
            $table->string('old_condition_band')->nullable();
            $table->integer('calculated_value')->default(0);
            $table->integer('final_price')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_estimates');
    }
};
