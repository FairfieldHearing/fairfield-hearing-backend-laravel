<?php

namespace App\Livewire\Admin\ExchangeSettings;

use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\ExchangeSetting;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use Toast;

    // Setting fields
    public int $min_exchange_value = 500;
    public int $max_exchange_value = 25000;
    public int $capped_exchange_value = 12000;

    public array $brand_multipliers = [
        'major' => 1.0,
        'other' => 0.8,
    ];

    public array $age_multipliers = [
        'less_than_1' => 1.0,
        '1_2_years' => 0.8,
        '2_4_years' => 0.6,
        '4_6_years' => 0.4,
        'more_than_6' => 0.25,
    ];

    public array $condition_multipliers = [
        'fully_working' => 1.0,
        'minor_issues' => 0.8,
        'receiver_not_working' => 0.6,
        'not_working' => 0.35,
        'broken' => 0.2,
    ];

    public array $price_bands = [
        'under_20k' => 6000,
        '20k_50k' => 10000,
        '50k_100k' => 16000,
        'above_100k' => 25000,
    ];

    public function mount()
    {
        Gate::authorize('manage-content');

        // Load existing settings
        $this->min_exchange_value = (int) ExchangeSetting::getValue('min_exchange_value', 500);
        $this->max_exchange_value = (int) ExchangeSetting::getValue('max_exchange_value', 25000);
        $this->capped_exchange_value = (int) ExchangeSetting::getValue('capped_exchange_value', 12000);

        $this->brand_multipliers = array_merge($this->brand_multipliers, ExchangeSetting::getValue('brand_multipliers', []));
        $this->age_multipliers = array_merge($this->age_multipliers, ExchangeSetting::getValue('age_multipliers', []));
        $this->condition_multipliers = array_merge($this->condition_multipliers, ExchangeSetting::getValue('condition_multipliers', []));
        $this->price_bands = array_merge($this->price_bands, ExchangeSetting::getValue('price_bands', []));
    }

    public function save()
    {
        $this->validate([
            'min_exchange_value' => 'required|integer|min:0',
            'max_exchange_value' => 'required|integer|gte:min_exchange_value',
            'capped_exchange_value' => 'required|integer|gte:min_exchange_value',
            
            'brand_multipliers.major' => 'required|numeric|min:0|max:5',
            'brand_multipliers.other' => 'required|numeric|min:0|max:5',

            'age_multipliers.less_than_1' => 'required|numeric|min:0|max:5',
            'age_multipliers.1_2_years' => 'required|numeric|min:0|max:5',
            'age_multipliers.2_4_years' => 'required|numeric|min:0|max:5',
            'age_multipliers.4_6_years' => 'required|numeric|min:0|max:5',
            'age_multipliers.more_than_6' => 'required|numeric|min:0|max:5',

            'condition_multipliers.fully_working' => 'required|numeric|min:0|max:5',
            'condition_multipliers.minor_issues' => 'required|numeric|min:0|max:5',
            'condition_multipliers.receiver_not_working' => 'required|numeric|min:0|max:5',
            'condition_multipliers.not_working' => 'required|numeric|min:0|max:5',
            'condition_multipliers.broken' => 'required|numeric|min:0|max:5',

            'price_bands.under_20k' => 'required|integer|min:0',
            'price_bands.20k_50k' => 'required|integer|min:0',
            'price_bands.50k_100k' => 'required|integer|min:0',
            'price_bands.above_100k' => 'required|integer|min:0',
        ]);

        ExchangeSetting::setValue('min_exchange_value', $this->min_exchange_value);
        ExchangeSetting::setValue('max_exchange_value', $this->max_exchange_value);
        ExchangeSetting::setValue('capped_exchange_value', $this->capped_exchange_value);

        ExchangeSetting::setValue('brand_multipliers', $this->brand_multipliers);
        ExchangeSetting::setValue('age_multipliers', $this->age_multipliers);
        ExchangeSetting::setValue('condition_multipliers', $this->condition_multipliers);
        ExchangeSetting::setValue('price_bands', $this->price_bands);

        $this->success('Exchange calculator settings updated successfully.', position: 'toast-bottom');
    }

    public function render()
    {
        return view('livewire.admin.exchange-settings.index')->layout('layouts.app');
    }
}
