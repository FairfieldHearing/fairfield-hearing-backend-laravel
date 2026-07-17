<?php

namespace App\Livewire;

use App\Models\FormSubmission;
use App\Models\Location;
use Livewire\Component;

class HeroBookingForm extends Component
{
    public string $name = '';
    public string $phone = '';
    public string $problem = '';
    public string $selectedClinic = '';
    
    public string $responseMsg = '';
    public string $status = 'idle';

    public $locations;

    public function mount($locations = [])
    {
        $this->locations = $locations;
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:8|max:15',
            'selectedClinic' => 'required|string',
        ]);

        $this->status = 'submitting';

        try {
            $locationId = null;
            $messageSuffix = "";

            if ($this->selectedClinic === 'home-visit') {
                $mainClinic = collect($this->locations)->firstWhere('is_main', true) ?? collect($this->locations)->first();
                $locationId = $mainClinic ? $mainClinic['id'] : null;
                $messageSuffix = "Request a home visit.";
            } else {
                $selected = collect($this->locations)->first(fn ($l) => (string)$l['id'] === $this->selectedClinic || ($l['slug'] ?? '') === $this->selectedClinic);
                $locationId = $selected ? $selected['id'] : (collect($this->locations)->first()['id'] ?? null);
            }

            FormSubmission::create([
                'full_name' => $this->name,
                'mobile_number' => $this->phone,
                'hearing_problem' => $this->problem ?: null,
                'location_id' => $locationId,
                'message' => $messageSuffix ?: null,
                'exchange_estimate_id' => session('exchange_estimate_id'),
            ]);

            $this->status = 'success';
            $this->responseMsg = "Thank you! We've received your request and will call you shortly to confirm.";

            // Dispatch event for WhatsApp redirection
            $chosenClinicName = $this->selectedClinic === 'home-visit' 
                ? "Home Visit Request" 
                : (collect($this->locations)->first(fn($l) => (string)$l['id'] === $this->selectedClinic)['name'] ?? '');

            $waMessage = "Hi Fairfield Hearing Clinic, I'd like to book a FREE hearing test.\nName: {$this->name}\nPhone: {$this->phone}\nClinic: {$chosenClinicName}";

            $this->dispatch('hero-booking-success', msg: $waMessage);

            $this->reset(['name', 'phone', 'problem', 'selectedClinic']);
        } catch (\Exception $e) {
            $this->status = 'error';
            $this->responseMsg = "Something went wrong. Please call us directly or try again later.";
        }
    }

    public function render()
    {
        return view('livewire.hero-booking-form');
    }
}
