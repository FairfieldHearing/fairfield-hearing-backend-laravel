<?php

namespace App\Livewire\Web;

use Livewire\Component;
use App\Models\Manufacturer;
use App\Models\HearingAidModel;
use App\Models\ExchangeSetting;
use App\Models\ExchangeEstimate;
use Illuminate\Http\Request;
use App\Traits\HasSeo;

class Exchange extends Component
{
    use HasSeo;
    public $brands;
    public $models = [];

    // User selections
    public ?int $selectedBrandId = null;
    public ?int $selectedModelId = null;
    public ?string $wantExchange = null;
    public string $oldBrand = '';
    public string $oldModel = '';
    public string $oldPriceBand = '';
    public string $oldAgeBand = '';
    public string $oldConditionBand = '';

    // Calculation outputs
    public int $discountedPrice = 0;
    public int $exchangeValue = 0;
    public int $rawExchangeValue = 0;
    public int $finalPrice = 0;
    public bool $isCapped = false;

    // Sharing & Session
    public ?string $uniqueHash = null;
    public string $whatsappUrl = '';
    public bool $isSharedView = false;

    public function mount(Request $request)
    {
        $this->brands = Manufacturer::where('is_active', true)
            ->whereHas('hearingAidModels', fn($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();

        if ($request->has('s')) {
            $estimate = ExchangeEstimate::where('unique_hash', $request->get('s'))->first();
            if ($estimate) {
                $this->isSharedView = true;
                $this->uniqueHash = $estimate->unique_hash;
                $this->selectedModelId = $estimate->hearing_aid_model_id;
                
                $model = HearingAidModel::find($this->selectedModelId);
                if ($model) {
                    $this->selectedBrandId = $model->manufacturer_id;
                    $this->models = HearingAidModel::where('manufacturer_id', $this->selectedBrandId)->where('is_active', true)->orderBy('sort_order')->get();
                }

                $this->wantExchange = $estimate->want_exchange ? 'yes' : 'no';
                $this->oldBrand = $estimate->old_brand ?? '';
                $this->oldModel = $estimate->old_model ?? '';
                $this->oldPriceBand = $estimate->old_price_band ?? '';
                $this->oldAgeBand = $estimate->old_age_band ?? '';
                $this->oldConditionBand = $estimate->old_condition_band ?? '';
                
                session(['exchange_estimate_id' => $estimate->id]);
                $this->recalculate();
                return;
            }
        }

        // Restore from session if not a shared view link
        if (session()->has('exchange_estimate_id')) {
            $estimate = ExchangeEstimate::find(session('exchange_estimate_id'));
            if ($estimate) {
                $this->uniqueHash = $estimate->unique_hash;
                $this->selectedModelId = $estimate->hearing_aid_model_id;
                
                $model = HearingAidModel::find($this->selectedModelId);
                if ($model) {
                    $this->selectedBrandId = $model->manufacturer_id;
                    $this->models = HearingAidModel::where('manufacturer_id', $this->selectedBrandId)->where('is_active', true)->orderBy('sort_order')->get();
                }

                $this->wantExchange = $estimate->want_exchange ? 'yes' : 'no';
                $this->oldBrand = $estimate->old_brand ?? '';
                $this->oldModel = $estimate->old_model ?? '';
                $this->oldPriceBand = $estimate->old_price_band ?? '';
                $this->oldAgeBand = $estimate->old_age_band ?? '';
                $this->oldConditionBand = $estimate->old_condition_band ?? '';
                
                $this->recalculate();
            }
        }
    }

    public function updated($propertyName)
    {
        // If they change anything and they were looking at a shared view, clone it to make a new editable estimate
        if ($this->isSharedView) {
            $this->isSharedView = false;
            $this->uniqueHash = null;
            session()->forget('exchange_estimate_id');
        }

        if ($propertyName === 'selectedBrandId') {
            $this->selectedModelId = null;
            if ($this->selectedBrandId) {
                $this->models = HearingAidModel::where('manufacturer_id', $this->selectedBrandId)->where('is_active', true)->orderBy('sort_order')->get();
            } else {
                $this->models = [];
            }
        }

        $this->recalculate();
    }

    public function recalculate()
    {
        $model = HearingAidModel::with('manufacturer')->find($this->selectedModelId);
        
        if (!$model) {
            $this->discountedPrice = 0;
            $this->exchangeValue = 0;
            $this->rawExchangeValue = 0;
            $this->finalPrice = 0;
            $this->isCapped = false;
            return;
        }

        // 1. Calculate new hearing aid discounted price
        $this->discountedPrice = (int) round($model->mrp * (100 - $model->discount_pct) / 100);

        // 2. Calculate exchange valuation
        if ($this->wantExchange === 'yes') {
            $minVal = (int) ExchangeSetting::getValue('min_exchange_value', 500);
            $maxVal = (int) ExchangeSetting::getValue('max_exchange_value', 25000);
            $cappedVal = (int) ExchangeSetting::getValue('capped_exchange_value', 12000);
            $brandFactors = ExchangeSetting::getValue('brand_multipliers', ['major' => 1.0, 'other' => 0.8]);
            $ageFactors = ExchangeSetting::getValue('age_multipliers', [
                'less_than_1' => 1.0,
                '1_2_years' => 0.8,
                '2_4_years' => 0.6,
                '4_6_years' => 0.4,
                'more_than_6' => 0.25
            ]);
            $conditionFactors = ExchangeSetting::getValue('condition_multipliers', [
                'fully_working' => 1.0,
                'minor_issues' => 0.8,
                'receiver_not_working' => 0.6,
                'not_working' => 0.35,
                'broken' => 0.2
            ]);
            $priceBands = ExchangeSetting::getValue('price_bands', [
                'under_20k' => 6000,
                '20k_50k' => 10000,
                '50k_100k' => 16000,
                'above_100k' => 25000
            ]);

            $basePrice = $priceBands[$this->oldPriceBand] ?? 0;
            $ageFactor = $ageFactors[$this->oldAgeBand] ?? 1.0;
            $condFactor = $conditionFactors[$this->oldConditionBand] ?? 1.0;
            
            $isMajor = in_array(strtolower($this->oldBrand), ['phonak', 'signia', 'siemens', 'resound', 'widex', 'bernafon', 'unitron', 'oticon', 'starkey']);
            $brandFactor = $isMajor ? ($brandFactors['major'] ?? 1.0) : ($brandFactors['other'] ?? 0.8);

            if ($basePrice > 0 && $this->oldBrand) {
                $val = $basePrice * $ageFactor * $condFactor * $brandFactor;
                $val = round($val / 100) * 100;
                
                // Clamp
                $val = max($minVal, min($maxVal, $val));
                
                $this->rawExchangeValue = $val;
                $this->exchangeValue = min($val, $cappedVal);
                $this->isCapped = $val > $cappedVal;
            } else {
                $this->exchangeValue = 0;
                $this->rawExchangeValue = 0;
                $this->isCapped = false;
            }
        } else {
            $this->exchangeValue = 0;
            $this->rawExchangeValue = 0;
            $this->isCapped = false;
        }

        // 3. Final estimate price
        $this->finalPrice = max(0, $this->discountedPrice - $this->exchangeValue);

        // 4. Save estimate record to database
        if (!$this->isSharedView) {
            $data = [
                'session_id' => session()->getId(),
                'hearing_aid_model_id' => $this->selectedModelId,
                'want_exchange' => $this->wantExchange === 'yes',
                'old_brand' => $this->oldBrand ?: null,
                'old_model' => $this->oldModel ?: null,
                'old_price_band' => $this->oldPriceBand ?: null,
                'old_age_band' => $this->oldAgeBand ?: null,
                'old_condition_band' => $this->oldConditionBand ?: null,
                'calculated_value' => $this->exchangeValue,
                'final_price' => $this->finalPrice,
            ];

            if ($this->uniqueHash) {
                ExchangeEstimate::where('unique_hash', $this->uniqueHash)->update($data);
                $estimate = ExchangeEstimate::where('unique_hash', $this->uniqueHash)->first();
            } else {
                $estimate = ExchangeEstimate::create($data);
                $this->uniqueHash = $estimate->unique_hash;
            }

            if ($estimate) {
                session(['exchange_estimate_id' => $estimate->id]);
            }
        }

        // 5. Generate WhatsApp link
        $url = route('web.exchange') . '?s=' . $this->uniqueHash;
        $message = "Hi, I am interested in the hearing aid exchange offer on your website.\n\n";
        $message .= "New Model: " . ($model->manufacturer?->name ?? '') . " " . $model->name . "\n";
        $message .= "MRP: ₹" . number_format($model->mrp) . "\n";
        $message .= "Discounted Price: ₹" . number_format($this->discountedPrice) . "\n";
        
        if ($this->wantExchange === 'yes') {
            $message .= "Exchanging Old Aid: " . $this->oldBrand . " (" . $this->oldModel . ")\n";
            $message .= "Estimated Exchange Value: ₹" . number_format($this->exchangeValue) . "\n";
        }
        
        $message .= "Estimated Final Price: ₹" . number_format($this->finalPrice) . "\n\n";
        $message .= "View calculation details here: " . $url;

        $this->whatsappUrl = "https://wa.me/919811418578?text=" . urlencode($message);
    }

    public function render()
    {
        $selectedModel = $this->selectedModelId ? HearingAidModel::with('manufacturer')->find($this->selectedModelId) : null;

        $defaults = [
            'title' => 'Exchange Your Old Hearing Aid | Fairfield Hearing Clinics',
            'description' => 'Exchange your old hearing aid for a new one at Fairfield Hearing Clinics. Get up to ₹25,000 exchange value plus up to 55% off on new hearing aids from Phonak, Signia, ReSound, Unitron, Widex & Bernafon.',
        ];

        return view('livewire.web.exchange', [
            'selectedModel' => $selectedModel,
        ])->layout('layouts.web', array_merge($defaults, $this->seo('exchange')));
    }
}
