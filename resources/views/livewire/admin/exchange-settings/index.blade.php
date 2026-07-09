<div>
    <!-- HEADER -->
    <x-header title="Exchange Calculator Settings" subtitle="Configure valuation multipliers, price bands, and caps for the old hearing aid exchange calculator" separator progress-indicator />

    <!-- SETTINGS FORM -->
    <x-card shadow class="max-w-4xl">
        <x-form wire:submit="save">
            <!-- VALUATION LIMITS -->
            <div class="grid grid-cols-3 gap-4">
                <x-input label="Minimum Exchange Value (INR)" type="number" wire:model="min_exchange_value" required />
                <x-input label="Maximum Estimate (INR)" type="number" wire:model="max_exchange_value" required />
                <x-input label="Capped Exchange Value (INR)" type="number" wire:model="capped_exchange_value" required />
            </div>
            
            <!-- BRAND MULTIPLIERS -->
            <div class="divider mt-6 font-bold text-sm">Brand Multipliers</div>
            <div class="grid grid-cols-2 gap-4">
                <x-input label="Major Brand Factor (Multiplier)" type="number" step="0.05" wire:model="brand_multipliers.major" required />
                <x-input label="Other / Local Brand Factor (Multiplier)" type="number" step="0.05" wire:model="brand_multipliers.other" required />
            </div>

            <!-- PRICE BANDS -->
            <div class="divider mt-6 font-bold text-sm">Original Purchase Price Bands (Base Values)</div>
            <div class="grid grid-cols-4 gap-4">
                <x-input label="Under ₹20,000" type="number" wire:model="price_bands.under_20k" required />
                <x-input label="₹20,000 – ₹50,000" type="number" wire:model="price_bands.20k_50k" required />
                <x-input label="₹50,001 – ₹1,00,000" type="number" wire:model="price_bands.50k_100k" required />
                <x-input label="Above ₹1,00,000" type="number" wire:model="price_bands.above_100k" required />
            </div>

            <!-- AGE MULTIPLIERS -->
            <div class="divider mt-6 font-bold text-sm">Age Multipliers</div>
            <div class="grid grid-cols-5 gap-4">
                <x-input label="Less than 1 year" type="number" step="0.05" wire:model="age_multipliers.less_than_1" required />
                <x-input label="1 – 2 years" type="number" step="0.05" wire:model="age_multipliers.1_2_years" required />
                <x-input label="2 – 4 years" type="number" step="0.05" wire:model="age_multipliers.2_4_years" required />
                <x-input label="4 – 6 years" type="number" step="0.05" wire:model="age_multipliers.4_6_years" required />
                <x-input label="More than 6 years" type="number" step="0.05" wire:model="age_multipliers.more_than_6" required />
            </div>

            <!-- CONDITION MULTIPLIERS -->
            <div class="divider mt-6 font-bold text-sm">Condition Multipliers</div>
            <div class="grid grid-cols-5 gap-4">
                <x-input label="Fully working" type="number" step="0.05" wire:model="condition_multipliers.fully_working" required />
                <x-input label="Minor issues" type="number" step="0.05" wire:model="condition_multipliers.minor_issues" required />
                <x-input label="Receiver faulty" type="number" step="0.05" wire:model="condition_multipliers.receiver_not_working" required />
                <x-input label="Not working" type="number" step="0.05" wire:model="condition_multipliers.not_working" required />
                <x-input label="Broken / Damaged" type="number" step="0.05" wire:model="condition_multipliers.broken" required />
            </div>

            <x-slot:actions>
                <x-button label="Save Settings" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
