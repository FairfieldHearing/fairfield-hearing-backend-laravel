<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

Route::get('/admin/login', \App\Livewire\Admin\Login::class)->name('admin.login');

Route::get('/admin/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('admin.login');
})->name('admin.logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/categories', \App\Livewire\Admin\Categories\Index::class)->name('categories');
    Route::get('/posts', \App\Livewire\Admin\Posts\Index::class)->name('posts');
    Route::get('/faqs', \App\Livewire\Admin\Faqs\Index::class)->name('faqs');
    Route::get('/locations', \App\Livewire\Admin\Locations\Index::class)->name('locations');
    Route::get('/policies', \App\Livewire\Admin\Policies\Index::class)->name('policies');
    Route::get('/submissions', \App\Livewire\Admin\Submissions\Index::class)->name('submissions');
    Route::get('/leads', \App\Livewire\Admin\Leads\Index::class)->name('leads');
    Route::get('/tickets', \App\Livewire\Admin\Tickets\Index::class)->name('tickets');
    Route::get('/staff', \App\Livewire\Admin\Users\Index::class)->name('staff');
    Route::get('/password', \App\Livewire\Admin\Password::class)->name('password');
    Route::get('/manufacturers', \App\Livewire\Admin\Manufacturers\Index::class)->name('manufacturers');
    Route::get('/models', \App\Livewire\Admin\HearingAidModels\Index::class)->name('models');
    Route::get('/exchange-settings', \App\Livewire\Admin\ExchangeSettings\Index::class)->name('exchange_settings');
});

// Public Website Routes
Route::get('/', \App\Livewire\Web\Home::class)
    ->name('web.home')
    ->middleware(\App\Http\Middleware\AddAgentLinkHeaders::class);

Route::get('/exchange', \App\Livewire\Web\Exchange::class)->name('web.exchange');

Route::get('/.well-known/api-catalog', function () {
    return response()->json([
        'services' => [
            [
                'name' => 'Fairfield Hearing API',
                'description' => 'Public API for retrieving clinics, blog posts, categories, and submitting leads.',
                'endpoints' => [
                    ['path' => '/api/locations', 'method' => 'GET', 'description' => 'Retrieve clinic locations.'],
                    ['path' => '/api/posts', 'method' => 'GET', 'description' => 'Retrieve public blog posts.'],
                    ['path' => '/api/categories', 'method' => 'GET', 'description' => 'Retrieve blog categories.'],
                    ['path' => '/api/faqs', 'method' => 'GET', 'description' => 'Retrieve FAQs.'],
                    ['path' => '/api/submissions', 'method' => 'POST', 'description' => 'Submit customer contact lead.'],
                ]
            ]
        ]
    ]);
});

Route::get('/about', \App\Livewire\Web\About::class)->name('web.about');
Route::get('/book-a-test', \App\Livewire\Web\BookTest::class)->name('web.book_test');

Route::get('/bluetooth', \App\Livewire\Web\Tech\Bluetooth::class)->name('web.tech.bluetooth');
Route::get('/bte', \App\Livewire\Web\Tech\Bte::class)->name('web.tech.bte');
Route::get('/invisible', \App\Livewire\Web\Tech\Invisible::class)->name('web.tech.invisible');
Route::get('/rechargeable', \App\Livewire\Web\Tech\Rechargeable::class)->name('web.tech.rechargeable');
Route::get('/ric', \App\Livewire\Web\Tech\Ric::class)->name('web.tech.ric');
Route::get('/tinnitus', \App\Livewire\Web\Tech\Tinnitus::class)->name('web.tech.tinnitus');

Route::get('/team/{slug}', \App\Livewire\Web\Team\Show::class)->name('web.team.show');
Route::get('/policies/{slug}', \App\Livewire\Web\Policy\Show::class)->name('web.policy.show');

Route::get('/blogs', \App\Livewire\Web\Blogs\Index::class)->name('web.blogs.index');
Route::get('/blogs/{category}', \App\Livewire\Web\Blogs\CategoryPage::class)->name('web.blogs.category');
Route::get('/blogs/{category}/{slug}', \App\Livewire\Web\Blogs\Show::class)->name('web.blogs.show');

