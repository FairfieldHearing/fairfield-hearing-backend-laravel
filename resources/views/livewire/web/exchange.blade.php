<div>
    <style>
        /* ---------- Exchange page (scoped) ---------- */
        .xc-steps { display: grid; gap: clamp(20px, 3vw, 32px); }
        .xc-card { background: var(--white); border: 1px solid var(--line); border-radius: var(--radius); box-shadow: var(--shadow-sm); padding: clamp(20px, 3vw, 32px); }
        .xc-card h3 { display: flex; align-items: center; gap: .6em; margin-top: 0; }
        .xc-stepnum { flex-shrink: 0; width: 34px; height: 34px; border-radius: 50%; background: var(--lime); color: var(--ink); display: grid; place-items: center; font-family: var(--font-body); font-weight: 800; font-size: 1rem; }
        .xc-row { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); margin-top: 14px; }
        .xc-field label { display: block; font-weight: 600; font-size: .92rem; margin-bottom: 6px; color: var(--ink); }
        .xc-field select, .xc-field input[type=text] { width: 100%; padding: .72em .9em; border: 1.5px solid var(--grey); border-radius: var(--radius-sm); font: inherit; background: #fff; color: var(--text); }
        .xc-field select:focus, .xc-field input:focus { outline: 2px solid var(--lime); border-color: var(--green); }
        .xc-product { margin-top: 20px; border: 1.5px solid var(--line); border-radius: var(--radius); padding: 20px; background: var(--bg-soft); }
        .xc-product h4 { margin-bottom: .2em; font-size: 1.25rem; margin-top: 0; }
        .xc-tags { display: flex; flex-wrap: wrap; gap: 8px; margin: 8px 0 14px; }
        .xc-tag { background: var(--cream); border: 1px solid var(--line); border-radius: 999px; padding: 3px 12px; font-size: .8rem; font-weight: 600; color: var(--green-dark); }
        .xc-feats { list-style: none; margin: 0 0 16px; padding: 0; }
        .xc-feats li { position: relative; padding-left: 22px; margin-bottom: 6px; font-size: .95rem; color: var(--text-muted); }
        .xc-feats li::before { content: ""; position: absolute; left: 0; top: .5em; width: 8px; height: 8px; border-radius: 50%; background: var(--lime); }
        .xc-pricebox { display: flex; flex-wrap: wrap; align-items: baseline; gap: 14px; border-top: 1px dashed var(--grey); padding-top: 14px; }
        .xc-mrp { color: var(--taupe); text-decoration: line-through; font-size: 1.05rem; }
        .xc-off { background: var(--green); color: #fff; border-radius: 6px; padding: 2px 10px; font-weight: 700; font-size: .85rem; }
        .xc-price { font-family: var(--font-head); font-size: 1.7rem; font-weight: 700; color: var(--green-dark); }
        .xc-choice { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 14px; }
        .xc-choice label { display: flex; align-items: center; gap: .55em; border: 1.5px solid var(--grey); border-radius: 999px; padding: .65em 1.3em; cursor: pointer; font-weight: 600; transition: all var(--t); }
        .xc-choice input { accent-color: var(--green); }
        .xc-choice label:has(input:checked) { border-color: var(--green); background: var(--cream); }
        .xc-value { margin-top: 18px; background: var(--cream); border: 1.5px solid var(--lime); border-radius: var(--radius); padding: 18px 20px; }
        .xc-capnote { font-size: .85rem; color: var(--taupe); margin: .3em 0 0; }
        .xc-summary { background: var(--green); color: #eef3e2; border-radius: var(--radius); padding: clamp(22px, 3vw, 34px); }
        .xc-summary h3 { color: #fff; margin-top: 0; }
        .xc-summary table { width: 100%; border-collapse: collapse; font-size: 1rem; }
        .xc-summary td { padding: 9px 0; border-bottom: 1px solid rgba(255, 255, 255, .18); color: #eef3e2; }
        .xc-summary td:last-child { text-align: right; font-weight: 700; white-space: nowrap; }
        .xc-summary tr.total td { border-bottom: 0; padding-top: 14px; font-family: var(--font-head); font-size: 1.45rem; color: var(--lime); }
        .xc-strike { text-decoration: line-through; opacity: .75; font-weight: 400 !important; }
        .xc-disclaimer { margin-top: 18px; background: rgba(255, 255, 255, .1); border-left: 4px solid var(--lime); border-radius: 6px; padding: 12px 16px; font-size: .88rem; line-height: 1.55; color: #eef3e2; }
        .xc-cta { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 20px; }
        @media(max-width: 560px) {
            .xc-price { font-size: 1.4rem; }
            .xc-summary tr.total td { font-size: 1.2rem; }
        }
    </style>

    <section class="section section--cream" style="padding-bottom: clamp(28px, 4vw, 48px);">
        <div class="container center">
            <span class="eyebrow">Exclusive at Fairfield</span>
            <h1>Exchange Your Old Hearing Aid<br>for a New One</h1>
            <p class="lead" style="margin-inline: auto;">Get up to <strong>₹25,000</strong> exchange value for your old hearing aid — any brand, working or not — on top of discounts of up to <strong>55%</strong> on new hearing aids. Estimate your price below in three simple steps.</p>
        </div>
    </section>

    <section class="section">
        <div class="container xc-steps">

            <!-- STEP 1 : Choose product -->
            <div class="xc-card">
                <h3><span class="xc-stepnum">1</span> Choose your new hearing aid</h3>
                <p class="muted">Select a brand and model to see its key features, MRP and Fairfield discounted price.</p>
                
                @if($isSharedView)
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 rounded-r-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-icon name="o-information-circle" class="h-5 w-5 text-blue-500" />
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700 font-medium">
                                    You are viewing a shared calculation summary. Changing any options below will create your own fresh calculation.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="xc-row">
                    <div class="xc-field">
                        <label for="xcBrand">Brand</label>
                        <select id="xcBrand" wire:model.live="selectedBrandId">
                            <option value="">— Select brand —</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="xc-field">
                        <label for="xcModel">Model</label>
                        <select id="xcModel" wire:model.live="selectedModelId" {{ empty($models) ? 'disabled' : '' }}>
                            <option value="">— Select model —</option>
                            @foreach($models as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} — MRP ₹{{ number_format($m->mrp) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($selectedModel)
                    <div class="xc-product" id="xcProduct">
                        <h4>{{ $selectedModel->manufacturer?->name }} {{ $selectedModel->name }}</h4>
                        <div class="xc-tags">
                            <span class="xc-tag">{{ $selectedModel->tech_level }} technology</span>
                            <span class="xc-tag">{{ $selectedModel->form_factor }}</span>
                            <span class="xc-tag">{{ $selectedModel->units === 2 ? 'Pair (both ears)' : 'Single ear' }}</span>
                        </div>
                        @if($selectedModel->key_features && count($selectedModel->key_features) > 0)
                            <strong style="font-size: .92rem;">Key features</strong>
                            <ul class="xc-feats" id="xcPFeats">
                                @foreach($selectedModel->key_features as $feat)
                                    <li>{{ $feat }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <div class="xc-pricebox">
                            <span class="xc-mrp">MRP <span>₹{{ number_format($selectedModel->mrp) }}</span></span>
                            <span class="xc-off">{{ $selectedModel->discount_pct }}% OFF</span>
                            <span class="xc-price">₹{{ number_format($discountedPrice) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- STEP 2 : Exchange yes/no -->
            @if($selectedModel)
                <div class="xc-card animate-fade-in" id="xcStep2">
                    <h3><span class="xc-stepnum">2</span> Do you want to exchange your old hearing aid?</h3>
                    <div class="xc-choice">
                        <label>
                            <input type="radio" name="wantExchange" wire:model.live="wantExchange" value="yes"> 
                            Yes, exchange my old hearing aid
                        </label>
                        <label>
                            <input type="radio" name="wantExchange" wire:model.live="wantExchange" value="no"> 
                            No, without exchange
                        </label>
                    </div>
                </div>
            @endif

            <!-- STEP 3 : Old aid details -->
            @if($selectedModel && $wantExchange === 'yes')
                <div class="xc-card animate-fade-in" id="xcStep3">
                    <h3><span class="xc-stepnum">3</span> Tell us about your old hearing aid</h3>
                    <div class="xc-row">
                        <div class="xc-field">
                            <label for="xcOldBrand">Company / Brand</label>
                            <select id="xcOldBrand" wire:model.live="oldBrand">
                                <option value="">— Select —</option>
                                <option value="Phonak">Phonak</option>
                                <option value="Signia / Siemens">Signia / Siemens</option>
                                <option value="ReSound">ReSound</option>
                                <option value="Widex">Widex</option>
                                <option value="Bernafon">Bernafon</option>
                                <option value="Unitron">Unitron</option>
                                <option value="Oticon">Oticon</option>
                                <option value="Starkey">Starkey</option>
                                <option value="Other">Other / Local brand</option>
                            </select>
                        </div>
                        <div class="xc-field">
                            <label for="xcOldModel">Model name / number</label>
                            <input type="text" id="xcOldModel" wire:model.live.debounce.500ms="oldModel" placeholder="e.g. Audéo P50, Pure 312, Alpha 5…">
                        </div>
                        <div class="xc-field">
                            <label for="xcOldPrice">Original purchase price</label>
                            <select id="xcOldPrice" wire:model.live="oldPriceBand">
                                <option value="">— Select —</option>
                                <option value="under_20k">Under ₹20,000</option>
                                <option value="20k_50k">₹20,000 – ₹50,000</option>
                                <option value="50k_100k">₹50,001 – ₹1,00,000</option>
                                <option value="above_100k">Above ₹1,00,000</option>
                            </select>
                        </div>
                        <div class="xc-field">
                            <label for="xcOldAge">How old is it?</label>
                            <select id="xcOldAge" wire:model.live="oldAgeBand">
                                <option value="">— Select —</option>
                                <option value="less_than_1">Less than 1 year</option>
                                <option value="1_2_years">1 – 2 years</option>
                                <option value="2_4_years">2 – 4 years</option>
                                <option value="4_6_years">4 – 6 years</option>
                                <option value="more_than_6">More than 6 years</option>
                            </select>
                        </div>
                        <div class="xc-field">
                            <label for="xcOldCond">Condition</label>
                            <select id="xcOldCond" wire:model.live="oldConditionBand">
                                <option value="">— Select —</option>
                                <option value="fully_working">Fully working</option>
                                <option value="minor_issues">Working, with minor issues</option>
                                <option value="receiver_not_working">Only receiver / speaker not working</option>
                                <option value="not_working">Not in working condition</option>
                                <option value="broken">Broken / physically damaged</option>
                            </select>
                        </div>
                    </div>
                    
                    @if($oldPriceBand && $oldAgeBand && $oldConditionBand && $oldBrand)
                        <div class="xc-value animate-fade-in" id="xcValueBox">
                            <strong>Estimated exchange value of your old hearing aid:</strong>
                            <div class="amt" id="xcValueAmt">₹{{ number_format($exchangeValue) }}</div>
                            <p class="xc-capnote" id="xcCapNote">
                                @if($isCapped)
                                    Your old hearing aid qualified for a higher value, capped at the maximum exchange value of ₹{{ number_format($exchangeValue) }}.
                                @else
                                    Exchange values range from ₹500 to a maximum of ₹12,000, subject to physical inspection.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- SUMMARY -->
            @if($selectedModel && ($wantExchange === 'no' || ($wantExchange === 'yes' && $oldPriceBand && $oldAgeBand && $oldConditionBand && $oldBrand)))
                <div class="xc-summary animate-fade-in" id="xcSummary">
                    <h3>Your Price Summary</h3>
                    <table>
                        <tr>
                            <td>Selected model</td>
                            <td>{{ $selectedModel->manufacturer?->name }} {{ $selectedModel->name }}</td>
                        </tr>
                        <tr>
                            <td>MRP</td>
                            <td class="xc-strike">₹{{ number_format($selectedModel->mrp) }}</td>
                        </tr>
                        <tr>
                            <td>Fairfield discount</td>
                            <td>{{ $selectedModel->discount_pct }}% OFF (− ₹{{ number_format($selectedModel->mrp - $discountedPrice) }})</td>
                        </tr>
                        <tr>
                            <td>Discounted price</td>
                            <td>₹{{ number_format($discountedPrice) }}</td>
                        </tr>
                        @if($wantExchange === 'yes')
                            <tr id="xcSExRow">
                                <td>Exchange value (your old hearing aid)</td>
                                <td>− ₹{{ number_format($exchangeValue) }}</td>
                            </tr>
                        @endif
                        <tr class="total">
                            <td>Estimated final price</td>
                            <td id="xcSFinal">₹{{ number_format($finalPrice) }}</td>
                        </tr>
                    </table>
                    
                    <div class="xc-disclaimer">
                        <strong>Please note:</strong> This is <u>not the final price</u> — it is an online estimate only and may vary after physical inspection of your old hearing aid and final billing at the clinic. Hearing aids are <strong>not sold online</strong> by Fairfield Hearing Clinics; this tool only shows the discount and exchange value we offer. For final pricing, please contact or visit Fairfield Hearing Clinics.
                    </div>
                    
                    <div class="xc-cta">
                        <a href="/book-a-test" class="btn btn--lime">Book a Free Consultation</a>
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="btn btn--ghost" style="--b:#fff;color:#fff">WhatsApp Us</a>
                    </div>
                </div>
            @endif

        </div>
    </section>
</div>
