<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $posts = DB::table('blog_posts')->get();
        foreach ($posts as $post) {
            $content = $post->content;
            
            // Replace occurrences like href="/ric.html" with href="/ric"
            $replaced = preg_replace('/href="\/([^"\s]+)\.html"/', 'href="/$1"', $content);
            
            if ($replaced !== $content) {
                DB::table('blog_posts')->where('id', $post->id)->update([
                    'content' => $replaced,
                ]);
            }
        }
    }

    public function down(): void
    {
        // No rollback action needed for URL normalization
    }
};