Route::get('/blogs/{category}/{slug}/markdown', function ($category, $slug) {
    $userAgent = strtolower(request()->header('User-Agent', ''));
    $isLlmOrCrawler = false;
    $llmAgents = [
        'gptbot', 'chatgpt-user', 'claude-web', 'googlebot', 'bingbot', 'bytespider', 
        'ccbot', 'anthropic-ai', 'cohere-ai', 'facebookexternalhit', 'yandexbot', 
        'baiduspider', 'semrushbot', 'ahrefsbot', 'mj12bot', 'dotbot', 'rogerbot', 
        'megaindex', 'blexbot', 'sogou', 'exabot', 'slackbot', 'curl', 'wget', 
        'python', 'urllib', 'httpclient', 'go-http-client', 'java', 'libwww-perl', 
        'llm', 'gemini', 'perplexity'
    ];
    foreach ($llmAgents as $agent) {
        if (str_contains($userAgent, $agent)) {
            $isLlmOrCrawler = true;
            break;
        }
    }

    if (!$isLlmOrCrawler) {
        return redirect()->route('web.blogs.show', ['category' => $category, 'slug' => $slug]);
    }

    $post = \App\Models\BlogPost::with('category')->where('slug', $slug)->first();
    if (!$post) {
        return response('Post not found', 404);
    }

    $faqs = \App\Models\Faq::where('blog_post_id', $post->id)->get();

    $convertHtmlToMarkdown = function ($html) {
        $md = str_replace('\\n', "\n", $html);
        $md = preg_replace('/<h1>(.*?)<\/h1>/i', "# $1\n\n", $md);
        $md = preg_replace('/<h2>(.*?)<\/h2>/i', "## $1\n\n", $md);
        $md = preg_replace('/<h3>(.*?)<\/h3>/i', "### $1\n\n", $md);
        $md = preg_replace('/<h4>(.*?)<\/h4>/i', "#### $1\n\n", $md);
        $md = preg_replace('/<strong>(.*?)<\/strong>/i', "**$1**", $md);
        $md = preg_replace('/<b>(.*?)<\/b>/i', "**$1**", $md);
        $md = preg_replace('/<em>(.*?)<\/em>/i', "*$1*", $md);
        $md = preg_replace('/<i>(.*?)<\/i>/i', "*$1*", $md);
        $md = preg_replace('/<p>(.*?)<\/p>/i', "$1\n\n", $md);
        $md = preg_replace('/<li>(.*?)<\/li>/i', "- $1\n", $md);
        $md = preg_replace('/<ul>([\s\S]*?)<\/ul>/i', "$1\n", $md);
        $md = preg_replace('/<ol>([\s\S]*?)<\/ol>/i', "$1\n", $md);
        $md = preg_replace('/<br\s*\/?>/i', "\n", $md);
        $md = preg_replace('/<a\s+[^>]*href="([^"]*)"[^>]*>(.*?)<\/a>/i', "[$2]($1)", $md);
        $md = preg_replace('/<[^>]+>/', "", $md);
        $md = preg_replace('/\n{3,}/', "\n\n", $md);
        return trim($md);
    };

    $markdown = "# " . $post->title . "\n\n";
    $markdown .= "*Author: " . $post->author_name . "*\n";
    $markdown .= "*Published: " . date('D M d Y', strtotime($post->created_at)) . "*\n\n";
    $markdown .= "> " . $post->summary . "\n\n";
    $markdown .= "---\n\n";
    $markdown .= $convertHtmlToMarkdown($post->content);

    if ($faqs->count() > 0) {
        $markdown .= "\n\n---\n\n## Frequently Asked Questions\n\n";
        foreach ($faqs as $faq) {
            $markdown .= "### " . $faq->question . "\n\n" . $convertHtmlToMarkdown($faq->answer) . "\n\n";
        }
    }

    return response($markdown, 200, [
        'Content-Type' => 'text/markdown; charset=utf-8',
        'Cache-Control' => 'public, max-age=3600',
    ]);
});

