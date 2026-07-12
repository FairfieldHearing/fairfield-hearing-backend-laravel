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

    $response = $this->get('/exchange');
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

test('manufacturer model logo url resolution and status filters work correctly', function () {
    $seeded = \App\Models\Manufacturer::create([
        'name' => 'Test Seeded',
        'logo_path' => 'assets/img/l-1.png',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $uploaded = \App\Models\Manufacturer::create([
        'name' => 'Test Uploaded',
        'logo_path' => 'manufacturers/test.png',
        'is_active' => false,
        'sort_order' => 2,
    ]);

    expect($seeded->logo_url)->toBe('/assets/img/l-1.png');
    expect($uploaded->logo_url)->toBe(Storage::url('manufacturers/test.png'));

    // Verify active filter on home render path
    $activeManufacturers = \App\Models\Manufacturer::where('is_active', true)->get();
    expect($activeManufacturers->contains($seeded))->toBeTrue();
    expect($activeManufacturers->contains($uploaded))->toBeFalse();
});

test('exchange page livewire calculation logic and session estimates work', function () {
    $manufacturer = \App\Models\Manufacturer::create([
        'name' => 'Signia Test',
        'logo_path' => 'assets/img/l-1.png',
        'is_active' => true,
    ]);

    $model = \App\Models\HearingAidModel::create([
        'manufacturer_id' => $manufacturer->id,
        'name' => 'Pure 312 Test',
        'mrp' => 100000,
        'discount_pct' => 50,
        'tech_level' => 'Standard',
        'form_factor' => 'RIC',
        'units' => 1,
        'is_active' => true,
    ]);

    // Test livewire flow
    $lw = \Livewire\Livewire::test(\App\Livewire\Web\Exchange::class)
        ->set('selectedBrandId', $manufacturer->id)
        ->set('selectedModelId', $model->id)
        ->assertSet('discountedPrice', 50000)
        ->set('wantExchange', 'yes')
        ->set('oldBrand', 'Phonak')
        ->set('oldModel', 'Marvel')
        ->set('oldPriceBand', '20k_50k')
        ->set('oldAgeBand', '1_2_years')
        ->set('oldConditionBand', 'fully_working')
        ->assertSet('exchangeValue', 8000)
        ->assertSet('finalPrice', 42000);

    expect($lw->get('uniqueHash'))->not->toBeEmpty();

    // Verify DB record matches
    $this->assertDatabaseHas('exchange_estimates', [
        'hearing_aid_model_id' => $model->id,
        'want_exchange' => true,
        'old_brand' => 'Phonak',
        'calculated_value' => 8000,
        'final_price' => 42000,
    ]);
});

test('seo meta keywords and canonical tags render correctly on public pages', function () {
    // 1. Check static page setting overrides
    $setting = \App\Models\PageSetting::where('page_key', 'home')->first();
    $setting->update([
        'meta_keywords' => 'test_keyword_1, test_keyword_2',
        'canonical_url' => 'https://fairfieldhearing.in/canonical-test-home',
    ]);

    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('<meta name="keywords" content="test_keyword_1, test_keyword_2">', false);
    $response->assertSee('<link rel="canonical" href="https://fairfieldhearing.in/canonical-test-home">', false);
    $response->assertSee('"@type": "MedicalBusiness"', false);

    // 2. Check dynamic blog page overrides
    $category = BlogCategory::firstOrCreate(
        ['slug' => 'hearing-news'],
        [
            'title' => 'Hearing News',
            'short_description' => 'News about hearing technology',
        ]
    );

    $post = BlogPost::firstOrCreate(
        ['slug' => 'new-seo-post'],
        [
            'blog_category_id' => $category->id,
            'title' => 'New SEO Post',
            'summary' => 'Common signs of hearing loss in adults.',
            'content' => '<p>Content details</p>',
            'author_name' => 'Wasiq Ali Khan',
            'meta_keywords' => 'blog_seo_keyword_1, blog_seo_keyword_2',
            'canonical_url' => 'https://fairfieldhearing.in/blogs/hearing-news/custom-canonical-post',
        ]
    );

    $response = $this->get("/blogs/{$category->slug}/{$post->slug}");
    $response->assertStatus(200);
    $response->assertSee('<meta name="keywords" content="blog_seo_keyword_1, blog_seo_keyword_2">', false);
    $response->assertSee('<link rel="canonical" href="https://fairfieldhearing.in/blogs/hearing-news/custom-canonical-post">', false);
});

