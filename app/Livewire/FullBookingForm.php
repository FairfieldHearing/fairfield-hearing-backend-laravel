<?php

namespace App\Livewire;

use App\Models\FormSubmission;
use Livewire\Component;

class FullBookingForm extends Component
{
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $problem = '';
    public string $selectedClinic = '';
    public string $time = '';
    public string $message = '';
    
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
            $customMessage = $this->message;

            if ($this->selectedClinic === 'home-visit') {
                $mainClinic = collect($this->locations)->firstWhere('is_main', true) ?? collect($this->locations)->first();
                $locationId = $mainClinic ? $mainClinic['id'] : null;
                $customMessage = trim("[Request a home visit] " . $this->message);
            } else {
                $selected = collect($this->locations)->first(fn($l) => String($l['id']) === $this->selectedClinic);
                $locationId = $selected ? $selected['id'] : (collect($this->locations)->first()['id'] ?? null);
            }

            FormSubmission::create([
                'full_name' => $this->name,
                'mobile_number' => $this->phone,
                'email' => $this->email ?: null,
                'hearing_problem' => $this->problem ?: null,
                'location_id' => $locationId,
                'preferred_day_time' => $this->time ?: null,
                'message' => $customMessage ?: null,
            ]);

            $this->status = 'success';
            $this->responseMsg = "Thank you! We've received your request and will call you shortly to confirm.";

            // WhatsApp formatting
            $chosenClinicName = $this->selectedClinic === 'home-visit'
                ? "Home Visit Request"
                : (collect($this->locations)->first(fn($l) => String($l['id']) === $this->selectedClinic)['name'] ?? '');

            $waMessage = "Hi Fairfield Hearing Clinic, I'd like to book a FREE hearing test.\nName: {$this->name}\nPhone: {$this->phone}\nEmail: {$this->email}\nClinic: {$chosenClinicName}\nPreferred Day/Time: {$this->time}\nMessage: {$this->message}";

            $this->dispatch('full-booking-success', msg: $waMessage);

            $this->reset(['name', 'phone', 'email', 'problem', 'selectedClinic', 'time', 'message']);
        } catch (\Exception $e) {
            $this->status = 'error';
            $this->responseMsg = "Something went wrong. Please call us directly or try again later.";
        }
    }

    public function render()
    {
        return view('livewire.full-booking-form');
    }
}
