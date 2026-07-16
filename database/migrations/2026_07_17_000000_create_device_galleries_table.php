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
        Schema::create('device_galleries', function (Blueprint $table) {
            $table->id();
            $table->string('style_slug'); // ric, bte, rechargeable, tinnitus, bluetooth, invisible
            $table->foreignId('media_id')->constrained('media')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('style_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_galleries');
    }
};
