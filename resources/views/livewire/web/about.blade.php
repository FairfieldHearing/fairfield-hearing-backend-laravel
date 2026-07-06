<div>
<script type="application/ld+json">{!! json_encode($aboutSchema) !!}</script>
      <section class="page-hero">
        <div class="container">
          <x-web-breadcrumbs :items="[
              [ 'name' => 'Home', 'item' => '/' ],
              [ 'name' => 'About', 'item' => '/about' ],
            ]" />
          <span class="eyebrow">Who we are</span>
          <h1>Honest, expert hearing care</h1>
          <p class="lead">
            At Fairfield Hearing Clinics, better hearing isn't a transaction — it's a relationship built on trust,
            expertise and genuine care.
          </p>
        </div>
      </section>

      <section class="section">
        <div class="container split">
          <div>
            <h2>Our mission</h2>
            <p>
              We believe everyone deserves to hear the moments that matter — a grandchild's laugh, a favourite song, a
              conversation across the dinner table. Our RCI-certified audiologists combine advanced diagnostics with
              patience and honesty, so you always understand your options and never feel pressured.
            </p>
            <p>
              From your first free hearing test to years of aftercare, we're with you every step of the way. We carry over
              500 models from the world's leading manufacturers, and we recommend only what genuinely fits your hearing,
              your lifestyle and your budget.
            </p>
            <a href="/book-a-test">
              Book a Free Test
            </a>
          </div>
          <div class="media-frame">
            <img src="/assets/img/ab.png" alt="Fairfield Hearing Clinics audiologist conducting a hearing test on a patient" >
          </div>
        </div>
      </section>

      <section class="section section--cream">
        <div class="container center">
          <span class="eyebrow">What guides us</span>
          <h2>Our values</h2>
        </div>
        <div class="container">
          <div class="cards">
            <article class="card">
              <div class="card__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M9 12l2 2 4-4" />
                  <circle cx="12" cy="12" r="9" />
                </svg>
              </div>
              <h3>Expertise</h3>
              <p>Qualified, RCI-registered audiologists using evidence-based testing and the latest fitting technology.</p>
            </article>
            <article class="card">
              <div class="card__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M12 1v22M5 8h9a3 3 0 010 6H8" />
                </svg>
              </div>
              <h3>Transparency</h3>
              <p>Clear pricing, honest advice and free trials. You decide — always at your own pace.</p>
            </article>
            <article class="card">
              <div class="card__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M12 21s-7-4.5-7-10a4 4 0 017-2.6A4 4 0 0119 11c0 5.5-7 10-7 10z" />
                </svg>
              </div>
              <h3>Compassion</h3>
              <p>We listen first. Every recommendation is shaped around your needs and comfort.</p>
            </article>
          </div>
        </div>
      </section>

      {{-- Meet our team --}}
      <section class="section" id="team">
        <div class="container center">
          <span class="eyebrow">Our team</span>
          <h2>Meet our team</h2>
          <p class="lead">Experienced, qualified professionals dedicated to your hearing health.</p>
        </div>
        <div class="container">
          {{-- Directors --}}
          @if(count($directors) > 0)
              <p class="team-sub">Director</p>
              @foreach($directors as $member)
                <article class="card doc-card" style="margin-bottom: 24px;">
                  <img
                    class="doc-card__photo"
                    src="/{{ $member['photo'] }}"
                    alt="{{ $member['name'] }}, {{ $member['role'] }}"
                  >
                  <div>
                    <h3>{{ $member['name'] }}</h3>
                    <p class="role">{{ $member['role'] }}</p>
                    <p>{{ $member['short_bio'] }}</p>
                    <a href="/team/{{ $member['slug'] }}">
                      Read full profile &rarr;
                    </a>
                  </div>
                </article>
              @endforeach
          @endif

          @if(count($entSpecialists) > 0)
              <p class="team-sub" style="margin-top: 44px;">
                Our ENT Specialist
              </p>
              @foreach($entSpecialists as $member)
                <article class="card doc-card" style="margin-bottom: 24px;">
                  <img
                    class="doc-card__photo"
                    src="/{{ $member['photo'] }}"
                    alt="{{ $member['name'] }}, {{ $member['role'] }}"
                  >
                  <div>
                    <h3>{{ $member['name'] }}</h3>
                    <p class="role">{{ $member['role'] }}</p>
                    @if($member['blockquote'])
                        <p class="quote">{{ $member['blockquote'] }}</p>
                    @endif
                    <p>{{ $member['short_bio'] }}</p>
                    <a href="/team/{{ $member['slug'] }}">
                      Read full profile &rarr;
                    </a>
                  </div>
                </article>
              @endforeach
          @endif

          @if(count($audiologists) > 0)
              <p class="team-sub" style="margin-top: 44px;">
                Our Audiologists
              </p>
              <div class="team-grid">
                @foreach($audiologists as $member)
                  <article class="person-card">
                    <img
                      class="person-card__photo"
                      src="/{{ $member['photo'] }}"
                      alt="{{ $member['name'] }}, {{ $member['role'] }}"
                    >
                    <div class="person-card__body">
                      <h3>{{ $member['name'] }}</h3>
                      <p class="role">{{ $member['role'] }}</p>
                      <p>{{ $member['short_bio'] }}</p>
                      <a href="/team/{{ $member['slug'] }}">
                        View profile &rarr;
                      </a>
                    </div>
                  </article>
                @endforeach
              </div>
          @endif

          @if(count($productSpecialists) > 0)
              <p class="team-sub" style="margin-top: 44px;">
                Product Specialists & Technicians
              </p>
              <div
                class="team-grid"
                style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); max-width: 720px; margin-inline: auto;"
              >
                @foreach($productSpecialists as $member)
                  <article class="person-card">
                    <img
                      class="person-card__photo"
                      src="/{{ $member['photo'] }}"
                      alt="{{ $member['name'] }}, {{ $member['role'] }}"
                    >
                    <div class="person-card__body">
                      <h3>{{ $member['name'] }}</h3>
                      <p class="role">{{ $member['role'] }}</p>
                      <p>{{ $member['short_bio'] }}</p>
                      <a href="/team/{{ $member['slug'] }}">
                        View profile &rarr;
                      </a>
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