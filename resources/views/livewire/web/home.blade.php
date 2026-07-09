<div>
<script type="application/ld+json">
    {!! json_encode($medicalBusinessSchema) !!}
</script>
@if($faqSchema)
    <script type="application/ld+json">
        {!! json_encode($faqSchema) !!}
    </script>
@endif
      {{-- ================= HERO ================= --}}
      <section class="hero">
        <svg class="hero__wave" viewBox="0 0 1440 600" preserveAspectRatio="none" aria-hidden="true">
          <g fill="none" stroke="#aacc00" strokeWidth="1.2" opacity="0.5">
            <path d="M-50,120 C300,40 600,200 1490,60" />
            <path d="M-50,150 C300,70 600,230 1490,90" />
            <path d="M-50,180 C300,100 600,260 1490,120" />
            <path d="M-50,210 C300,130 600,290 1490,150" />
          </g>
        </svg>
        <div class="container hero__inner">
          <div class="hero__text">
            <span class="eyebrow">Rediscover the sounds you love</span>
            <h1>
              Better hearing starts with a <span class="accent">free hearing test</span>
            </h1>
            <p class="hero__lead">
              Personalised hearing care from RCI-certified audiologists. Test, trial and find the perfect hearing aid
              — with transparent pricing, EMI and two years of free aftercare.
            </p>
            <div class="hero__cta">
              <a href="/book-a-test" class="btn btn--lg">
                Book Your Free Test
              </a>
              <a href="https://wa.me/919811551399" class="btn btn--wa btn--lg" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                  <path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.82 11.82 0 018.413 3.488 11.82 11.82 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24zM6.597 20.13c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.82 9.82 0 001.51 5.26l-.999 3.648 3.477-.607zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                </svg>
                WhatsApp Us
              </a>
            </div>
            <div class="hero__trust">
              <span>
                <span class="star">★★★★★</span> 4.9 Google rating
              </span>
              <span>• 25,000+ patients served</span>
              <span>• 500+ device models</span>
            </div>
          </div>

          <div class="hero__media">
            <div class="hero__badge">100% FREE • No obligation</div>
            <livewire:hero-booking-form :locations="$locations" />
          </div>
        </div>
      </section>

      {{-- ================= TRUST STRIP ================= --}}
      <section class="trust" aria-label="Why patients trust Fairfield">
        <div class="container trust__row">
          <div>
            <div class="trust__num">25k+</div>
            <div class="trust__lbl">Patients served</div>
          </div>
          <div>
            <div class="trust__num">RCI</div>
            <div class="trust__lbl">Certified audiologists</div>
          </div>
          <div>
            <div class="trust__num">500+</div>
            <div class="trust__lbl">Hearing aid models</div>
          </div>
          <div>
            <div class="trust__num">2 yrs</div>
            <div class="trust__lbl">Free aftercare</div>
          </div>
        </div>
      </section>

      {{-- ================= HOW IT WORKS ================= --}}
      <section class="section" id="how">
        <div class="container center">
          <span class="eyebrow">Simple & stress-free</span>
          <h2>Your journey to better hearing</h2>
          <p class="lead">Four easy steps — guided by an expert audiologist at every stage.</p>
        </div>
        <div class="container">
          <div class="steps">
            <div class="step">
              <div class="step__n">1</div>
              <h3>Book a free test</h3>
              <p>Reserve a slot online, by phone or on WhatsApp — no cost, no pressure.</p>
            </div>
            <div class="step">
              <div class="step__n">2</div>
              <h3>Full assessment</h3>
              <p>A detailed, evidence-based hearing evaluation with advanced diagnostics.</p>
            </div>
            <div class="step">
              <div class="step__n">3</div>
              <h3>Trial the right device</h3>
              <p>Experience recommended hearing aids free, at home or in clinic.</p>
            </div>
            <div class="step">
              <div class="step__n">4</div>
              <h3>Ongoing care</h3>
              <p>Fine-tuning, servicing and support for years — not just a one-off sale.</p>
            </div>
          </div>
        </div>
      </section>

      {{-- ================= SERVICES ================= --}}
      <section class="section section--soft" id="services">
        <div class="container center">
          <span class="eyebrow">What we do</span>
          <h2>Comprehensive hearing care</h2>
          <p class="lead">Everything you need under one roof, delivered by qualified audiologists.</p>
        </div>
        <div class="container">
          <div class="cards">
            <article class="card">
              <div class="card__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M12 3a9 9 0 00-9 9v3a3 3 0 003 3h1v-7H5a7 7 0 0114 0h-2v7h1a3 3 0 003-3v-3a9 9 0 00-9-9z" />
                </svg>
              </div>
              <h3>Free Hearing Tests</h3>
              <p>
                Accurate, painless assessments using calibrated equipment and Real Ear Measurement for a precise
                picture of your hearing.
              </p>
              <a href="/book-a-test">
                Book now →
              </a>
            </article>
            <article class="card">
              <div class="card__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M4 13a8 8 0 0116 0M4 13v3a2 2 0 002 2h1v-5H4zm16 0v3a2 2 0 01-2 2h-1v-5h3z" />
                  <circle cx="12" cy="13" r="2" />
                </svg>
              </div>
              <h3>Hearing Aid Fitting</h3>
              <p>
                Expert selection and precision tuning of devices matched to your hearing, lifestyle and budget — from
                invisible to rechargeable models.
              </p>
              <a href="/#devices">
                Explore devices →
              </a>
            </article>
            <article class="card">
              <div class="card__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M12 21s-7-4.5-7-10a4 4 0 017-2.6A4 4 0 0119 11c0 5.5-7 10-7 10z" />
                </svg>
              </div>
              <h3>Tinnitus Management</h3>
              <p>
                Personalised therapy and devices to relieve ringing in the ears and help you focus on the sounds that
                matter.
              </p>
              <a href="/#clinics">
                Talk to us →
              </a>
            </article>
            <article class="card">
              <div class="card__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <circle cx="12" cy="8" r="4" />
                  <path d="M4 21a8 8 0 0116 0" />
                </svg>
              </div>
              <h3>Paediatric Audiology</h3>
              <p>
                Gentle, child-friendly hearing assessment and care, supporting healthy speech and language
                development.
              </p>
              <a href="/#clinics">
                Learn more →
              </a>
            </article>
            <article class="card">
              <div class="card__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M3 12h4l2 5 4-12 2 7h6" />
                </svg>
              </div>
              <h3>Ear Wax Removal</h3>
              <p>
                Safe, professional ear-wax removal for instant comfort and clearer hearing, performed by trained
                clinicians.
              </p>
              <a href="/#clinics">
                Book a visit →
              </a>
            </article>
            <article class="card">
              <div class="card__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M3 7h18M6 7V5a2 2 0 012-2h8a2 2 0 012 2v2M8 11v6M12 11v6M16 11v6" />
                </svg>
              </div>
              <h3>Repairs & Aftercare</h3>
              <p>
                Servicing, cleaning, reprogramming and battery support for all major brands — with two years of free
                aftercare.
              </p>
              <a href="/#clinics">
                Get support →
              </a>
            </article>
          </div>
        </div>
      </section>

      {{-- ================= DEVICE CATEGORIES ================= --}}
      <section class="section" id="devices">
        <div class="container center">
          <span class="eyebrow">Hearing aids for every need</span>
          <h2>From invisible to fully connected</h2>
          <p class="lead">Over 500 models from the world's leading manufacturers — we match the technology to your life.</p>
          <div class="chips">
            <a class="chip" href="/invisible">
              <span class="dot"></span> Invisible (IIC/CIC)
            </a>
            <a class="chip" href="/ric">
              <span class="dot"></span> Receiver-in-Canal (RIC)
            </a>
            <a class="chip" href="/bte">
              <span class="dot"></span> Behind-the-Ear (BTE)
            </a>
            <a class="chip" href="/rechargeable">
              <span class="dot"></span> Rechargeable
            </a>
            <a class="chip" href="/bluetooth">
              <span class="dot"></span> Bluetooth streaming
            </a>
            <a class="chip" href="/tinnitus">
              <span class="dot"></span> Tinnitus relief
            </a>
          </div>
          <div class="brands" aria-label="Brands we offer">
            @foreach($manufacturers as $m)
              <span>
                <img src="{{ $m->logo_url }}" alt="{{ $m->name }}" style="width: 120px;" >
              </span>
            @endforeach
          </div>
        </div>
      </section>

      {{-- ================= WHY US ================= --}}
      <section class="section section--cream">
        <div class="container split">
          <div>
            <span class="eyebrow">Why Fairfield</span>
            <h2>Hearing care you can trust</h2>
            <div class="feat">
              <div class="feat__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M9 12l2 2 4-4" />
                  <circle cx="12" cy="12" r="9" />
                </svg>
              </div>
              <div>
                <h3>RCI-certified experts</h3>
                <p>Every test and fitting is carried out by qualified, registered audiologists.</p>
              </div>
            </div>
            <div class="feat">
              <div class="feat__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M12 1v22M5 8h9a3 3 0 010 6H8" />
                </svg>
              </div>
              <div>
                <h3>Transparent pricing & EMI</h3>
                <p>Clear prices, free trials and easy instalments — no surprises, ever.</p>
              </div>
            </div>
            <div class="feat">
              <div class="feat__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M20 7L9 18l-5-5" />
                </svg>
              </div>
              <div>
                <h3>Free trials before you buy</h3>
                <p>Experience your recommended device in real life before making a decision.</p>
              </div>
            </div>
            <div class="feat">
              <div class="feat__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M12 21s-7-4.5-7-10a4 4 0 017-2.6A4 4 0 0119 11c0 5.5-7 10-7 10z" />
                </svg>
              </div>
              <div>
                <h3>Lifelong aftercare</h3>
                <p>Two years of free servicing and a relationship that lasts well beyond the sale.</p>
              </div>
            </div>
          </div>
          <div class="media-frame">
            <img src="/assets/img/why-us.png" alt="Audiologist fitting a hearing aid for a patient" >
          </div>
        </div>
      </section>

      {{-- ================= TESTIMONIALS ================= --}}
      
    <!-- TESTIMONIALS SLIDER (Alpine.js) -->
    <section class="section" id="testimonials" x-data="{ 
        activeIndex: 0,
        slidesPerView: 3,
        slidesCount: 4,
        get totalPages() { return Math.max(1, this.slidesCount - this.slidesPerView + 1) },
        init() {
            const checkSize = () => {
                if (window.innerWidth < 640) this.slidesPerView = 1;
                else if (window.innerWidth < 1024) this.slidesPerView = 2;
                else this.slidesPerView = 3;
            };
            checkSize();
            window.addEventListener('resize', checkSize);
        }
    }">
      <div class="container center">
        <span class="eyebrow">Patient Success Stories</span>
        <h2>Real Stories of Clinical Success</h2>
        <p class="lead" style="margin-top: 10px; margin-bottom: 40px;">
          Discover how expert audiologists and world-class hearing technology are transforming lives across India.
        </p>
      </div>

      <div class="container relative overflow-hidden" style="max-width: 1220px;">
        <div class="swiper testimonial-swiper" style="overflow: hidden;">
          <div class="swiper-wrapper" style="display: flex; transition: transform 0.4s ease; gap: 20px;" :style="{ transform: `translateX(-${activeIndex * (100 / slidesPerView)}%)` }">
            
            <div class="swiper-slide" :style="{ flexShrink: 0, width: `calc(${100 / slidesPerView}% - ${(20 * (slidesPerView - 1)) / slidesPerView}px)` }">
                <div class="video-card">
                  <div class="video-wrapper">
                    <iframe src="https://www.youtube.com/embed/jgZqTJtMe80" title="Severe Hearing Loss" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                  </div>
                  <div class="video-info">
                    <h4>Severe Hearing Loss</h4>
                    <p class="quote-text">"I regained my confidence and social life after visiting Fairfield Clinic."</p>
                    <p class="patient-info">Verified Patient</p>
                  </div>
                </div>
            </div>

            <div class="swiper-slide" :style="{ flexShrink: 0, width: `calc(${100 / slidesPerView}% - ${(20 * (slidesPerView - 1)) / slidesPerView}px)` }">
                <div class="video-card">
                  <div class="video-wrapper">
                    <iframe src="https://www.youtube.com/embed/wG50UmVvtQs" title="Bilateral Tinnitus" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                  </div>
                  <div class="video-info">
                    <h4>Bilateral Tinnitus</h4>
                    <p class="quote-text">"The specialized care and the right hearing aid made all the difference."</p>
                    <p class="patient-info">Verified Patient</p>
                  </div>
                </div>
            </div>

            <div class="swiper-slide" :style="{ flexShrink: 0, width: `calc(${100 / slidesPerView}% - ${(20 * (slidesPerView - 1)) / slidesPerView}px)` }">
                <div class="video-card">
                  <div class="video-wrapper">
                    <iframe src="https://www.youtube.com/embed/ay6jXkLWrMo" title="Moderate Impairment" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                  </div>
                  <div class="video-info">
                    <h4>Moderate Impairment</h4>
                    <p class="quote-text">"World-class technology with a personal touch. Highly recommended."</p>
                    <p class="patient-info">Verified Patient</p>
                  </div>
                </div>
            </div>

            <div class="swiper-slide" :style="{ flexShrink: 0, width: `calc(${100 / slidesPerView}% - ${(20 * (slidesPerView - 1)) / slidesPerView}px)` }">
                <div class="video-card">
                  <div class="video-wrapper">
                    <iframe src="https://www.youtube.com/embed/a0VAfEAUWXw" title="Sudden Hearing Loss" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                  </div>
                  <div class="video-info">
                    <h4>Sudden Hearing Loss</h4>
                    <p class="quote-text">"Fast diagnosis and perfect fitting helped me return to work quickly."</p>
                    <p class="patient-info">Verified Patient</p>
                  </div>
                </div>
            </div>

          </div>

          <!-- Swiper Pagination Dots -->
          <div class="swiper-pagination" style="position: static; margin-top: 24px; display: flex; justify-content: center; gap: 8px;">
            <template x-for="i in Array.from({length: totalPages}).map((_, idx) => idx)">
              <span class="swiper-pagination-bullet" :class="{ 'swiper-pagination-bullet-active': activeIndex === i }" style="cursor: pointer; width: 10px; height: 10px; border-radius: 50%; display: inline-block; transition: background-color 0.3s;" :style="{ backgroundColor: activeIndex === i ? '#a8cf45' : '#ccc' }" @click="activeIndex = i"></span>
            </template>
          </div>

          <!-- Navigation Arrows -->
          <template x-if="totalPages > 1">
            <div>
              <div class="swiper-button-prev" @click="activeIndex = (activeIndex - 1 + totalPages) % totalPages" style="position: absolute; top: 40%; left: 10px; cursor: pointer; z-index: 10; color: '#a8cf45'; font-weight: bold; font-size: 24px; user-select: none;">&#10094;</div>
              <div class="swiper-button-next" @click="activeIndex = (activeIndex + 1) % totalPages" style="position: absolute; top: 40%; right: 10px; cursor: pointer; z-index: 10; color: '#a8cf45'; font-weight: bold; font-size: 24px; user-select: none;">&#10095;</div>
            </div>
          </template>
        </div>
      </div>
    </section>
    

      {{-- ================= CTA BAND ================= --}}
      <section class="section cta-band">
        <div class="container">
          <h2>Take the first step today — it's free</h2>
          <p class="lead">
            Book a no-obligation hearing test with an RCI-certified audiologist. Same-week appointments available.
          </p>
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

      {{-- ================= CLINIC LOCATOR ================= --}}
      
    <!-- CLINIC LOCATOR (Alpine.js) -->
    <section class="section section--soft" id="clinics" x-data="{
        selectedClinicId: null,
        locations: {{ json_encode($locations) }},
        get activeClinic() {
            return this.locations.find(l => l.id === this.selectedClinicId) || this.locations.find(l => l.is_main) || this.locations[0];
        },
        get embedUrl() {
            const loc = this.activeClinic;
            if (!loc) return '';
            // For the main clinic, use the specific google_maps_link embed URL
            if (loc.is_main && loc.google_maps_link && loc.google_maps_link.includes('maps/embed')) {
                return loc.google_maps_link;
            }
            // For other regional clinics, embed the map pinpointing their exact street address
            const query = loc.address_line1;
            return `https://maps.google.com/maps?q=${encodeURIComponent(query)}&t=&z=15&ie=UTF8&iwloc=&output=embed`;
        },
        init() {
            const main = this.locations.find(l => l.is_main) || this.locations[0];
            if (main) this.selectedClinicId = main.id;
        }
    }">
      <div class="container center" style="margin-bottom: 8px;">
        <span class="eyebrow">Find us</span>
        <h2>Our clinics</h2>
        <p class="lead">Visit a Fairfield Hearing Clinic near you, or request a home visit.</p>
      </div>

      <div class="container locator">
        <div id="clinic-list">
          <template x-for="loc in locations" :key="loc.id">
            <article class="clinic" :class="{ 'active-clinic': selectedClinicId === loc.id }" @click="selectedClinicId = loc.id">
              <h3 x-text="loc.name"></h3>
              <p x-text="loc.address_line1"></p>
              <p x-text="loc.availability"></p>
              <div class="clinic__links">
                <a class="btn btn--ghost" :href="`tel:${loc.phone}`">Call</a>
                <a class="btn btn--wa" :href="`https://wa.me/${loc.whatsapp}`" target="_blank" rel="noopener">WhatsApp</a>
                <a class="btn btn--lime" :href="loc.google_maps_link && !loc.google_maps_link.includes('maps/embed') && !loc.google_maps_link.includes('output=embed') ? loc.google_maps_link : `https://maps.google.com/?q=${encodeURIComponent(loc.is_main ? loc.name : loc.address_line1)}`" target="_blank" rel="noopener" @click.stop>Directions</a>
              </div>
            </article>
          </template>
        </div>

        <div class="map-embed">
          <template x-if="activeClinic">
            <iframe id="main-map-frame" :src="embedUrl" width="100%" height="450" style="border: 0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </template>
        </div>
      </div>
    </section>
    

      {{-- ================= FAQ ACCORDION ================= --}}
      
    <!-- FAQ ACCORDION (Alpine.js) -->
    <section class="section" id="faq">
      <div class="container center">
        <span class="eyebrow">Good to know</span>
        <h2>Frequently asked questions</h2>
      </div>
      <div class="container">
        <div class="faq" x-data="{ openIndex: null }">
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
      </div>
    </section>
</div>