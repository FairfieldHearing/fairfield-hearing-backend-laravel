<?php

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\TeamMember;
use App\Models\PolicyPage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response for admin redirect', function () {
    $response = $this->get('/admin');

    $response->assertRedirect('/admin/login');
});

test('public pages are accessible and return successful status', function () {
    $response = $this->get('/');
    $response->assertStatus(200);

    $response = $this->get('/about');
    $response->assertStatus(200);

    $response = $this->get('/book-a-test');
    $response->assertStatus(200);

    $response = $this->get('/bluetooth');
    $response->assertStatus(200);

    $response = $this->get('/blogs');
    $response->assertStatus(200);
});

test('dynamic public pages load successfully using seeded data', function () {
    // 1. Team Profile
    $team = TeamMember::first();
    if ($team) {
        $response = $this->get("/team/{$team->slug}");
        $response->assertStatus(200);
    }

    // 2. Policy Page
    $policy = PolicyPage::first();
    if ($policy) {
        $response = $this->get("/policies/{$policy->slug}");
        $response->assertStatus(200);
    }

    // 3. Category Page
    $category = BlogCategory::first();
    if ($category) {
        $response = $this->get("/blogs/{$category->slug}");
        $response->assertStatus(200);

        // 4. Blog Detail Page
        $post = BlogPost::where('blog_category_id', $category->id)->first();
        if ($post) {
            $response = $this->get("/blogs/{$category->slug}/{$post->slug}");
            $response->assertStatus(200);
        }
    }
});

test('blog posts markdown endpoint checks user agents correctly', function () {
    // Retrieve seeded or create category and post
    $category = BlogCategory::firstOrCreate(
        ['slug' => 'hearing-loss'],
        [
            'title' => 'Hearing Loss Info',
            'short_description' => 'Info about hearing loss',
        ]
    );

    $post = BlogPost::firstOrCreate(
        ['slug' => 'signs-of-hearing-loss'],
        [
            'blog_category_id' => $category->id,
            'title' => 'Signs of Hearing Loss',
            'summary' => 'Common signs of hearing loss in adults.',
            'content' => '<h1>Signs</h1><p>Here are some signs of hearing loss...</p>',
            'author_name' => 'Dr. Smith',
            'status' => 'published',
        ]
    );

    // 1. Normal browser agent (e.g. Chrome) should redirect (302) to standard detail page
    $response = $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ])->get("/blogs/{$category->slug}/{$post->slug}/markdown");

    $response->assertStatus(302);
    $response->assertRedirect("/blogs/{$category->slug}/{$post->slug}");

    // 2. Crawler / LLM agent (e.g. GPTBot) should return markdown content (200)
    $response = $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (compatible; GPTBot/1.0; +https://openai.com/gptbot)'
    ])->get("/blogs/{$category->slug}/{$post->slug}/markdown");

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/markdown; charset=utf-8');
    $response->assertSee('# Signs of Hearing Loss');
    $response->assertSee('*Author: Dr. Smith*');
    $response->assertSee('Here are some signs of hearing loss...');
});
