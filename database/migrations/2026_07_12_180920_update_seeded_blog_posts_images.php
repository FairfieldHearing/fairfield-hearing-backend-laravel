<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('blog_posts')
            ->where('slug', 'best-hearing-aids-for-senior-citizens')
            ->update(['featured_image' => 'assets/img/best-hearing-aids-for-senior-citizens.svg']);

        DB::table('blog_posts')
            ->where('slug', 'best-hearing-aids-for-severe-to-profound-loss')
            ->update(['featured_image' => 'assets/img/best-hearing-aids-for-severe-to-profound-loss.svg']);

        DB::table('blog_posts')
            ->where('slug', 'signia-styletto-ix-7ix-vs-5ix-vs-3ix')
            ->update(['featured_image' => 'assets/img/signia-styletto-ix-7ix-vs-5ix-vs-3ix.svg']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('blog_posts')
            ->whereIn('slug', [
                'best-hearing-aids-for-senior-citizens',
                'best-hearing-aids-for-severe-to-profound-loss',
                'signia-styletto-ix-7ix-vs-5ix-vs-3ix'
            ])
            ->update(['featured_image' => null]);
    }
};
