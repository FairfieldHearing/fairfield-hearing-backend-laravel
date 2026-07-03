<?php

use App\Models\Ticket;
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
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Helpdesk Support Tickets" subtitle="Manage client inquiries and ticket resolutions" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-2">
            <x-select label="" wire:model.live="statusFilter" :options="[
                ['id' => 'all', 'name' => 'All Statuses'],
                ['id' => 'open', 'name' => 'Open'],
                ['id' => 'in_progress', 'name' => 'In Progress'],
                ['id' => 'resolved', 'name' => 'Resolved'],
                ['id' => 'closed', 'name' => 'Closed']
            ]" option-value="id" option-label="name" class="w-48" />
            <x-input placeholder="Search tickets..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <!-- TABLE -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$rows" :sort-by="$sortBy" with-pagination>
            @scope('cell_created_at', $ticket)
                {{ $ticket->created_at->format('M d, Y H:i') }}
            @endscope

            @scope('cell_assignedUser.name', $ticket)
                @if($ticket->assignedUser)
                    <span class="font-semibold text-sm">{{ $ticket->assignedUser->name }}</span>
                @else
                    <span class="text-base-content/40 text-xs italic">Unassigned</span>
                @endif
            @endscope

            @scope('cell_status', $ticket)
                <span class="badge badge-sm badge-outline 
                    {{ $ticket->status === 'open' ? 'badge-error' : '' }}
                    {{ $ticket->status === 'in_progress' ? 'badge-warning' : '' }}
                    {{ $ticket->status === 'resolved' ? 'badge-success' : '' }}
                    {{ $ticket->status === 'closed' ? 'badge-neutral' : '' }}
                ">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                </span>
            @endscope

            @scope('actions', $ticket)
            <div class="flex gap-2">
                <x-button icon="o-eye" wire:click="showDetails({{ $ticket->id }})" class="btn-ghost btn-sm text-primary" />
                <x-button icon="o-trash" wire:click="delete({{ $ticket->id }})" wire:confirm="Are you sure you want to delete this ticket?" class="btn-ghost btn-sm text-error" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- DETAILS DRAWER -->
    <x-drawer wire:model="drawer" title="Ticket Conversation Log" right separator with-close-button class="lg:w-1/2">
        @if($selectedTicket)
            <x-form wire:submit="save">
                <div class="space-y-6">
                    <!-- Ticket Header -->
                    <div class="flex justify-between items-start border-b border-base-200 pb-3">
                        <div>
                            <span class="text-xs text-base-content/60">Ticket Reference</span>
                            <h2 class="font-bold text-lg text-primary">{{ $selectedTicket->ticket_number }}</h2>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-base-content/60 font-semibold">Status:</span>
                            <p class="text-sm font-bold uppercase">{{ $selectedTicket->status }}</p>
                        </div>
                    </div>

                    <!-- Client Detail -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-base-content/60">Customer Name</span>
                            <p class="font-semibold text-sm">{{ $selectedTicket->customer->name }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">E-mail</span>
                            <p class="font-semibold text-sm text-primary">{{ $selectedTicket->customer->email }}</p>
                        </div>
                    </div>

                    <!-- Assigner and Status updates -->
                    <div class="grid grid-cols-2 gap-4">
                        <x-select label="Assign Support Member" wire:model="assigned_user_id" :options="$staffList" option-value="id" option-label="name" placeholder="Choose staff member" />
                        <x-select label="Ticket Status" wire:model="status" :options="[
                            ['id' => 'open', 'name' => 'Open'],
                            ['id' => 'in_progress', 'name' => 'In Progress'],
                            ['id' => 'resolved', 'name' => 'Resolved'],
                            ['id' => 'closed', 'name' => 'Closed']
                        ]" option-value="id" option-label="name" required />
                    </div>

                    <!-- Original Message -->
                    <div class="bg-base-300 p-4 rounded-lg">
                        <span class="text-xs text-base-content/50 uppercase font-bold">Original Message ({{ $selectedTicket->subject }}):</span>
                        <p class="text-sm mt-1 whitespace-pre-line text-base-content/90">{{ $selectedTicket->message }}</p>
                    </div>

                    <!-- Replies thread list -->
                    <div>
                        <h4 class="text-sm font-bold uppercase text-base-content/50 mb-2">Replies History</h4>
                        
                        <div class="space-y-3 max-h-60 overflow-y-auto mb-4 border border-base-200 p-3 rounded-lg bg-base-100">
                            @forelse($selectedTicket->replies ?: [] as $reply)
                                <div class="p-2.5 rounded-lg text-xs {{ $reply['sender'] === 'staff' ? 'bg-primary/5 text-right ml-8 border-r-4 border-primary' : 'bg-base-200 mr-8 border-l-4 border-accent' }}">
                                    <div class="flex justify-between text-base-content/50 mb-1">
                                        <span class="font-bold">{{ $reply['author'] }}</span>
                                        <span>{{ $reply['date'] }}</span>
                                    </div>
                                    <p class="text-base-content/95 text-left whitespace-pre-line">{{ $reply['message'] }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-base-content/40 italic py-2 text-center">No replies in this thread yet.</p>
                            @endforelse
                        </div>

                        <!-- Post new reply -->
                        <x-textarea label="Post Reply" wire:model="replyMessage" placeholder="Type reply message to send to the client..." rows="4" />
                    </div>
                </div>

                <x-slot:actions>
                    <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                    <x-button label="Send Reply" type="submit" class="btn-primary" spinner="save" />
                </x-slot:actions>
            </x-form>
        @endif
    </x-drawer>
</div>
