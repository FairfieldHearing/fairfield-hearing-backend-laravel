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
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->foreignId('featured_image_media_id')->nullable()->constrained('media')->nullOnDelete();
        });

        Schema::table('blog_categories', function (Blueprint $table) {
            $table->foreignId('image_media_id')->nullable()->constrained('media')->nullOnDelete();
        });

        Schema::table('manufacturers', function (Blueprint $table) {
            $table->foreignId('logo_media_id')->nullable()->constrained('media')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropForeign(['featured_image_media_id']);
            $table->dropColumn('featured_image_media_id');
        });

        Schema::table('blog_categories', function (Blueprint $table) {
            $table->dropForeign(['image_media_id']);
            $table->dropColumn('image_media_id');
        });

        Schema::table('manufacturers', function (Blueprint $table) {
            $table->dropForeign(['logo_media_id']);
            $table->dropColumn('logo_media_id');
        });
    }
};
