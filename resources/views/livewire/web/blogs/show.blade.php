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
                        @if($post['author_name'] === 'Wasiq Ali Khan')
                            <a href="/team/wasiq-ali-khan">{{ $post['author_name'] }}</a>, Senior Audiologist & Clinical Lead · Medically reviewed by <a href="/team/dr-nayeem-ahmad-siddiqui">Dr. Nayeem Ahmad Siddiqui</a>
                        @else
                            {{ $post['author_name'] }}
                        @endif
                        · Updated {{ date('d M Y', strtotime($post['updated_at'] ?? $post['created_at'])) }}
                    </p>

                    <img class="article__cover" src="{{ $coverImage }}" alt="{{ $post['title'] }}">



                    <!-- Article Body Content -->
                    <div class="fhc-article-content-body">
                        {!! $post['content'] !!}
                    </div>

                    <!-- Call To Action Banner -->
                    <div class="cta-inline"><h3>Not sure which hearing aid is right for you?</h3><p>Get a free hearing test and an honest, expert recommendation — with a no-obligation trial.</p><a href="/book-a-test" class="btn btn--white btn--lg">Book Your Free Test</a></div>

                    <!-- Linked FAQs -->
                    @if(count($faqs) > 0)
                        <h2 style="margin-top: 40px; margin-bottom: 20px;">Frequently asked questions</h2>
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

                    @if($post['author_name'] === 'Wasiq Ali Khan')
                        <!-- Author Box -->
                        <div class="author-box" style="margin-top: 40px; display: flex; align-items: center; gap: 20px; padding: 20px; background: #f9f9f9; border-radius: 8px;">
                            <img src="{{ $authorPhoto }}" alt="{{ $post['author_name'] }}" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <h4 style="margin: 0 0 5px 0; font-size: 1.1em; color: #1b1b1b;">{{ $post['author_name'] }}</h4>
                                <p style="margin: 0; font-size: 0.9em; color: #666;">
                                    Senior Audiologist & Clinical Lead at Fairfield Hearing Clinics.
                                    <a href="/about" style="color: #6b8e23; font-weight: 600; text-decoration: none;">View profile &rarr;</a>
                                </p>
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