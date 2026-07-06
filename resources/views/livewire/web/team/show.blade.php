<div>
    <script type="application/ld+json">
        {!! json_encode($physicianSchema) !!}
    </script>
    <section class="page-hero">
        <div class="container">
            <x-web-breadcrumbs :items="[
                ['name' => 'Home', 'item' => '/'],
                ['name' => 'About Us', 'item' => '/about'],
                ['name' => $member['name'], 'item' => '/team/' . $member['slug']],
            ]" />
            <span class="eyebrow">{{ $member['eyebrow'] ?: 'Our Specialist' }}</span>
            <h1>{{ $member['name'] }}</h1>
            <p class="lead">{{ $member['role'] }}</p>
        </div>
    </section>

    <section class="section">
        <div class="container profile">
            {{-- left: photo + facts --}}
            <div class="profile__photo">
                <img src="/{{ $member['photo'] }}" alt="{{ $member['name'] }}, {{ $member['role'] }}">
                
                @if(count($glanceItems) > 0)
                    <div class="profile__card">
                        <h4>At a glance</h4>
                        <ul>
                            @foreach($glanceItems as $item)
                                @php
                                    $parts = explode(':', $item, 2);
                                @endphp
                                @if(count($parts) > 1)
                                    <li><strong>{{ trim($parts[0]) }}:</strong>{{ $parts[1] }}</li>
                                @else
                                    <li>{{ $item }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if(count($expertiseItems) > 0)
                    <div class="profile__card">
                        <h4>Areas of expertise</h4>
                        <ul>
                            @foreach($expertiseItems as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <a href="/book-a-test" class="btn btn--block btn--lime" style="margin-top: 20px; display: inline-block; text-align: center;">
                    Book a Consultation
                </a>
            </div>

            {{-- right: bio --}}
            <div>
                @if($member['blockquote'])
                    <blockquote>&ldquo;{{ $member['blockquote'] }}&rdquo;</blockquote>
                @endif

                <h2>About {{ explode(' ', $member['name'])[0] }}</h2>
                <div class="bio-content">
                    {!! $member['bio'] !!}
                </div>

                @if(count($timelineItems) > 0)
                    <h2 style="margin-top: 1.2em;">Career &amp; qualifications</h2>
                    <ul class="timeline">
                        @foreach($timelineItems as $item)
                            <li>
                                <span class="yr">{{ $item['year'] ?? '' }}</span> — {{ $item['text'] ?? '' }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                <p style="margin-top: 1.4em;">
                    <a href="/about">
                        &larr; Back to our team
                    </a>
                </p>
            </div>
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