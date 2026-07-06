<div class="hero__card" x-data="{ isDisabled: false }" @hero-booking-success.window="window.open('https://wa.me/919811551399?text=' + encodeURIComponent($event.detail.msg), '_blank')">
    <h3>Book your free hearing test</h3>
    <p class="muted" style="margin: .2em 0 0; font-size: .95rem;">
        Takes 30 seconds. We'll call to confirm your slot.
    </p>
    <form class="hero__form" wire:submit.prevent="submit">
        <div class="field">
            <label for="h-name">Full name</label>
            <input
                id="h-name"
                name="name"
                type="text"
                required
                autocomplete="name"
                placeholder="Your name"
                wire:model="name"
                :disabled="isDisabled"
            />
        </div>
        <div class="field">
            <label for="h-phone">Mobile number</label>
            <input
                id="h-phone"
                name="phone"
                type="tel"
                required
                autocomplete="tel"
                pattern="[0-9+ ]{8,15}"
                placeholder="+91 XXXXX XXXXX"
                wire:model="phone"
                :disabled="isDisabled"
            />
        </div>
        <div class="field">
            <label for="h-problem">Type of hearing problem</label>
            <select
                id="h-problem"
                name="hearing_problem"
                wire:model="problem"
                :disabled="isDisabled"
            >
                <option value="" disabled selected>Select your problem</option>
                <option value="General Hearing Loss">General Hearing Loss</option>
                <option value="Ringing in ears (Tinnitus)">Ringing in ears (Tinnitus)</option>
                <option value="Ear Pain or Blockage">Ear Pain or Blockage</option>
                <option value="Ear Discharge">Ear Discharge</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="field">
            <label for="h-clinic">Preferred clinic</label>
            <select
                id="h-clinic"
                name="clinic"
                required
                wire:model="selectedClinic"
                :disabled="isDisabled"
            >
                <option value="" disabled selected>Select a clinic</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc['id'] }}">
                        {{ $loc['name'] }}
                    </option>
                @endforeach
                <option value="home-visit">Request a home visit</option>
            </select>
        </div>
        <button type="submit" class="btn btn--block btn--lg" :disabled="isDisabled">
            @if($status === 'submitting')
                Requesting...
            @else
                Request My Free Test
            @endif
        </button>
        
        @if($responseMsg)
            <p
                role="status"
                style="margin-top: 12px; padding: 12px 14px; border-radius: 8px; background: {{ $status === 'success' ? '#eef6da' : '#fdf2f2' }}; color: {{ $status === 'success' ? '#445e15' : '#9b1c1c' }}; font-weight: 600; border: 1px solid {{ $status === 'success' ? '#cfe39a' : '#f8b4b4' }};"
            >
                {{ $responseMsg }}
            </p>
        @endif
        
        <p class="muted" style="font-size: .78rem; margin: .2em 0 0;">
            By submitting you agree to our privacy policy. We never share your details.
        </p>
    </form>
</div>
