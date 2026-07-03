<?php

use App\Models\FormSubmission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
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
        $this->selectedSubmission = $submission->load('location');
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
            ['key' => 'created_at', 'label' => 'Date Received', 'sortable' => true],
        ];
    }

    public function submissions()
    {
        return FormSubmission::query()
            ->with('location')
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
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Form Submissions" subtitle="View and manage leads sent from the frontend" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search leads..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <!-- TABLE -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$rows" :sort-by="$sortBy" with-pagination>
            @scope('cell_created_at', $sub)
                {{ $sub->created_at->format('M d, Y H:i') }}
            @endscope

            @scope('actions', $sub)
            <div class="flex gap-2">
                <x-button icon="o-eye" wire:click="showDetails({{ $sub->id }})" class="btn-ghost btn-sm text-primary" />
                <x-button icon="o-trash" wire:click="delete({{ $sub->id }})" wire:confirm="Are you sure you want to delete this submission?" class="btn-ghost btn-sm text-error" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- DETAILS DRAWER -->
    <x-drawer wire:model="drawer" title="Submission Details" right separator with-close-button class="lg:w-1/3">
        @if($selectedSubmission)
            <div class="space-y-6">
                <!-- Basic Contact -->
                <div>
                    <h3 class="text-sm font-semibold text-base-content/50 uppercase tracking-wider mb-2">Lead Information</h3>
                    <div class="bg-base-200 p-4 rounded-lg space-y-2">
                        <div>
                            <span class="text-xs text-base-content/60">Full Name</span>
                            <p class="font-bold text-base">{{ $selectedSubmission->full_name }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">Mobile Number</span>
                            <p class="font-semibold">{{ $selectedSubmission->mobile_number }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">Email Address</span>
                            <p class="font-semibold text-primary">{{ $selectedSubmission->email ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div>
                    <h3 class="text-sm font-semibold text-base-content/50 uppercase tracking-wider mb-2">Service Request</h3>
                    <div class="bg-base-200 p-4 rounded-lg space-y-2">
                        <div>
                            <span class="text-xs text-base-content/60">Preferred Clinic</span>
                            <p class="font-bold text-success">{{ $selectedSubmission->location->name }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">Type of Hearing Problem</span>
                            <p class="font-semibold">{{ $selectedSubmission->hearing_problem ?: 'Not specified' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">Preferred Day / Time</span>
                            <p class="font-semibold text-warning">{{ $selectedSubmission->preferred_day_time ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div>
                    <h3 class="text-sm font-semibold text-base-content/50 uppercase tracking-wider mb-2">Anything we should know?</h3>
                    <div class="bg-base-200 p-4 rounded-lg">
                        <p class="whitespace-pre-line text-sm text-base-content/80">{{ $selectedSubmission->message ?: 'No additional notes provided.' }}</p>
                    </div>
                </div>

                <!-- Timestamp -->
                <div class="text-xs text-base-content/50 text-right">
                    Received on: {{ $selectedSubmission->created_at->format('F d, Y \a\t H:i') }}
                </div>
            </div>

            <x-slot:actions>
                <x-button label="Convert to Lead" icon="o-user-plus" wire:click="convertToLead({{ $selectedSubmission->id }})" class="btn-success" />
                <x-button label="Convert to Ticket" icon="o-ticket" wire:click="convertToTicket({{ $selectedSubmission->id }})" class="btn-info" />
                <x-button label="Delete" icon="o-trash" wire:click="delete({{ $selectedSubmission->id }})" wire:confirm="Are you sure you want to delete this submission?" class="btn-error btn-outline mr-auto" />
                <x-button label="Close" @click="$wire.drawer = false" class="btn-ghost" />
            </x-slot:actions>
        @endif
    </x-drawer>
</div>
