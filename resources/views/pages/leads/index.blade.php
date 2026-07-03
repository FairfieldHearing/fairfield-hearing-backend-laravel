<?php

use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc'];

    // Drawer / Edit fields
    public ?Lead $selectedLead = null;
    public ?int $assigned_user_id = null;
    public string $status = 'new';
    public string $logMessage = '';

    public bool $drawer = false;

    public function mount()
    {
        Gate::authorize('manage-leads');
    }

    public function showDetails(Lead $lead): void
    {
        $this->selectedLead = $lead->load(['location', 'assignedUser']);
        $this->assigned_user_id = $lead->assigned_user_id;
        $this->status = $lead->status;
        $this->logMessage = '';
        $this->drawer = true;
    }

    public function save(): void
    {
        $rules = [
            'assigned_user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:new,contacted,in_progress,won,lost',
            'logMessage' => 'nullable|string',
        ];

        $this->validate($rules);

        $currentLogs = $this->selectedLead->logs ?: [];

        // If status changed or staff changed, log it automatically
        if ($this->selectedLead->status !== $this->status) {
            $currentLogs[] = [
                'date' => now()->toDateTimeString(),
                'author' => auth()->user()->name,
                'message' => "Status changed from '{$this->selectedLead->status}' to '{$this->status}'."
            ];
        }

        if ($this->selectedLead->assigned_user_id !== $this->assigned_user_id) {
            $staffName = $this->assigned_user_id ? User::find($this->assigned_user_id)->name : 'Unassigned';
            $currentLogs[] = [
                'date' => now()->toDateTimeString(),
                'author' => auth()->user()->name,
                'message' => "Assigned staff changed to: {$staffName}."
            ];
        }

        // If manual log note added
        if ($this->logMessage) {
            $currentLogs[] = [
                'date' => now()->toDateTimeString(),
                'author' => auth()->user()->name,
                'message' => $this->logMessage
            ];
        }

        $this->selectedLead->update([
            'assigned_user_id' => $this->assigned_user_id,
            'status' => $this->status,
            'logs' => $currentLogs
        ]);

        $this->success('Lead updated successfully.', position: 'toast-bottom');
        $this->drawer = false;
    }

    public function delete(Lead $lead): void
    {
        $lead->delete();
        $this->success('Lead deleted successfully.', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'full_name', 'label' => 'Client Name', 'sortable' => true],
            ['key' => 'mobile_number', 'label' => 'Mobile', 'sortable' => true],
            ['key' => 'location.name', 'label' => 'Clinic', 'sortable' => false],
            ['key' => 'assignedUser.name', 'label' => 'Assigned Staff', 'sortable' => false],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'created_at', 'label' => 'Date Opened', 'sortable' => true],
        ];
    }

    public function leads()
    {
        return Lead::query()
            ->with(['location', 'assignedUser'])
            ->when($this->search, function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('mobile_number', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(15);
    }

    public function with(): array
    {
        return [
            'rows' => $this->leads(),
            'headers' => $this->headers(),
            'staffList' => User::all(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Leads Pipeline" subtitle="Track prospects from inquiry to hearing aid fitting" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-2">
            <x-select label="" wire:model.live="statusFilter" :options="[
                ['id' => 'all', 'name' => 'All Statuses'],
                ['id' => 'new', 'name' => 'New Lead'],
                ['id' => 'contacted', 'name' => 'Contacted'],
                ['id' => 'in_progress', 'name' => 'Assessment / Fitting In Progress'],
                ['id' => 'won', 'name' => 'Hearing Aid Purchased (Won)'],
                ['id' => 'lost', 'name' => 'Lost / Disengaged']
            ]" option-value="id" option-label="name" class="w-56" />
            <x-input placeholder="Search leads..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <!-- TABLE -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$rows" :sort-by="$sortBy" with-pagination>
            @scope('cell_created_at', $lead)
                {{ $lead->created_at->format('M d, Y') }}
            @endscope

            @scope('cell_assignedUser.name', $lead)
                @if($lead->assignedUser)
                    <span class="font-semibold text-sm">{{ $lead->assignedUser->name }}</span>
                @else
                    <span class="text-base-content/40 text-xs italic">Unassigned</span>
                @endif
            @endscope

            @scope('cell_status', $lead)
                <span class="badge badge-sm badge-outline 
                    {{ $lead->status === 'won' ? 'badge-success' : '' }}
                    {{ $lead->status === 'lost' ? 'badge-error' : '' }}
                    {{ $lead->status === 'in_progress' ? 'badge-warning' : '' }}
                    {{ $lead->status === 'contacted' ? 'badge-info' : '' }}
                    {{ $lead->status === 'new' ? 'badge-primary' : '' }}
                ">
                    {{ str_replace('_', ' ', ucfirst($lead->status)) }}
                </span>
            @endscope

            @scope('actions', $lead)
            <div class="flex gap-2">
                <x-button icon="o-eye" wire:click="showDetails({{ $lead->id }})" class="btn-ghost btn-sm text-primary" />
                <x-button icon="o-trash" wire:click="delete({{ $lead->id }})" wire:confirm="Are you sure you want to delete this lead?" class="btn-ghost btn-sm text-error" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- DETAILS DRAWER -->
    <x-drawer wire:model="drawer" title="Lead Management" right separator with-close-button class="lg:w-1/2">
        @if($selectedLead)
            <x-form wire:submit="save">
                <div class="space-y-6">
                    <!-- Client contact info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-base-content/60">Client Name</span>
                            <p class="font-bold text-lg">{{ $selectedLead->full_name }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">Phone Number</span>
                            <p class="font-semibold">{{ $selectedLead->mobile_number }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-select label="Assign Staff Member" wire:model="assigned_user_id" :options="$staffList" option-value="id" option-label="name" placeholder="Choose staff member" />
                        
                        <x-select label="Pipeline Status" wire:model="status" :options="[
                            ['id' => 'new', 'name' => 'New Lead'],
                            ['id' => 'contacted', 'name' => 'Contacted'],
                            ['id' => 'in_progress', 'name' => 'Assessment / Fitting In Progress'],
                            ['id' => 'won', 'name' => 'Hearing Aid Purchased (Won)'],
                            ['id' => 'lost', 'name' => 'Lost']
                        ]" option-value="id" option-label="name" required />
                    </div>

                    <!-- Client initial inquiry details -->
                    <div class="bg-base-200 p-4 rounded-lg space-y-2">
                        <div>
                            <span class="text-xs text-base-content/60">Inquiry Target / Hearing Issue</span>
                            <p class="font-semibold text-sm">{{ $selectedLead->hearing_problem ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">Preferred Clinic Location</span>
                            <p class="font-semibold text-sm">{{ $selectedLead->location->name }}</p>
                        </div>
                        @if($selectedLead->message)
                            <div>
                                <span class="text-xs text-base-content/60">Additional Notes</span>
                                <p class="text-sm text-base-content/80 whitespace-pre-line">{{ $selectedLead->message }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- History / Updates log -->
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-wider text-base-content/50 mb-2">History & Updates Log</h4>
                        
                        <div class="space-y-2 max-h-60 overflow-y-auto mb-4 border border-base-300 p-3 rounded-lg bg-base-100">
                            @forelse($selectedLead->logs ?: [] as $log)
                                <div class="text-xs border-b border-base-200 pb-2 last:border-0">
                                    <div class="flex justify-between text-base-content/50 mb-1">
                                        <span class="font-bold">{{ $log['author'] }}</span>
                                        <span>{{ $log['date'] }}</span>
                                    </div>
                                    <p class="text-base-content/95">{{ $log['message'] }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-base-content/40 italic py-2 text-center">No update logs recorded yet.</p>
                            @endforelse
                        </div>

                        <!-- Add new log note -->
                        <x-textarea label="Add Progress Log Note" wire:model="logMessage" placeholder="Type logs, audiologist reports, fittings updates..." rows="3" />
                    </div>
                </div>

                <x-slot:actions>
                    <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                    <x-button label="Save Changes" type="submit" class="btn-primary" spinner="save" />
                </x-slot:actions>
            </x-form>
        @endif
    </x-drawer>
</div>
