<div>
    <script type="application/ld+json">
        {!! json_encode($postSchema) !!}
    </script>
    @if($faqSchema)
        <script type="application/ld+json">
            {!! json_encode($faqSchema) !!}
        </script>
    @endif

    <main id="main">
        <section class="section">
            <div class="container">
                <div class="article">
                    <x-web-breadcrumbs :items="[
                        ['name' => 'Home', 'item' => '/'],
                        ['name' => 'Blog', 'item' => '/blogs'],
                        ['name' => $category['title'], 'item' => '/blogs/' . $category['slug']],
                    ]" />
                    
                    <div class="article__meta">
                        <span class="tag">{{ $category['title'] }}</span>
                        <span>{{ date('d M Y', strtotime($post['created_at'])) }}</span>
                        <span>·</span>
                        <span>5 min read</span>
                    </div>

                    <h1>{{ $post['title'] }}</h1>

                    <p class="article__review">
                        Written by 
                        @if($postModel->author)
                            <a href="/team/{{ $postModel->author->slug }}">{{ $postModel->author->name }}</a>, {{ $postModel->author->role }}
                        @else
                            {{ $post['author_name'] }}
                        @endif

                        @if($postModel->reviewer)
                            · Medically reviewed by <a href="/team/{{ $postModel->reviewer->slug }}">{{ $postModel->reviewer->name }}</a>
                        @elseif(!empty($post['reviewer_name']))
                            · Medically reviewed by {{ $post['reviewer_name'] }}
                        @endif

                        · Updated {{ date('d M Y', strtotime($post['updated_at'] ?? $post['created_at'])) }}
                    </p>

                    <img class="article__cover" src="{{ $coverImage }}" alt="{{ $post['title'] }}">

                    <!-- Article Body Content -->
                    <div class="fhc-article-content-body">
                        {!! $post['content'] !!}
                    </div>

                    <!-- Call To Action Banner -->
                    <div class="cta-inline">
                        <h3>Not sure which hearing aid is right for you?</h3>
                        <p>Get a free hearing test and an honest, expert recommendation — with a no-obligation trial.</p>
                        <a href="/book-a-test" class="btn btn--white btn--lg">Book Your Free Test</a>
                    </div>

                    <!-- Linked FAQs -->
                    @if(count($faqs) > 0)
                        <h2 id="faq" style="margin-top: 40px; margin-bottom: 20px;">Frequently asked questions</h2>
                        <div class="faq" x-data="{ openIndex: null }" style="margin-bottom: 40px;">
                            @foreach($faqs as $index => $faq)
                                <div class="faq__item">
                                    <button class="faq__q" :aria-expanded="openIndex === {{ $index }}" @click="openIndex = (openIndex === {{ $index }} ? null : {{ $index }})">
                                        <span>{{ $faq['question'] }}</span>
                                        <span class="ic">+</span>
                                    </button>
                                    <div class="faq__a" :class="{ 'open': openIndex === {{ $index }} }" x-show="openIndex === {{ $index }}" style="display: none;">
                                        <p>{!! $faq['answer'] !!}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($post['author_name']))
                        <!-- Author Box -->
                        <div class="author-box">
                            <img src="{{ $authorPhoto }}" alt="{{ $post['author_name'] }}">
                            <div>
                                <h4>{{ $post['author_name'] }}</h4>
                                <p>
                                    {{ $postModel->author->role ?? 'Audiologist & Clinical Lead at Fairfield Hearing Clinics.' }}
                                    <a href="{{ $postModel->author ? '/team/' . $postModel->author->slug : '/about' }}">View profile &rarr;</a>
                                </p>
                            </div>
                        </div>
                    @endif

                </div>

                @if(count($relatedPosts) > 0)
                    <!-- Related Articles Grid -->
                    <div style="max-width:1100px; margin:40px auto 0">
                        <h2 style="text-align:center">Related articles</h2>
                        <div class="post-grid">
                            @foreach($relatedPosts as $relPost)
                                <article class="post-card">
                                    <a href="/blogs/{{ $relPost->category->slug ?? 'hearing-health' }}/{{ $relPost->slug }}">
                                        <img class="post-card__img" src="{{ $relPost->featured_image ? \Illuminate\Support\Facades\Storage::url($relPost->featured_image) : (str_contains($relPost->slug, 'styletto') ? '/img/signia-styletto-ix-7ix-vs-5ix-vs-3ix.svg' : (str_contains($relPost->slug, 'severe') ? '/img/best-hearing-aids-for-severe-to-profound-loss.svg' : '/img/best-hearing-aids-for-senior-citizens.svg')) }}" alt="{{ $relPost->title }}" loading="lazy">
                                    </a>
                                    <div class="post-card__body">
                                        <span class="tag">{{ $relPost->category->title ?? 'Buying Guides' }}</span>
                                        <h3><a href="/blogs/{{ $relPost->category->slug ?? 'hearing-health' }}/{{ $relPost->slug }}">{{ $relPost->title }}</a></h3>
                                        <p>{{ Str::limit($relPost->summary, 120) }}</p>
                                        <div class="post-card__meta">
                                            <span>{{ date('d M Y', strtotime($relPost->created_at)) }}</span>
                                            <span>·</span>
                                            <span>5 min read</span>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </section>

        <section class="section cta-band">
            <div class="container">
                <h2>Have a hearing question?</h2>
                <p class="lead">Book a free, no-obligation hearing test with our RCI-certified audiologists.</p>
                <div class="cta-band__btns">
                    <a href="/book-a-test" class="btn btn--white btn--lg">Book Your Free Test</a>
                    <a href="tel:+919811418578" class="btn btn--lime btn--lg">📞 Call +91-9811418578</a>
                </div>
            </div>
        </section>
    </main>
</div>