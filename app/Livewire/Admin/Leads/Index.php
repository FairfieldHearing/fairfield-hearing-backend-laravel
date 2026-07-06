<?php

namespace App\Livewire\Admin\Leads;

use Mary\Traits\Toast;
use App\Models\User;
use Illuminate\Support\Collection;
use App\Models\Lead;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Livewire\WithPagination;

class Index extends Component
{
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

    public function render()
    {
        return view('livewire.admin.leads.index', $this->with())->layout('layouts.app');
    }

}
