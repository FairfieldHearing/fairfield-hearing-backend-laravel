<div>
<section class="page-hero">
        <div class="container">
          <x-web-breadcrumbs :items="[
              [ 'name' => 'Home', 'item' => '/' ],
              [ 'name' => 'Book a Free Test', 'item' => '/book-a-test' ],
            ]" />
          <span class="eyebrow">100% free • No obligation</span>
          <h1>Book your free hearing test</h1>
          <p class="lead">
            Fill in the form and our team will call to confirm your preferred time. Prefer to talk now? Call&nbsp;
            <a href="tel:+919811418578">+91-9811418578</a> or message us on WhatsApp.
          </p>
        </div>
      </section>

      <section class="section">
        <div class="container locator">
          {{-- Form --}}
          <livewire:full-booking-form :locations="$locations" />

          {{-- Reassurance / contact --}}
          <div>
            <h2>What to expect</h2>
            <div class="feat">
              <div class="feat__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <circle cx="12" cy="12" r="9" />
                  <path d="M12 7v5l3 2" />
                </svg>
              </div>
              <div>
                <h3>About 45 minutes</h3>
                <p>A relaxed, painless assessment — no needles, no discomfort.</p>
              </div>
            </div>
            <div class="feat">
              <div class="feat__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M9 12l2 2 4-4" />
                  <circle cx="12" cy="12" r="9" />
                </svg>
              </div>
              <div>
                <h3>Clear results, explained simply</h3>
                <p>Your audiologist walks you through your results and options — no pressure to buy.</p>
              </div>
            </div>
            <div class="feat">
              <div class="feat__ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M12 21s-7-4.5-7-10a4 4 0 017-2.6A4 4 0 0119 11c0 5.5-7 10-7 10z" />
                </svg>
              </div>
              <div>
                <h3>Free trial if you need a device</h3>
                <p>Try recommended hearing aids in real life before you decide anything.</p>
              </div>
            </div>

            <div class="clinic" style="marginTop: 24px;">
              <h3 class="mt0">Prefer to book instantly?</h3>
              <p>Message us on WhatsApp or call — we usually respond within minutes during clinic hours.</p>
              <div class="clinic__links">
                <a
                  class="btn btn--wa"
                  href="https://wa.me/919811551399?text=Hi%20Fairfield%2C%20I'd%20like%20to%20book%20a%20free%20hearing%20test."
                  target="_blank"
                  rel="noopener"
                >
                  WhatsApp Us
                </a>
                <a class="btn btn--ghost" href="tel:+919811418578">
                  Call Now
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>
</div>