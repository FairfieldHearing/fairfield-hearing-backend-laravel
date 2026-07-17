<?php

namespace App\Livewire\Admin\Submissions;

use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use Livewire\Component;
use App\Models\FormSubmission;
use Illuminate\Support\Facades\Gate;
use Livewire\WithPagination;

class Index extends Component
{
    use Toast, WithPagination;

    public function mount()
    {
        Gate::authorize('manage-leads');
    }

    public function convertToLead(FormSubmission $submission): void
    {
        if (\App\Models\Lead::where('form_submission_id', $submission->id)->exists()) {
            $this->warning('This submission has already been converted to a Lead.', position: 'toast-bottom');
            return;
        }

        \App\Models\Lead::create([
            'form_submission_id' => $submission->id,
            'full_name' => $submission->full_name,
            'mobile_number' => $submission->mobile_number,
            'email' => $submission->email,
            'hearing_problem' => $submission->hearing_problem,
            'location_id' => $submission->location_id,
            'preferred_day_time' => $submission->preferred_day_time,
            'message' => $submission->message,
            'status' => 'new',
            'logs' => [
                [
                    'date' => now()->toDateTimeString(),
                    'author' => auth()->user()->name,
                    'message' => 'Lead created via Form Submission conversion.'
                ]
            ]
        ]);

        $this->success('Submission converted to Lead successfully.', position: 'toast-bottom');
        $this->drawer = false;
    }

    public function convertToTicket(FormSubmission $submission): void
    {
        if (\App\Models\Ticket::where('form_submission_id', $submission->id)->exists()) {
            $this->warning('This submission has already been converted to a Support Ticket.', position: 'toast-bottom');
            return;
        }

        $customer = \App\Models\Customer::firstOrCreate(
            ['email' => $submission->email ?: 'no-email@fairfield-hearing.com'],
            [
                'name' => $submission->full_name,
                'phone' => $submission->mobile_number,
            ]
        );

        \App\Models\Ticket::create([
            'ticket_number' => 'TCK-' . strtoupper(str()->random(6)),
            'customer_id' => $customer->id,
            'form_submission_id' => $submission->id,
            'subject' => 'Service Inquiry: ' . ($submission->hearing_problem ?: 'General'),
            'message' => $submission->message ?: 'Raised from Form Submission.',
            'status' => 'open',
            'secure_token' => str()->random(32),
            'replies' => [],
        ]);

        $this->success('Submission converted to Support Ticket successfully.', position: 'toast-bottom');
        $this->drawer = false;
    }

    public string $search = '';
    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc'];

    // Drawer state
    public ?FormSubmission $selectedSubmission = null;
    public bool $drawer = false;

    public function showDetails(FormSubmission $submission): void
    {
        $this->selectedSubmission = $submission->load(['location', 'exchangeEstimate.hearingAidModel.manufacturer']);
        $this->drawer = true;
    }

    public function delete(FormSubmission $submission): void
    {
        $submission->delete();
        $this->success('Submission deleted successfully.', position: 'toast-bottom');
        $this->drawer = false;
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'full_name', 'label' => 'Full Name', 'sortable' => true],
            ['key' => 'mobile_number', 'label' => 'Mobile', 'sortable' => true],
            ['key' => 'email', 'label' => 'E-mail', 'sortable' => true],
            ['key' => 'location.name', 'label' => 'Preferred Clinic', 'sortable' => false],
            ['key' => 'status', 'label' => 'Status', 'sortable' => false],
            ['key' => 'created_at', 'label' => 'Date Received', 'sortable' => true],
        ];
    }

    public function submissions()
    {
        return FormSubmission::query()
            ->with(['location', 'lead', 'ticket'])
            ->when($this->search, function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('mobile_number', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(15);
    }

    public function with(): array
    {
        return [
            'rows' => $this->submissions(),
            'headers' => $this->headers(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.submissions.index', $this->with())->layout('layouts.app');
    }
}
