<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clear existing seed data to ensure tracking
        Schema::disableForeignKeyConstraints();
        DB::table('policy_pages')->truncate();
        DB::table('locations')->truncate();
        DB::table('blog_categories')->truncate();
        DB::table('blog_posts')->truncate();
        DB::table('faqs')->truncate();
        DB::table('team_members')->truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Insert Locations
        $locations = [
            [
                'name' => 'Fairfield Hearing Clinic — Delhi NFC (Main Clinic)',
                'is_main' => true,
                'address_line1' => '139 A, GROUND FLOOR, MAIN ROAD, TAIMOOR NAGAR, NEW FRIENDS COLONY, NEW DELHI, DELHI 110025',
                'address_line2' => '',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'postal_code' => '110025',
                'country' => 'India',
                'availability' => 'Mon–Sat: 10:00 AM – 7:00 PM',
                'phone' => '+919811418578',
                'whatsapp' => '919811551399',
                'google_maps_link' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14015.715249803048!2d77.2662763!3d28.5719003!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce317b2d1e80d%3A0x699abb1c1bbe4231!2sFairfield%20Hearing%20Clinics%20%7C%20Ai%20Hearing%20Aid%20%7C%20Top%20Hearing%20aid%20consultants%20in%20Delhi%20%7C%20Hearing%20Test%20Near%20by%20you!5e0!3m2!1sen!2sin!4v1781983041689!5m2!1sen!2sin',
                'meta_title' => 'Fairfield Hearing Clinic — Delhi NFC (Main Clinic) | Fairfield Hearing Clinics',
                'meta_description' => 'Visit Fairfield Hearing Clinic — Delhi NFC (Main Clinic) for professional hearing assessments, fittings, and trials.',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fairfield Hearing Clinic — Delhi Hari Nagar',
                'is_main' => false,
                'address_line1' => 'SHOP NO 2, WZ,406-L-1, PLOT NUMBER 54, JANAK PARK, HARI NAGAR, DELHI, 110064',
                'address_line2' => '',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'postal_code' => '110064',
                'country' => 'India',
                'availability' => 'Mon–Sat: 10:00 AM – 7:00 PM',
                'phone' => '+919811418578',
                'whatsapp' => '919811551399',
                'google_maps_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3502.1385412953285!2d77.10446107616641!3d28.625615784391295!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d035b86e2468f%3A0x88c9fecad66212c!2sHari%20Nagar%2C%20New%20Delhi%2C%20Delhi!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin',
                'meta_title' => 'Fairfield Hearing Clinic — Delhi Hari Nagar | Fairfield Hearing Clinics',
                'meta_description' => 'Visit Fairfield Hearing Clinic — Delhi Hari Nagar for professional hearing assessments, fittings, and trials.',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fairfield Hearing Clinic — Delhi Sukhdev Vihar',
                'is_main' => false,
                'address_line1' => 'FLAT NO 48, GROUND FLOOR, DDA FLAT, POCKET B, SUKHDEV VIHAR, NEW DELHI-110025',
                'address_line2' => '',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'postal_code' => '110025',
                'country' => 'India',
                'availability' => 'Mon–Sat: 10:00 AM – 7:00 PM',
                'phone' => '+919811418578',
                'whatsapp' => '919811551399',
                'google_maps_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3503.957599723223!2d77.27110757616422!3d28.571040386927367!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce3a2b7218683%3A0xe54fb1d3ff28b584!2sSukhdev%20Vihar%2C%20New%20Delhi%2C%20Delhi!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin',
                'meta_title' => 'Fairfield Hearing Clinic — Delhi Sukhdev Vihar | Fairfield Hearing Clinics',
                'meta_description' => 'Visit Fairfield Hearing Clinic — Delhi Sukhdev Vihar for professional hearing assessments, fittings, and trials.',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fairfield Hearing Clinic — Uttar Pradesh Kaimganj',
                'is_main' => false,
                'address_line1' => 'SHOP NO-9, SULTAN MARKET, NEAR A P PUBLIC SCHOOL, KAMPIL ROAD, KAIMGANJ, FARRUKHABAD, U.P - 209502',
                'address_line2' => '',
                'city' => 'Kaimganj',
                'state' => 'Uttar Pradesh',
                'postal_code' => '209502',
                'country' => 'India',
                'availability' => 'Mon–Sat: 10:00 AM – 7:00 PM',
                'phone' => '+919811418578',
                'whatsapp' => '919811551399',
                'google_maps_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14188.751222476562!2d79.34024346146602!3d27.498871633513364!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399e557f339efbd1%3A0xf639890bc41d2275!2sKaimganj%2C%20Uttar%20Pradesh%20209502!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin',
                'meta_title' => 'Fairfield Hearing Clinic — Uttar Pradesh Kaimganj | Fairfield Hearing Clinics',
                'meta_description' => 'Visit Fairfield Hearing Clinic — Uttar Pradesh Kaimganj for professional hearing assessments, fittings, and trials.',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('locations')->insert($locations);

        // 3. Insert Blog Categories
        $categories = [
            [
                'title' => 'Buying Guides',
                'slug' => 'buying-guides',
                'short_description' => 'Expert “best hearing aid for…” guides to help you choose the right device with confidence.',
                'meta_title' => 'Hearing Aid Buying Guides | Fairfield Hearing',
                'meta_description' => 'Compare and choose the best hearing aid styles and models with our expert audiologist guides.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Brand & Model Comparisons',
                'slug' => 'comparisons',
                'short_description' => 'Honest, side-by-side comparisons of leading brands and models so you can pick the best fit.',
                'meta_title' => 'Hearing Aid Brand Comparisons | Fairfield Hearing',
                'meta_description' => 'Compare Signia vs Phonak vs Widex and other leading brands side by side.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Prices & Cost in India',
                'slug' => 'prices',
                'short_description' => 'Transparent price guides, EMI options and value advice for hearing-aid buyers in India.',
                'meta_title' => 'Hearing Aid Prices & Cost in India | Fairfield Hearing',
                'meta_description' => 'Find transparent pricing information and EMI options for hearing aids in India.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Types & Technology',
                'slug' => 'types-technology',
                'short_description' => 'Invisible, rechargeable, Bluetooth and AI hearing-aid technology, clearly explained.',
                'meta_title' => 'Hearing Aid Types & Technology | Fairfield Hearing',
                'meta_description' => 'Learn about rechargeable, invisible, Bluetooth, and AI hearing aid models.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Hearing Loss & Conditions',
                'slug' => 'hearing-loss',
                'short_description' => 'Understanding hearing loss, tinnitus and related ear conditions in adults and children.',
                'meta_title' => 'Hearing Loss & Ear Conditions | Fairfield Hearing',
                'meta_description' => 'Read clinical guides on hearing loss, tinnitus, and pediatric audiology conditions.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Care & Maintenance',
                'slug' => 'care-maintenance',
                'short_description' => 'Cleaning, batteries, troubleshooting and getting the most from your hearing aids.',
                'meta_title' => 'Hearing Aid Care & Troubleshooting | Fairfield Hearing',
                'meta_description' => 'Tips on maintaining, cleaning, and caring for your digital hearing aids.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('blog_categories')->insert($categories);

        // Get category IDs
        $categoryIds = DB::table('blog_categories')->pluck('id', 'slug')->toArray();

        // 4. Insert Blog Posts & Link them
        $postId = DB::table('blog_posts')->insertGetId([
            'blog_category_id' => $categoryIds['buying-guides'],
            'title' => 'Best Hearing Aids for Senior Citizens (2026 Edition)',
            'slug' => 'best-hearing-aids-for-senior-citizens',
            'summary' => 'For most senior citizens, the best hearing aids are rechargeable receiver-in-canal (RIC) devices with automatic noise management and simple Bluetooth. They are easy to handle, comfortable for all-day wear and clear in conversation. In 2026, our audiologists most often recommend the Phonak Infinio, Signia IX, ReSound Vivia, Widex Moment and Bernafon Encanta families for older adults.',
            'content' => '<p>For most senior citizens, the best hearing aids are <strong>rechargeable receiver-in-canal (RIC) devices with automatic noise management and simple Bluetooth</strong>. They are easy to handle, comfortable for all-day wear and clear in conversation. In 2026, our audiologists most often recommend the <strong>Phonak Infinio, Signia IX, ReSound Vivia, Widex Moment and Bernafon Encanta</strong> families for older adults.</p>\n\n<div class="takeaways"><h2>Key takeaways</h2><ul>\n<li>Prioritise <strong>easy handling</strong> (rechargeable, no fiddly batteries), <strong>clear speech in noise</strong> and dependable aftercare.</li>\n<li>RIC and BTE styles suit most seniors; tiny invisible styles can be hard for limited dexterity or eyesight.</li>\n<li>A quality pair typically costs about <strong>₹50,000 to ₹3,00,000+</strong>, with EMI and free trials widely available.</li>\n<li>A professional hearing test and a real-life trial matter more than any single “best” model.</li>\n</ul></div>\n\n<h2 id="what-makes-a-hearing-aid-good-for-seniors">What makes a hearing aid good for seniors?</h2>\n<p>Older adults usually value reliability and simplicity over gadgetry. The features that matter most are:</p>\n<ul>\n<li><strong>Easy handling:</strong> rechargeable batteries remove the need to change tiny cells; larger or app-based controls help.</li>\n<li><strong>Speech clarity in noise:</strong> modern AI sound processing lifts voices above background noise in restaurants and family gatherings.</li>\n<li><strong>Comfort for all-day wear:</strong> lightweight, well-fitted devices that don’t block the ear.</li>\n<li><strong>Connectivity:</strong> direct streaming of phone calls and TV, which is a real quality-of-life boost.</li>\n<li><strong>Local support & aftercare:</strong> easy servicing, cleaning and follow-up tuning close to home.</li>\n</ul>\n\n<h2 id="our-top-picks-for-seniors-in-2026">Our top picks for seniors in 2026</h2>\n<table><thead><tr><th>Model family</th><th>Best for</th><th>Why seniors like it</th></tr></thead><tbody>\n<tr><td>Phonak Infinio 50/70</td><td>Conversations in noise</td><td>Advanced speech processing, robust and rechargeable</td></tr>\n<tr><td>Signia IX (3IX / 5IX)</td><td>Natural own-voice</td><td>Conversation enhancement, slim and comfortable</td></tr>\n<tr><td>ReSound Vivia 5</td><td>All-day comfort</td><td>Lightweight with deep-learning noise reduction</td></tr>\n<tr><td>Widex Moment 220</td><td>Natural sound</td><td>Very natural, smooth sound quality</td></tr>\n<tr><td>Bernafon Encanta 200</td><td>Value clarity</td><td>Clear performance at a sensible price</td></tr>\n</tbody></table>\n<p>The “right” model depends on your hearing test results, lifestyle and budget — which is why we always recommend a trial before buying.</p>\n\n<h2 id="which-style-is-best-for-older-adults">Which style is best for older adults?</h2>\n<p>For most seniors we suggest <a href="/ric.html">Receiver-in-Canal (RIC)</a> or <a href="/bte.html">Behind-the-Ear (BTE)</a> styles. They are easier to insert and clean, support rechargeable batteries and offer the most power. Very small <a href="/invisible.html">invisible (IIC/CIC)</a> aids look discreet but can be fiddly for those with arthritis or reduced eyesight. If batteries are a worry, choose a <a href="/rechargeable.html">rechargeable</a> model.</p>\n\n<h2 id="how-much-do-they-cost-in-india">How much do they cost in India?</h2>\n<p>Senior-friendly hearing aids generally range from about <strong>₹50,000 for a good mid-range pair to ₹3,00,000+ for premium technology</strong>. Rechargeable and Bluetooth features sit at the higher end. EMI and free trials are widely available, so you can experience the benefit before committing. See our <a href="/blog/prices/">Prices & Cost guides</a> for detailed figures. <em>(Prices are indicative and vary by model and offer.)</em></p>\n\n<h2 id="how-to-choose-the-right-one">How to choose the right one</h2>\n<p>Start with a professional hearing test, then trial the recommended device in your own daily life — at home, on the phone and in noisy places. Judge each aid on comfort, clarity and ease of use, and choose a clinic that offers strong aftercare and follow-up tuning. The best hearing aid is the one that fits <em>your</em> ears, hearing and routine — not simply the most expensive.</p>\n\n    <div class="cta-inline"><h3>Not sure which hearing aid is right for you?</h3><p>Get a free hearing test and an honest, expert recommendation — with a no-obligation trial.</p><a href="/book-a-test.html" class="btn btn--white btn--lg">Book Your Free Test</a></div>',
            'author_name' => 'Wasiq Ali Khan',
            'meta_title' => 'Best Hearing Aids for Senior Citizens (2026 Edition) | Fairfield Hearing Clinics',
            'meta_description' => 'Best hearing aids for senior citizens in India (2026): our audiologists’ top rechargeable picks for easy handling, clear speech and all-day comfort — with prices, styles and how to choose.',
            'created_at' => '2026-03-18 10:00:00',
            'updated_at' => '2026-03-18 10:00:00',
        ]);
        DB::table('faqs')->insert([
            [
                'blog_post_id' => $postId,
                'question' => 'What is the best type of hearing aid for the elderly?',
                'answer' => 'For most seniors, a rechargeable Receiver-in-Canal (RIC) or Behind-the-Ear (BTE) hearing aid is best. These are easy to insert, handle and clean, support all-day rechargeable batteries, and offer clear speech in noise.',
                'type' => 'blog_post',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        $postId = DB::table('blog_posts')->insertGetId([
            'blog_category_id' => $categoryIds['buying-guides'],
            'title' => 'Best Hearing Aids for Severe to Profound Hearing Loss (2026 Edition)',
            'slug' => 'best-hearing-aids-for-severe-to-profound-loss',
            'summary' => 'For severe to profound hearing loss, the best hearing aids are high-power “superpower” or “ultra-power” Behind-the-Ear (BTE) models with strong amplification, feedback control and AI noise management. In 2026 the standout performers are the Phonak Naída, Signia Motion SP IX, ReSound Enzo, Widex Moment SP and Bernafon Leox power families.',
            'content' => '<p>For severe to profound hearing loss, the best hearing aids are <strong>high-power “superpower” or “ultra-power” Behind-the-Ear (BTE) models</strong> with strong amplification, feedback control and AI noise management. In 2026 the standout performers are the <strong>Phonak Naída, Signia Motion SP IX, ReSound Enzo, Widex Moment SP and Bernafon Leox</strong> power families.</p>\n\n<div class="takeaways"><h2>Key takeaways</h2><ul>\n<li>Severe-to-profound loss needs <strong>power BTE</strong> hearing aids — small invisible styles cannot deliver enough output.</li>\n<li>Look for high gain/output, strong <strong>feedback cancellation</strong>, and a custom earmould for a secure, leak-free fit.</li>\n<li>Top families: Phonak Naída, Signia Motion SP IX, ReSound Enzo, Widex Moment SP, Bernafon Leox.</li>\n<li>Realistic expectations and expert programming matter as much as the device itself.</li>\n</ul></div>\n\n<h2 id="what-counts-as-severe-to-profound-loss">What counts as severe to profound loss?</h2>\n<p>Hearing loss is graded by the quietest sounds you can hear, measured in decibels (dB HL). <strong>Severe</strong> loss is roughly 71–90 dB HL and <strong>profound</strong> loss is 91 dB HL or greater. At these levels you may miss most conversation without aids and rely heavily on volume and visual cues. A <a href="/book-a-test.html">professional hearing test</a> confirms your exact levels and the power you need.</p>\n\n<h2 id="best-power-hearing-aids-for-2026">Best power hearing aids for 2026</h2>\n<table><thead><tr><th>Model family</th><th>Type</th><th>Strength</th></tr></thead><tbody>\n<tr><td>Phonak Naída (Lumity/Infinio)</td><td>Superpower / Ultra-power BTE</td><td>Market-leading power and speech-in-noise</td></tr>\n<tr><td>Signia Motion SP IX</td><td>Superpower BTE</td><td>Strong output with conversation enhancement</td></tr>\n<tr><td>ReSound Enzo IA</td><td>Superpower BTE</td><td>Rich, comfortable sound with great connectivity</td></tr>\n<tr><td>Widex Moment SP</td><td>Superpower BTE</td><td>Natural sound quality at high power</td></tr>\n<tr><td>Bernafon Leox SP/UP</td><td>Super / Ultra-power BTE</td><td>Dependable power and value</td></tr>\n</tbody></table>\n\n<h2 id="why-bte-and-power-receivers">Why BTE and power receivers?</h2>\n<p>Power <a href="/bte.html">Behind-the-Ear (BTE)</a> aids house a larger amplifier and battery, so they can deliver the high gain that severe-to-profound loss requires while controlling feedback (whistling). They are usually paired with a <strong>custom earmould</strong> that seals the ear canal — essential for both power and comfort. Tiny invisible aids simply cannot produce enough output for these losses.</p>\n\n<h2 id="what-results-to-expect">What results to expect</h2>\n<p>Well-fitted power aids can restore a great deal of speech understanding, but expectations should be realistic: outcomes depend on how long the loss has been present and on the health of the hearing nerve. Expert, evidence-based programming — and follow-up fine-tuning — is what turns raw power into real-world clarity. In some profound cases, a <strong>cochlear implant</strong> may be discussed; our team can advise and refer where appropriate.</p>\n\n<h2 id="cost-and-next-steps">Cost and next steps</h2>\n<p>Power hearing aids span a wide range depending on technology level and brand. Book a <a href="/book-a-test.html">free hearing test</a> to confirm your levels, then trial a recommended power model with a proper earmould. For pricing and EMI, see our <a href="/blog/prices/">Prices & Cost guides</a>. <em>(Prices are indicative and vary by model.)</em></p>\n\n    <div class="cta-inline"><h3>Not sure which hearing aid is right for you?</h3><p>Get a free hearing test and an honest, expert recommendation — with a no-obligation trial.</p><a href="/book-a-test.html" class="btn btn--white btn--lg">Book Your Free Test</a></div>',
            'author_name' => 'Wasiq Ali Khan',
            'meta_title' => 'Best Hearing Aids for Severe to Profound Hearing Loss (2026 Edition) | Fairfield Hearing Clinics',
            'meta_description' => 'Best hearing aids for severe to profound hearing loss (2026): top high-power (superpower / ultra-power) BTE models from Phonak, Signia, ReSound, Widex and Bernafon, with prices and advice.',
            'created_at' => '2026-04-02 10:00:00',
            'updated_at' => '2026-04-02 10:00:00',
        ]);
        DB::table('faqs')->insert([
            [
                'blog_post_id' => $postId,
                'question' => 'What are the best hearing aids for profound hearing loss?',
                'answer' => 'The best options are high-power (superpower or ultra-power) Behind-the-Ear models such as the Phonak Naída, Signia Motion SP IX, ReSound Enzo, Widex Moment SP and Bernafon Leox, fitted with a custom earmould.',
                'type' => 'blog_post',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        $postId = DB::table('blog_posts')->insertGetId([
            'blog_category_id' => $categoryIds['comparisons'],
            'title' => 'Signia Styletto IX: 7IX vs 5IX vs 3IX',
            'slug' => 'signia-styletto-ix-7ix-vs-5ix-vs-3ix',
            'summary' => 'The Signia Styletto IX is a slim, rechargeable hearing aid that comes in three technology tiers: 7IX (premium), 5IX (advanced) and 3IX (essential). They look identical and share the same signature sound — the difference is how much automatic processing and how many features you get. In short: 7IX suits busy, demanding listeners; 5IX is the balanced all-rounder; 3IX covers everyday quieter lifestyles.',
            'content' => '<p>The <strong>Signia Styletto IX</strong> is a slim, rechargeable hearing aid that comes in three technology tiers: <strong>7IX (premium), 5IX (advanced) and 3IX (essential)</strong>. They look identical and share the same signature sound — the difference is how much automatic processing and how many features you get. In short: <strong>7IX</strong> suits busy, demanding listeners; <strong>5IX</strong> is the balanced all-rounder; <strong>3IX</strong> covers everyday quieter lifestyles.</p>\n\n<div class="takeaways"><h2>Key takeaways</h2><ul>\n<li>All three share the same <strong>slim design, rechargeable battery and Bluetooth</strong> — only the technology tier changes.</li>\n<li><strong>Higher tiers</strong> add more automatic programs and stronger speech-in-noise performance for complex environments.</li>\n<li>Choose by <strong>lifestyle and noise exposure</strong>, not looks — your audiologist can match the tier to your routine.</li>\n<li>A trial is the best way to feel the difference between tiers in your own life.</li>\n</ul></div>\n\n<h2 id="what-the-styletto-ix-series-is">What the Styletto IX series is</h2>\n<p>The Styletto IX stands out for its slim, pen-like design, portable charging case and Signia’s “Integrated Xperience” platform with RealTime Conversation Enhancement. It is a <a href="/ric.html">Receiver-in-Canal</a> style aimed at people who want a discreet, modern look with strong speech clarity.</p>\n\n<h2 id="7ix-vs-5ix-vs-3ix-at-a-glance">7IX vs 5IX vs 3IX at a glance</h2>\n<table><thead><tr><th></th><th>Styletto 7IX</th><th>Styletto 5IX</th><th>Styletto 3IX</th></tr></thead><tbody>\n<tr><td>Tier</td><td>Premium</td><td>Advanced</td><td>Essential</td></tr>\n<tr><td>Automatic programs</td><td>Most</td><td>More</td><td>Core</td></tr>\n<tr><td>Speech in noise</td><td>Excellent</td><td>Very good</td><td>Good</td></tr>\n<tr><td>Best for</td><td>Busy, noisy lifestyles</td><td>Mixed daily life</td><td>Quieter, routine settings</td></tr>\n<tr><td>Design / rechargeable / Bluetooth</td><td>Same</td><td>Same</td><td>Same</td></tr>\n</tbody></table>\n\n<h2 id="how-the-tiers-differ">How the tiers differ</h2>\n<p>Think of the tiers as how “smart” and automatic the hearing aid is. The <strong>7IX</strong> reacts fastest and most precisely as your surroundings change — ideal if you’re often in restaurants, meetings or crowds. The <strong>5IX</strong> handles most everyday situations very well and is the popular middle choice. The <strong>3IX</strong> delivers clear, comfortable hearing for calmer, more predictable environments at a friendlier price.</p>\n\n<h2 id="which-one-should-you-choose">Which one should you choose?</h2>\n<ul>\n<li><strong>Choose 7IX</strong> if you have an active social or working life with lots of background noise.</li>\n<li><strong>Choose 5IX</strong> if you want excellent everyday performance and value — the best all-round pick for most people.</li>\n<li><strong>Choose 3IX</strong> if your days are mostly quiet (home, one-to-one chats, small groups) and budget is a priority.</li>\n</ul>\n\n<h2 id="price-and-trial">Price and trial</h2>\n<p>Within a series, higher tiers cost more because of the added processing. The best way to decide is to <a href="/book-a-test.html">book a free hearing test</a> and trial the recommended tier in your own daily life. For broader pricing, see our <a href="/blog/prices/">Prices & Cost guides</a>. <em>(Prices are indicative and vary by offer.)</em></p>\n\n    <div class="cta-inline"><h3>Not sure which hearing aid is right for you?</h3><p>Get a free hearing test and an honest, expert recommendation — with a no-obligation trial.</p><a href="/book-a-test.html" class="btn btn--white btn--lg">Book Your Free Test</a></div>',
            'author_name' => 'Wasiq Ali Khan',
            'meta_title' => 'Signia Styletto IX: 7IX vs 5IX vs 3IX — Which Is Best for You? | Fairfield Hearing Clinics',
            'meta_description' => 'Signia Styletto IX 7IX vs 5IX vs 3IX compared: the slim, rechargeable design is the same — the difference is the technology tier. See features, who each suits, and indicative prices.',
            'created_at' => '2026-04-20 10:00:00',
            'updated_at' => '2026-04-20 10:00:00',
        ]);
        DB::table('faqs')->insert([
            [
                'blog_post_id' => $postId,
                'question' => 'What is the difference between Signia Styletto 7IX, 5IX and 3IX?',
                'answer' => 'All three share the same slim, rechargeable design and Bluetooth. The difference is the technology tier: 7IX is premium with the most automatic processing for noisy environments, 5IX is the balanced all-rounder, and 3IX is the essential tier for quieter, routine settings.',
                'type' => 'blog_post',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blog_post_id' => $postId,
                'question' => 'Is the Styletto 7IX worth the extra money?',
                'answer' => 'If you spend a lot of time in noisy or demanding situations — restaurants, meetings, crowds — the 7IX’s faster, more precise processing is worth it. For mostly everyday use, the 5IX usually offers the best value.',
                'type' => 'blog_post',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blog_post_id' => $postId,
                'question' => 'Are all Styletto IX models rechargeable and Bluetooth-enabled?',
                'answer' => 'Yes. The slim design, rechargeable battery, portable charging case and Bluetooth streaming are the same across 7IX, 5IX and 3IX — only the processing tier changes.',
                'type' => 'blog_post',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blog_post_id' => $postId,
                'question' => 'Which Signia Styletto IX is best for me?',
                'answer' => 'Choose by lifestyle: 7IX for busy, noisy lives; 5IX for balanced everyday use and value; 3IX for quieter routines and tighter budgets. A trial with an audiologist is the best way to decide.',
                'type' => 'blog_post',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 5. Insert General FAQs
        $generalFaqs = [
            [
                'question' => 'What is the average hearing aid price in India?',
                'answer' => 'The average hearing aid price in India starts from ₹14,999 for basic digital hearing aids and goes up to ₹3,00,000+ for advanced invisible or AI-powered models. The final cost depends on the brand, features, technology level, and type of hearing aid you choose.',
                'type' => 'general',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Why do hearing aid prices vary so much?',
                'answer' => 'Hearing aid prices in India vary based on factors like technology level, sound processing quality, Bluetooth connectivity, rechargeability, invisibility, and brand reputation. Basic models offer essential amplification, while premium hearing aids provide advanced speech clarity, automatic noise adaptation, smartphone control, and tinnitus masking.',
                'type' => 'general',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What is the price of invisible hearing aids in India?',
                'answer' => 'Invisible hearing aid prices in India typically range between ₹49,999 and ₹2,50,000. These are custom-made to fit deep inside the ear canal, making them nearly invisible while delivering natural sound quality.',
                'type' => 'general',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What is the cost of rechargeable hearing aids in India?',
                'answer' => 'Rechargeable hearing aids in India start from ₹29,000 and can go up to ₹2,80,000 depending on the model. They eliminate the need for regular battery changes and are popular for their convenience and eco-friendliness.',
                'type' => 'general',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Which is the best hearing aid under ₹30,000?',
                'answer' => 'In the ₹15,000–₹30,000 range, reliable Behind-The-Ear (BTE) digital hearing aids from brands like Signia, Phonak, and Widex offer good sound quality, noise reduction, and basic rechargeability. These are ideal for mild to moderate hearing loss.',
                'type' => 'general',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Do hearing aid prices include consultation and fitting?',
                'answer' => 'Most reputed clinics and providers include a free hearing test, consultation with an audiologist, and basic fitting in the hearing aid price. Some advanced features like real-ear measurement or fine-tuning may cost extra depending on the clinic.',
                'type' => 'general',
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Can I buy hearing aids on EMI or with insurance?',
                'answer' => "Yes. Many authorized hearing aid providers in India offer flexible EMI options starting as low as ₹2,00,000/month. Some insurance policies may cover partial or full costs depending on the plan. It's best to check with your insurer and clinic.",
                'type' => 'general',
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Are branded hearing aids more expensive?',
                'answer' => 'Yes. Top brands like Signia, Phonak, Widex, and Oticon typically cost more due to their advanced research, reliable performance, and after-sales service. However, they offer better clarity, longer lifespan, and superior comfort compared to low-cost local models.',
                'type' => 'general',
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Do hearing aid prices vary between BTE, RIC, and CIC types?',
                'answer' => 'Yes. Behind-the-Ear (BTE) hearing aids are usually the most affordable, Receiver-in-Canal (RIC) are mid-range with more discreet designs, and Completely-in-Canal (CIC) or invisible models are the most expensive due to customization and miniaturization.',
                'type' => 'general',
                'sort_order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Where can I find the best price for hearing aids in India?',
                'answer' => 'You can compare hearing aid prices online and through authorized clinics. At Hearing Solutions (Fairfield Hearing), we offer up to 50% OFF on genuine digital hearing aids with price-match guarantees, free consultations, and nationwide service support.',
                'type' => 'general',
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('faqs')->insert($generalFaqs);

        // 6. Insert Team Members
        $teamMembers = [
            [
                'name' => 'Dr. Nayeem Ahmad Siddiqui',
                'slug' => 'dr-nayeem-ahmad-siddiqui',
                'role' => "Senior ENT Surgeon &middot; MBBS, DLO, DNB (ENT) &middot; 20+ years' experience",
                'category' => 'ent_specialist',
                'eyebrow' => 'Our ENT Specialist',
                'photo' => 'assets/img/dr-nayeem.jpg',
                'short_bio' => 'With over 20 years’ experience, Dr. Nayeem specialises in ear and hearing disorders in adults and children — with a special interest in chronic ear disease. He now continues as a Senior ENT Surgeon at Diyos Hospital, New Delhi.',
                'at_a_glance' => '["Qualifications: MBBS, DLO, DNB (ENT)", "Experience: 20+ years", "Special interest: Chronic ear disease", "Treats: Adults & children", "Currently: Senior ENT Surgeon, Diyos Hospital, New Delhi"]',
                'areas_of_expertise' => '["Ear & hearing disorders", "Chronic ear disease", "Ear surgery (adults & children)", "Full range of nose & throat conditions"]',
                'blockquote' => '"My profession is not only a label to wear but also a spark which enlightens me whenever I try to help mankind. The contentment of witnessing the patients get relief and lead to a healthy life is utterly incomparable. And I am overjoyed to be a part of this fraternity."',
                'bio' => '<p>Dr. Nayeem Ahmad Siddiqui completed his <strong>MBBS</strong> from Magadh University, Bodh Gaya, Bihar, in 2001, followed by a one-year practising internship. In 2003 he completed a <strong>Diploma in Otorhinolaryngology (DLO)</strong> at Patna Medical College & Hospital, and in the same year earned the <strong>Diplomate of National Board (DNB)</strong> — a medical qualification awarded by the National Board of Examinations (NBE).</p>\n<p>He began his career as a Senior Resident Doctor at VIMHANS Super Specialty Hospital, Delhi. His excellence in diagnosis led to his appointment as a Junior Consultant for ENT surgeries in 2010. From 2011 he served as <strong>Head of the Department (HOD) of ENT at Metro Hospital, Faridabad</strong>, for five years, and subsequently as a <strong>Senior Consultant at Apollo Spectra Hospitals</strong> for the next five years.</p>\n<p>Dr. Nayeem undertakes the full range of ear, nose and throat conditions, but holds a special interest in <strong>ear and hearing problems in adults and children &mdash; particularly chronic ear disease</strong>.</p>\n<p>From being a senior surgical consultant to patients and hospitals, Dr. Nayeem has always participated actively in conferences and professional meets. Learning and practising newly developed, modern tools to examine and treat disease remains his pure and promising goal. He continues to be a Senior ENT Surgeon at Diyos Hospital, New Delhi.</p>',
                'timeline' => '[{"year": "2001", "text": "MBBS, Magadh University, Bodh Gaya, Bihar; followed by a one-year internship."}, {"year": "2003", "text": "Diploma in Otorhinolaryngology (DLO), Patna Medical College & Hospital, and DNB (National Board of Examinations)."}, {"year": "Early career", "text": "Senior Resident Doctor, VIMHANS Super Specialty Hospital, Delhi."}, {"year": "2010", "text": "Junior Consultant for ENT surgeries."}, {"year": "2011\\u20132016", "text": "Head of Department (HOD), ENT, Metro Hospital, Faridabad."}, {"year": "2016\\u20132021", "text": "Senior Consultant, Apollo Spectra Hospitals."}, {"year": "Present", "text": "Senior ENT Surgeon, Diyos Hospital, New Delhi."}]',
                'meta_title' => 'Dr. Nayeem Ahmad Siddiqui — Senior ENT Surgeon | Fairfield Hearing Clinics',
                'meta_description' => "Dr. Nayeem Ahmad Siddiqui (MBBS, DLO, DNB) is a Senior ENT Surgeon with 20+ years' experience in ear and hearing disorders in adults and children, with a special interest in chronic ear disease.",
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Farhan Khan',
                'slug' => 'farhan-khan',
                'role' => 'Senior Product Specialist cum Technician',
                'category' => 'product_specialist',
                'eyebrow' => 'Product Specialists & Technicians',
                'photo' => 'assets/img/farhan-khan.jpg',
                'short_bio' => 'A Senior Product Specialist at Fairfield Hearing Clinics with hands-on expertise across leading brands — Phonak, Signia, Unitron, Bernafon, ReSound, Widex and Danavox — guiding patients to the right device and keeping it working perfectly.',
                'at_a_glance' => '["Role: Senior Product Specialist", "Experience: 2+ years at Fairfield", "Brands: Phonak, Signia, Unitron, Bernafon, ReSound, Widex", "Focus: Fitting & maintenance"]',
                'areas_of_expertise' => '["Multi-brand product expertise", "Hearing-aid fitting & demonstration", "Servicing & technical maintenance", "Patient guidance & support"]',
                'blockquote' => '',
                'bio' => '<p>A Senior Product Specialist at Fairfield Hearing Clinics with hands-on expertise across leading brands — Phonak, Signia, Unitron, Bernafon, ReSound, Widex and Danavox — guiding patients to the right device and keeping it working perfectly.</p>\n<p>Farhan Khan is a Senior Product Specialist cum Technician at Fairfield Hearing Clinics, where he has been a valued member of the team for over two years. He brings strong, hands-on expertise across a wide range of leading hearing-aid brands — including Phonak, Signia, Unitron, Bernafon, ReSound, Widex and Danavox.</p>\n<p>He helps patients understand and choose the right device for their needs, and provides expert fitting, demonstration, servicing and technical maintenance — ensuring every patient enjoys reliable, comfortable and long-lasting performance from their hearing aids.</p>',
                'timeline' => '[]',
                'meta_title' => 'Farhan Khan — Senior Product Specialist cum Technician | Fairfield Hearing Clinics',
                'meta_description' => 'Farhan Khan, Senior Product Specialist cum Technician at Fairfield Hearing Clinics — expert across Phonak, Signia, Unitron, Bernafon, ReSound, Widex and Danavox hearing aids.',
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Md. Umar Khan',
                'slug' => 'md-umar-khan',
                'role' => 'Product Specialist cum Technician',
                'category' => 'product_specialist',
                'eyebrow' => 'Product Specialists & Technicians',
                'photo' => 'assets/img/md-umar-khan.jpg',
                'short_bio' => 'Product Expert cum Technician at Fairfield Hearing Clinics since 2023, with strong technical knowledge of all major brands — Signia, Phonak, Widex, Bernafon and Unitron — and a talent for understanding patient needs and fitting devices with precision.',
                'at_a_glance' => '["Role: Product Specialist cum Technician", "Joined: 2023", "Brands: Signia, Phonak, Widex, Bernafon, Unitron", "Focus: Patient fitting"]',
                'areas_of_expertise' => '["Multi-brand technical knowledge", "Precision hearing-aid fitting", "Patient needs assessment", "Servicing & support"]',
                'blockquote' => '',
                'bio' => '<p>Product Expert cum Technician at Fairfield Hearing Clinics since 2023, with strong technical knowledge of all major brands — Signia, Phonak, Widex, Bernafon and Unitron — and a talent for understanding patient needs and fitting devices with precision.</p>\n<p>Md. Umar Khan joined Fairfield Hearing Clinics as a Product Expert cum Technician in 2023, after completing his degree. He has built strong technical knowledge of almost all major hearing-aid brands, including Signia, Phonak, Widex, Bernafon and Unitron.</p>\n<p>He excels at handling patients with care — understanding their requirements and needs, and fitting hearing aids with precision and patience to deliver comfortable, effective results.</p>',
                'timeline' => '[]',
                'meta_title' => 'Md. Umar Khan — Product Specialist cum Technician | Fairfield Hearing Clinics',
                'meta_description' => 'Md. Umar Khan, Product Specialist cum Technician at Fairfield Hearing Clinics since 2023 — strong technical knowledge of Signia, Phonak, Widex, Bernafon and Unitron hearing aids.',
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sahil Khan',
                'slug' => 'sahil-khan',
                'role' => 'Audiologist & Speech-Language Pathologist',
                'category' => 'audiologist',
                'eyebrow' => 'Our Audiologists',
                'photo' => 'assets/img/sahil-khan.jpg',
                'short_bio' => 'A compassionate Audiologist & Speech-Language Pathologist skilled in assessing and treating speech, language, voice, fluency and hearing disorders in children and adults — including ASD, ADHD, aphasia and apraxia.',
                'at_a_glance' => '["Role: Audiologist & SLP", "Education: BASLP", "Treats: Children & adults", "Focus: Speech, language & hearing"]',
                'areas_of_expertise' => '["Hearing assessment (PTA, Impedance, OAE, ABR/BERA, VNG)", "Speech, language, voice & fluency therapy", "Paediatric disorders (ASD, ADHD)", "Hearing-aid fitting & counselling", "Parent & caregiver training"]',
                'blockquote' => '',
                'bio' => '<p>A compassionate Audiologist & Speech-Language Pathologist skilled in assessing and treating speech, language, voice, fluency and hearing disorders in children and adults — including ASD, ADHD, aphasia and apraxia.</p>\n<p>Sahil Khan is a compassionate and dedicated Audiologist and Speech-Language Pathologist with strong clinical knowledge in the assessment, diagnosis and intervention of speech, language, voice, fluency and hearing disorders across paediatric and adult populations.</p>\n<p>He is skilled in evidence-based therapy, counselling and multidisciplinary teamwork. His clinical work includes hearing assessments such as Pure Tone Audiometry (PTA), Impedance, OAE, ABR/BERA and VNG, and the diagnosis and management of speech and language disorders including ASD, ADHD, aphasia, apraxia and dysarthria.</p>\n<p>He provides speech, language, voice and fluency therapy, hearing-aid selection, fitting and counselling, and structured parent and caregiver counselling with home-training programmes.</p>\n<p>Sahil holds a Bachelor’s in Audiology and Speech-Language Pathology (BASLP) from Sri Guru Ram Das University of Health Sciences and Research, Amritsar.</p>',
                'timeline' => '[{"year": "Education", "text": "BASLP, Sri Guru Ram Das University of Health Sciences & Research, Amritsar"}, {"year": "Present", "text": "Audiologist & Speech-Language Pathologist, Fairfield Hearing Clinic"}]',
                'meta_title' => 'Sahil Khan — Audiologist & Speech-Language Pathologist | Fairfield Hearing Clinics',
                'meta_description' => 'Sahil Khan, Audiologist & Speech-Language Pathologist at Fairfield Hearing Clinics — BASLP qualified, expert in hearing assessment, speech & language therapy for children and adults.',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Shahabuddin Khan',
                'slug' => 'shahabuddin-khan',
                'role' => 'Director · Hearing Care Professional',
                'category' => 'director',
                'eyebrow' => 'Director',
                'photo' => 'assets/img/shahabuddin-khan.jpg',
                'short_bio' => 'Director of Fairfield Hearing Clinics with 21+ years across the world’s leading hearing-aid brands. A former Head of North India Business at Phonak and an engineer by training, he leads with deep technical expertise and a patient-first vision.',
                'at_a_glance' => '["Role: Director", "Experience: 21+ years", "Expertise: Hearing aids & cochlear implants", "Focus: Patient-first care"]',
                'areas_of_expertise' => '["Hearing-aid sales & business development", "Audiological support & troubleshooting", "Complex case programming", "VIP patient management", "Training & team development"]',
                'blockquote' => '',
                'bio' => '<p>Director of Fairfield Hearing Clinics with 21+ years across the world’s leading hearing-aid brands. A former Head of North India Business at Phonak and an engineer by training, he leads with deep technical expertise and a patient-first vision.</p>\n<p>Shahabuddin Khan is the Director of Fairfield Hearing Clinics — a dynamic, patient-centric hearing-care professional with over 21 years of experience across the world’s leading global hearing-aid brands. An engineer by training, he combines a strong technical foundation with deep, on-ground clinical and commercial experience across North and West India.</p>\n<p>He is known for sales leadership, audiological support, VIP patient handling and complex case troubleshooting — building trust with patients, driving regional business growth and elevating clinical outcomes through precise programming and counselling.</p>\n<p>As Head of North India Business at Phonak (Sonova India Pvt. Ltd.), he led sales, channel development and customer support across Delhi-NCR, Haryana, Punjab, Himachal Pradesh, J&K, UP, Uttarakhand and Rajasthan. Over 11 years with Bernafon (Demant Group), he managed regional sales and support across North and West India, handling VIP and audiologically complex cases and training audiologists and partners. He began his career as a Sales Engineer at Alps International (2000–2003).</p>\n<p>He holds a B.E. in Electrical Engineering from Jamia Millia Islamia, New Delhi, and a Diploma in Engineering. At Fairfield, he brings two decades of industry knowledge to a clinic built on trust, technology and compassionate care.</p>',
                'timeline' => '[{"year": "2000\\u20132003", "text": "Sales Engineer, Alps International Pvt. Ltd."}, {"year": "11 years", "text": "Regional Sales & Customer Support (North & West India), Bernafon (Demant Group)"}, {"year": "3.5 years", "text": "Head \\u2013 North India Business, Phonak (Sonova India)"}, {"year": "2006", "text": "B.E. Electrical Engineering, Jamia Millia Islami"}, {"year": "Present", "text": "Director, Fairfield Hearing Clinic"}]',
                'meta_title' => 'Shahabuddin Khan — Director · Hearing Care Professional | Fairfield Hearing Clinics',
                'meta_description' => 'Shahabuddin Khan, Director of Fairfield Hearing Clinics — 21+ years across leading hearing-aid brands, former Head of North India Business at Phonak, engineer by training.',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Suraj Kumar',
                'slug' => 'suraj-kumar',
                'role' => 'Audiologist',
                'category' => 'audiologist',
                'eyebrow' => 'Our Audiologists',
                'photo' => 'assets/img/suraj-kumar.jpg',
                'short_bio' => 'An audiologist focused on hearing awareness, screening and rehabilitation — conducting hearing screening (OAE), evaluation (PTA & impedance) and hearing-aid fitting and programming, with a passion for community hearing-care camps.',
                'at_a_glance' => '["Role: Audiologist", "Skills: OAE, PTA, Impedance", "Focus: Screening & rehabilitation", "Education: DHLS (RCI)"]',
                'areas_of_expertise' => '["Hearing screening (OAE)", "Hearing evaluation (PTA & Impedance)", "Hearing-aid fitting & programming", "Aural rehabilitation", "Awareness camps & community screening"]',
                'blockquote' => '',
                'bio' => '<p>An audiologist focused on hearing awareness, screening and rehabilitation — conducting hearing screening (OAE), evaluation (PTA & impedance) and hearing-aid fitting and programming, with a passion for community hearing-care camps.</p>\n<p>Suraj Kumar is an audiologist dedicated to hearing awareness, screening and rehabilitation. He conducts hearing screening (OAE) and hearing evaluation (PTA and impedance), and carries out hearing-aid fitting and programming for aural rehabilitation.</p>\n<p>He gained clinical experience as an assistant audiologist at Shree Radhe Speech and Hearing Center, Noida, supporting assessments, fittings and patient care. He is also committed to community engagement through hearing-awareness drives and screening camps.</p>\n<p>Suraj holds a Diploma in Hearing, Language and Speech recognised by the Rehabilitation Council of India (through IGNOU), and a Bachelor of Arts from the University of Delhi (School of Open Learning).</p>',
                'timeline' => '[{"year": "Clinical experience", "text": "Assistant Audiologist, Shree Radhe Speech and Hearing Center, Noi"}, {"year": "Education", "text": "Diploma in Hearing, Language & Speech (RCI / IGNOU); B.A., University of Delhi (SOL)"}, {"year": "Present", "text": "Audiologist, Fairfield Hearing Clinic"}]',
                'meta_title' => 'Suraj Kumar — Audiologist | Fairfield Hearing Clinics',
                'meta_description' => 'Suraj Kumar, Audiologist at Fairfield Hearing Clinics — skilled in hearing screening (OAE), evaluation (PTA & impedance), hearing-aid fitting and aural rehabilitation.',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wasiq Ali Khan',
                'slug' => 'wasiq-ali-khan',
                'role' => 'Senior Audiologist & Clinical Lead',
                'category' => 'audiologist',
                'eyebrow' => 'Our Audiologists',
                'photo' => 'assets/img/wasiq-ali-khan.jpg',
                'short_bio' => 'A founding member of Fairfield Hearing Clinics with 10+ years in audiology. Wasiq has cared for 700+ patients — including VIP and complex cases — and is known for precise fittings, real-ear measurement and a calm, empathetic approach.',
                'at_a_glance' => '["Role: Senior Audiologist & Clinical Lead", "Experience: 10+ years", "Patients: 700+ managed", "Skills: REM, Audiometry, Tympanometry"]',
                'areas_of_expertise' => '["Hearing-aid fitting & programming", "Real Ear Measurement (REM)", "Complex & VIP case management", "Audiological assessment & counselling", "Clinic operations & patient flow"]',
                'blockquote' => '',
                'bio' => '<p>A founding member of Fairfield Hearing Clinics with 10+ years in audiology. Wasiq has cared for 700+ patients — including VIP and complex cases — and is known for precise fittings, real-ear measurement and a calm, empathetic approach.</p>\n<p>Wasiq Ali Khan is a Senior Audiologist and Clinical Lead at Fairfield Hearing Clinics, and a core member of its founding team. With over 10 years of experience in hearing-aid technology, patient counselling and clinic operations, he has shaped the clinic’s protocols, patient experience and technical standards from inception.</p>\n<p>He has managed more than 700 patients, including high-profile and audiologically challenging cases — among them VIP patients such as a former Vice-President of India. He delivers precise hearing-aid fittings, real-ear measurements and long-term rehabilitation counselling, and is known for a calm, empathetic approach and deep multi-brand technical expertise.</p>\n<p>Earlier, he served as Senior Product Manager and Clinic Manager at Sound Hearing Centre (2019–2023) and as a Product Specialist at Manas Speech & Hearing Clinic (2014–2019), building strong foundations in fitting science, troubleshooting and patient care.</p>\n<p>He holds a Diploma in Hearing, Language & Speech (DHLS) from AYJNISHD (D), Mumbai, and a B.Sc. He works hands-on with all major global brands including Phonak, Bernafon, Signia, Widex, ReSound, Danavox and Unitron.</p>',
                'timeline' => '[{"year": "2014\\u20132019", "text": "Product Specialist, Manas Speech & Hearing Clinic"}, {"year": "2019\\u20132023", "text": "Senior Product Manager & Clinic Manager, Sound Hearing Centre"}, {"year": "2023", "text": "Diploma in Hearing, Language & Speech (DHLS), AYJNISHD (D), Mumbai"}, {"year": "Since inception", "text": "Senior Audiologist & Clinical Lead, Fairfield Hearing Clinic"}]',
                'meta_title' => 'Wasiq Ali Khan — Senior Audiologist & Clinical Lead | Fairfield Hearing Clinics',
                'meta_description' => "Wasiq Ali Khan, Senior Audiologist & Clinical Lead at Fairfield Hearing Clinics — 10+ years' experience, 700+ patients, expert in hearing-aid fitting, REM and complex case management.",
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('team_members')->insert($teamMembers);

        // 7. Seed Policy Pages
        $policies = [
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => "# Privacy Policy\n\nLast updated: June 2026\n\nYour privacy is important to us. This policy describes how we collect, use, and process your personal and medical information.\n\n## 1. Information We Collect\n- **Personal Details:** Name, email, and telephone number.\n- **Audiology Data:** Pure-tone audiograms and clinical histories.\n\n## 2. RCI Compliance\nWe strictly adhere to audiology and clinical health record storage guidelines in India.",
                'meta_title' => 'Privacy Policy - Fairfield Hearing Clinics',
                'meta_description' => 'Understand how Fairfield Hearing Clinics collects, stores, and protects your personal and medical data.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => "# Terms of Service\n\nWelcome to Fairfield Hearing Clinics.\n\n## 1. Appointments & Cancellations\n- We request at least 24 hours notice for any cancellations.\n- COMPLIMENTARY hearing tests are provided under no-obligation trials.\n\n## 2. Fitting Trials\n- Hearing aids supplied on trial require a verification process or deposit where applicable.",
                'meta_title' => 'Terms of Service - Fairfield Hearing Clinics',
                'meta_description' => 'Review the terms and conditions governing appointments, trials, and audiology services.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('policy_pages')->insert($policies);

        // 7. Insert Users
        DB::table('users')->insert([
            [
                'name' => 'Super Admin',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'roles' => json_encode(['superadmin']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Blogger Staff',
                'email' => 'blogger@example.com',
                'password' => bcrypt('password'),
                'roles' => json_encode(['blog_posting']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Leads Staff',
                'email' => 'leads@example.com',
                'password' => bcrypt('password'),
                'roles' => json_encode(['leads_management']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Support Staff',
                'email' => 'support@example.com',
                'password' => bcrypt('password'),
                'roles' => json_encode(['support']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Content Staff',
                'email' => 'content@example.com',
                'password' => bcrypt('password'),
                'roles' => json_encode(['content_uploading']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager Staff',
                'email' => 'manager@example.com',
                'password' => bcrypt('password'),
                'roles' => json_encode(['manage_staff']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Get Clinic Location IDs
        $nfcClinicId = DB::table('locations')->where('name', 'Fairfield Hearing Clinic — Delhi NFC (Main Clinic)')->value('id');
        $hariNagarClinicId = DB::table('locations')->where('name', 'Fairfield Hearing Clinic — Delhi Hari Nagar')->value('id');

        // 8. Insert Form Submissions
        $sub1Id = DB::table('form_submissions')->insertGetId([
            'full_name' => 'John Doe',
            'mobile_number' => '+91 98114 18578',
            'email' => 'john.doe@example.com',
            'hearing_problem' => 'Tinnitus & High Frequency Loss',
            'location_id' => $nfcClinicId,
            'preferred_day_time' => 'Saturday morning',
            'message' => 'I have experienced persistent ringing in my left ear for the past 3 weeks after attending a music concert.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sub2Id = DB::table('form_submissions')->insertGetId([
            'full_name' => 'Jane Miller',
            'mobile_number' => '+91 98114 18579',
            'email' => 'jane.miller@example.com',
            'hearing_problem' => 'Difficulty hearing in background noise',
            'location_id' => $hariNagarClinicId,
            'preferred_day_time' => 'Wednesday afternoon',
            'message' => 'I struggle to hear my family when sitting at dinner or in restaurants. I would like a general assessment.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 9. Insert Leads
        DB::table('leads')->insert([
            [
                'full_name' => 'Michael Scott',
                'mobile_number' => '+91 99999 88888',
                'email' => 'michael@dundermifflin.com',
                'hearing_problem' => 'Mild age-related hearing loss',
                'location_id' => $nfcClinicId,
                'preferred_day_time' => 'Thursday Morning',
                'status' => 'new',
                'logs' => json_encode([
                    ['date' => now()->subDays(3)->toDateTimeString(), 'author' => 'System', 'message' => 'Lead generated from frontend appointment form.']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Dwight Schrute',
                'mobile_number' => '+91 99999 77777',
                'email' => 'dwight@schrutebeetfarms.com',
                'hearing_problem' => 'Tinnitus in left ear',
                'location_id' => $hariNagarClinicId,
                'preferred_day_time' => null,
                'status' => 'contacted',
                'logs' => json_encode([
                    ['date' => now()->subDays(5)->toDateTimeString(), 'author' => 'System', 'message' => 'Lead created.'],
                    ['date' => now()->subDays(4)->toDateTimeString(), 'author' => 'Leads Staff', 'message' => 'Called client. Left a voicemail.']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Jim Halpert',
                'mobile_number' => '+91 99999 66666',
                'email' => 'jim@dundermifflin.com',
                'hearing_problem' => 'Difficulty hearing conversation in noise',
                'location_id' => $nfcClinicId,
                'preferred_day_time' => null,
                'status' => 'in_progress',
                'logs' => json_encode([
                    ['date' => now()->subDays(10)->toDateTimeString(), 'author' => 'System', 'message' => 'Lead created.'],
                    ['date' => now()->subDays(8)->toDateTimeString(), 'author' => 'Leads Staff', 'message' => 'Audiology consultation scheduled.'],
                    ['date' => now()->subDays(2)->toDateTimeString(), 'author' => 'Dr. Sarah Jenkins', 'message' => 'Audiogram completed. Client testing trial hearing aids.']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Pam Beesly',
                'mobile_number' => '+91 99999 55555',
                'email' => 'pam@dundermifflin.com',
                'hearing_problem' => 'General review',
                'location_id' => $nfcClinicId,
                'preferred_day_time' => null,
                'status' => 'won',
                'logs' => json_encode([
                    ['date' => now()->subDays(15)->toDateTimeString(), 'author' => 'System', 'message' => 'Lead created.'],
                    ['date' => now()->subDays(5)->toDateTimeString(), 'author' => 'Leads Staff', 'message' => 'Consultation completed. Recommended Signia Active Pro.'],
                    ['date' => now()->subDays(1)->toDateTimeString(), 'author' => 'Leads Staff', 'message' => 'Purchased Signia Active Pro. Lead won.']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name' => 'Angela Martin',
                'mobile_number' => '+91 99999 44444',
                'email' => 'angela@dundermifflin.com',
                'hearing_problem' => 'Invisible hearing aid inquiry',
                'location_id' => $hariNagarClinicId,
                'preferred_day_time' => null,
                'status' => 'lost',
                'logs' => json_encode([
                    ['date' => now()->subDays(12)->toDateTimeString(), 'author' => 'System', 'message' => 'Lead created.'],
                    ['date' => now()->subDays(2)->toDateTimeString(), 'author' => 'Leads Staff', 'message' => 'Client decided not to proceed due to budget constraints.']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep down operations simple or truncate
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        DB::table('form_submissions')->truncate();
        DB::table('leads')->truncate();
        DB::table('policy_pages')->truncate();
        DB::table('locations')->truncate();
        DB::table('blog_categories')->truncate();
        DB::table('blog_posts')->truncate();
        DB::table('faqs')->truncate();
        DB::table('team_members')->truncate();
        Schema::enableForeignKeyConstraints();
    }
};
