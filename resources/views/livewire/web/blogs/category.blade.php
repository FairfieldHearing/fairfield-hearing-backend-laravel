<div>
    <script type="application/ld+json">
        {!! json_encode($collectionSchema) !!}
    </script>
    <section class="page-hero">
        <div class="container">
            <x-web-breadcrumbs :items="[
                ['name' => 'Home', 'item' => '/'],
                ['name' => 'Blog', 'item' => '/blogs'],
                ['name' => $category['title'], 'item' => '/blogs/' . $category['slug']],
            ]" />
            <span class="eyebrow">Category Archive</span>
            <h1>{{ $category['title'] }}</h1>
            <p class="lead">{{ $category['short_description'] }}</p>
        </div>
    </section>

    <section class="section section--soft">
        <div class="container">
            @if(count($posts) === 0)
                <div class="center" style="padding: 40px 0; text-align: center;">
                    <p class="lead">No articles found in this category yet. Check back soon!</p>
                    <a href="/blogs" class="btn btn--lime">
                        Back to Blog
                    </a>
                </div>
            @else
                <div class="post-grid">
                    @foreach($posts as $post)
                        @php
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
                            <a href="/blogs/{{ $category['slug'] }}/{{ $post['slug'] }}">
                                <img class="post-card__img" src="{{ $coverImg }}" alt="{{ $post['title'] }}" loading="lazy">
                            </a>
                            <div class="post-card__body">
                                <span class="tag">{{ $category['title'] }}</span>
                                <h3>
                                    <a href="/blogs/{{ $category['slug'] }}/{{ $post['slug'] }}">{{ $post['title'] }}</a>
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
            @endif
        </div>
    </section>

    <section class="section cta-band">
        <div class="container">
            <h2>Ready to hear better?</h2>
            <p class="lead">Book a free, no-obligation hearing test with our team today.</p>
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