$validateMaintenance = function (Request $request) {
    // 1. Restrict execution strictly to local environment
    if (!App::environment('local')) {
        abort(403, 'Unauthorized environment.');
    }
    // 2. Validate the authorization token
    $token = $request->header('X-Maintenance-Token') ?: $request->input('token');
    $expectedToken = config('admin.maintenance_token');
    if (empty($expectedToken) || $token !== $expectedToken) {
        abort(401, 'Unauthorized token.');
    }
};

Route::prefix('maintenance')->group(function () use ($validateMaintenance) {
    Route::get('/migrate', function (Request $request) use ($validateMaintenance) {
        $validateMaintenance($request);
        Artisan::call('migrate', ['--force' => true]);
        return response()->json(['output' => Artisan::output()]);
    });
    Route::get('/migrate-force-seed', function (Request $request) use ($validateMaintenance) {
        $validateMaintenance($request);
        Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
        return response()->json(['output' => Artisan::output()]);
    });
    Route::get('/optimize', function (Request $request) use ($validateMaintenance) {
        $validateMaintenance($request);
        Artisan::call('optimize');
        return response()->json(['output' => Artisan::output()]);
    });
    Route::get('/optimize-clear', function (Request $request) use ($validateMaintenance) {
        $validateMaintenance($request);
        Artisan::call('optimize:clear');
        return response()->json(['output' => Artisan::output()]);
    });
    Route::get('/storage-link', function (Request $request) use ($validateMaintenance) {
        $validateMaintenance($request);

        $targetPath = storage_path('app/public');
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }

        $linkPath = public_path('storage');
        if (is_link($linkPath)) {
            unlink($linkPath);
        }

        Artisan::call('storage:link');
        return response()->json(['output' => Artisan::output()]);
    });
});

Route::get('/sitemap.xml', function () {
    $urls = [];
    $baseUrl = config('app.url', 'https://fairfieldhearing.in');
    
    $addUrl = function ($path, $lastMod = null, $changefreq = 'weekly', $priority = '0.5') use ($baseUrl, &$urls) {
        $urls[] = [
            'loc' => rtrim($baseUrl, '/') . '/' . ltrim($path, '/'),
            'lastmod' => $lastMod ? $lastMod->toAtomString() : now()->toAtomString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    };

    // Static pages
    $addUrl('/', now(), 'daily', '1.0');
    $addUrl('/about', now(), 'monthly', '0.8');
    $addUrl('/book-a-test', now(), 'monthly', '0.9');
    $addUrl('/bluetooth', now(), 'monthly', '0.7');
    $addUrl('/bte', now(), 'monthly', '0.7');
    $addUrl('/invisible', now(), 'monthly', '0.7');
    $addUrl('/rechargeable', now(), 'monthly', '0.7');
    $addUrl('/ric', now(), 'monthly', '0.7');
    $addUrl('/tinnitus', now(), 'monthly', '0.7');
    $addUrl('/blogs', now(), 'daily', '0.9');

    // Dynamic Team Members
    foreach (\App\Models\TeamMember::all() as $member) {
        $addUrl('/team/' . $member->slug, $member->updated_at, 'monthly', '0.6');
    }

    // Dynamic Policies
    foreach (\App\Models\PolicyPage::all() as $policy) {
        $addUrl('/policies/' . $policy->slug, $policy->updated_at, 'monthly', '0.5');
    }

    // Dynamic Blog Categories
    foreach (\App\Models\BlogCategory::all() as $category) {
        $addUrl('/blogs/' . $category->slug, $category->updated_at, 'weekly', '0.7');
    }

    // Dynamic Blog Posts
    foreach (\App\Models\BlogPost::with('category')->get() as $post) {
        if ($post->category) {
            $addUrl('/blogs/' . $post->category->slug . '/' . $post->slug, $post->updated_at, 'weekly', '0.8');
        }
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($urls as $url) {
        $xml .= '<url>';
        $xml .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
        $xml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
        $xml .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
        $xml .= '<priority>' . $url['priority'] . '</priority>';
        $xml .= '</url>';
    }
    $xml .= '</urlset>';

    return response($xml, 200, [
        'Content-Type' => 'application/xml',
    ]);
});