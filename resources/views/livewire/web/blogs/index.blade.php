<div>
    <script type="application/ld+json">
        {!! json_encode($blogSchema) !!}
    </script>
    <section class="page-hero">
        <div class="container">
            <x-web-breadcrumbs :items="[
                ['name' => 'Home', 'item' => '/'],
                ['name' => 'Blog', 'item' => '/blogs'],
            ]" />
            <span class="eyebrow">Hearing Health Blog</span>
            <h1>Expert hearing care, explained</h1>
            <p class="lead">
                Buying guides, brand comparisons, prices and practical tips — written and reviewed by RCI-certified
                audiologists to help you hear better.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="container center">
            <span class="eyebrow">Browse by topic</span>
            <h2>Blog categories</h2>
            <p class="lead">Find articles organised by what you need.</p>
        </div>
        <div class="container">
            <div class="cat-grid">
                @foreach($categories as $cat)
                    <a class="cat-card" href="/blogs/{{ $cat['slug'] }}">
                        <div class="cat-card__ic">
                            @if($cat['slug'] === 'buying-guides')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3 8-8" /><path d="M20 12v6a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h9" /></svg>
                            @elseif($cat['slug'] === 'comparisons')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M5 7l-3 6h6zM19 7l-3 6h6zM5 7h14M7 21h10" /></svg>
                            @elseif($cat['slug'] === 'prices')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 4h12M6 9h12M9 4c4 0 4 5 0 5M6 9c8 0 8 0 8 0M7 13l7 7" /></svg>
                            @elseif($cat['slug'] === 'types-technology')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="6" width="12" height="12" rx="2" /><path d="M9 1v3M15 1v3M9 20v3M15 20v3M1 9h3M1 15h3M20 9h3M20 15h3" /></svg>
                            @elseif($cat['slug'] === 'hearing-loss')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0112 0c0 4-3 4-3 7a3 3 0 01-6 0M9 9a3 3 0 016 0" /></svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a4 4 0 01-5.4 5.4L4 17v3h3l5.3-5.3a4 4 0 015.4-5.4l-2.6 2.6-2-2 2.6-2.6z" /></svg>
                            @endif
                        </div>
                        <h3>{{ $cat['title'] }}</h3>
                        <p>{{ $cat['short_description'] }}</p>
                        <span class="count">View articles →</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--soft">
        <div class="container center" style="margin-bottom: 40px;">
            <span class="eyebrow">Latest articles</span>
            <h2>Most recent posts</h2>
        </div>
        <div class="container">
            <div class="post-grid">
                @foreach($posts as $post)
                    @php
                        $catSlug = $post['category']['slug'] ?? 'general';
                        $coverImg = '/assets/img/logo.jpeg';
                        if (str_contains($post['slug'], 'styletto')) {
                            $coverImg = '/assets/img/signia-styletto-ix-7ix-vs-5ix-vs-3ix.svg';
                        } elseif (str_contains($post['slug'], 'severe')) {
                            $coverImg = '/assets/img/best-hearing-aids-for-severe-to-profound-loss.svg';
                        } elseif (str_contains($post['slug'], 'senior')) {
                            $coverImg = '/assets/img/best-hearing-aids-for-senior-citizens.svg';
                        }
                        $formattedDate = date('d M Y', strtotime($post['created_at']));
                    @endphp
                    <article class="post-card">
                        <a href="/blogs/{{ $catSlug }}/{{ $post['slug'] }}">
                            <img class="post-card__img" src="{{ $coverImg }}" alt="{{ $post['title'] }}" loading="lazy">
                        </a>
                        <div class="post-card__body">
                            <span class="tag">{{ $post['category']['title'] ?? 'Hearing Health' }}</span>
                            <h3>
                                <a href="/blogs/{{ $catSlug }}/{{ $post['slug'] }}">{{ $post['title'] }}</a>
                            </h3>
                            <p>{{ $post['summary'] }}</p>
                            <div class="post-card__meta">
                                <span>{{ $formattedDate }}</span>
                                <span>·</span>
                                <span>5 min read</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section cta-band">
        <div class="container">
            <h2>Have a hearing question?</h2>
            <p class="lead">Book a free, no-obligation hearing test with our RCI-certified audiologists.</p>
            <div class="cta-band__btns">
                <a href="/book-a-test" class="btn btn--white btn--lg">
                    Book Your Free Test
                </a>
                <a href="tel:+919811418578" class="btn btn--lime btn--lg">
                    📞 Call +91-9811418578
                </a>
            </div>
        </div>
    </section>
</div>