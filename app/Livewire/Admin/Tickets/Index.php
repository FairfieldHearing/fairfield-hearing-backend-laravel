<?php

namespace App\Livewire\Admin\Tickets;

use Mary\Traits\Toast;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Livewire\WithPagination;
use App\Models\Ticket;

class Index extends Component
{
use Toast, WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc'];

    // Drawer / Edit fields
    public ?Ticket $selectedTicket = null;
    public ?int $assigned_user_id = null;
    public string $status = 'open';
    public string $replyMessage = '';

    public bool $drawer = false;

    public function mount()
    {
        Gate::authorize('manage-tickets');
    }

    public function showDetails(Ticket $ticket): void
    {
        $this->selectedTicket = $ticket->load(['customer', 'assignedUser']);
        $this->assigned_user_id = $ticket->assigned_user_id;
        $this->status = $ticket->status;
        $this->replyMessage = '';
        $this->drawer = true;
    }

    public function save(): void
    {
        $rules = [
            'assigned_user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:open,in_progress,resolved,closed',
            'replyMessage' => 'nullable|string',
        ];

        $this->validate($rules);

        $currentReplies = $this->selectedTicket->replies ?: [];

        // If a reply message was added
        if ($this->replyMessage) {
            $currentReplies[] = [
                'sender' => 'staff',
                'author' => auth()->user()->name,
                'message' => $this->replyMessage,
                'date' => now()->toDateTimeString()
            ];
        }

        $this->selectedTicket->update([
            'assigned_user_id' => $this->assigned_user_id,
            'status' => $this->status,
            'replies' => $currentReplies
        ]);

        $this->success('Ticket updated successfully.', position: 'toast-bottom');
        $this->drawer = false;
    }

    public function delete(Ticket $ticket): void
    {
        $ticket->delete();
        $this->success('Ticket deleted successfully.', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'ticket_number', 'label' => 'Ticket ID', 'sortable' => true],
            ['key' => 'customer.name', 'label' => 'Customer', 'sortable' => false],
            ['key' => 'subject', 'label' => 'Subject', 'sortable' => true],
            ['key' => 'assignedUser.name', 'label' => 'Assigned Support', 'sortable' => false],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'created_at', 'label' => 'Created', 'sortable' => true],
        ];
    }

    public function tickets()
    {
        return Ticket::query()
            ->with(['customer', 'assignedUser'])
            ->when($this->search, function ($query) {
                $query->where('ticket_number', 'like', '%' . $this->search . '%')
                    ->orWhere('subject', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
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
            'rows' => $this->tickets(),
            'headers' => $this->headers(),
            'staffList' => User::all(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.tickets.index', $this->with())->layout('layouts.app');
    }

}
