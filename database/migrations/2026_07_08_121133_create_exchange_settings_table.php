<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exchange_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        DB::table('exchange_settings')->insert([
            ['key' => 'min_exchange_value', 'value' => json_encode(500), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_exchange_value', 'value' => json_encode(25000), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'capped_exchange_value', 'value' => json_encode(12000), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'brand_multipliers', 'value' => json_encode(['major' => 1.0, 'other' => 0.8]), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'age_multipliers', 'value' => json_encode([
                'less_than_1' => 1.0,
                '1_2_years' => 0.8,
                '2_4_years' => 0.6,
                '4_6_years' => 0.4,
                'more_than_6' => 0.25
            ]), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'condition_multipliers', 'value' => json_encode([
                'fully_working' => 1.0,
                'minor_issues' => 0.8,
                'receiver_not_working' => 0.6,
                'not_working' => 0.35,
                'broken' => 0.2
            ]), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'price_bands', 'value' => json_encode([
                'under_20k' => 6000,
                '20k_50k' => 10000,
                '50k_100k' => 16000,
                'above_100k' => 25000
            ]), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_settings');
    }
};
