<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $hasAuthorId = Schema::hasColumn('blog_posts', 'author_id');
            $hasAuthorName = Schema::hasColumn('blog_posts', 'author_name');

            if (!Schema::hasColumn('blog_posts', 'reviewer_id')) {
                if ($hasAuthorId) {
                    $table->unsignedBigInteger('reviewer_id')->nullable()->after('author_id');
                } else {
                    $table->unsignedBigInteger('reviewer_id')->nullable();
                }
            }

            if (!Schema::hasColumn('blog_posts', 'reviewer_name')) {
                if ($hasAuthorName) {
                    $table->string('reviewer_name')->nullable()->after('author_name');
                } else {
                    $table->string('reviewer_name')->nullable();
                }
            }
        });

        // Set default reviewer (Dr. Nayeem Ahmad Siddiqui) for existing blog posts
        $drNayeem = DB::table('team_members')->where('slug', 'dr-nayeem-ahmad-siddiqui')->first();
        if ($drNayeem) {
            DB::table('blog_posts')->whereNull('reviewer_id')->update([
                'reviewer_id' => $drNayeem->id,
                'reviewer_name' => $drNayeem->name,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            if (Schema::hasColumn('blog_posts', 'reviewer_id')) {
                $table->dropColumn('reviewer_id');
            }
            if (Schema::hasColumn('blog_posts', 'reviewer_name')) {
                $table->dropColumn('reviewer_name');
            }
        });
    }
};
