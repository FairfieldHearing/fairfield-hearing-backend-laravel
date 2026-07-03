<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public string $search = '';
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    // Form fields
    public ?User $selectedUser = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public array $selectedRoles = [];

    public bool $drawer = false;

    public function mount()
    {
        Gate::authorize('manage-staff');
    }

    public function showCreate(): void
    {
        $this->resetValidation();
        $this->selectedUser = null;
        $this->reset(['name', 'email', 'password', 'selectedRoles']);
        $this->drawer = true;
    }

    public function showEdit(User $user): void
    {
        // Prevent editing superadmins if logged-in user is not a superadmin
        if ($user->hasRole('superadmin') && !auth()->user()->hasRole('superadmin')) {
            $this->error('Access denied.', position: 'toast-bottom');
            return;
        }

        $this->resetValidation();
        $this->selectedUser = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->selectedRoles = $user->roles ?: [];
        $this->drawer = true;
    }

    public function save(): void
    {
        $allowedRoles = ['manage_staff', 'blog_posting', 'leads_management', 'content_uploading', 'support'];
        if (auth()->user()->hasRole('superadmin')) {
            $allowedRoles[] = 'superadmin';
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . ($this->selectedUser?->id ?? 'NULL'),
            'selectedRoles' => 'required|array|min:1',
            'selectedRoles.*' => 'in:' . implode(',', $allowedRoles),
        ];

        if (!$this->selectedUser) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->selectedRoles,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->selectedUser) {
            $this->selectedUser->update($data);
            $this->success('Staff member updated successfully.', position: 'toast-bottom');
        } else {
            User::create($data);
            $this->success('Staff member created successfully.', position: 'toast-bottom');
        }

        $this->drawer = false;
    }

    public function delete(User $user): void
    {
        if ($user->id === auth()->id()) {
            $this->error('You cannot delete your own account.', position: 'toast-bottom');
            return;
        }

        if ($user->hasRole('superadmin') && !auth()->user()->hasRole('superadmin')) {
            $this->error('Access denied.', position: 'toast-bottom');
            return;
        }

        $user->delete();
        $this->success('Staff member deleted successfully.', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Name', 'sortable' => true],
            ['key' => 'email', 'label' => 'E-mail', 'sortable' => true],
            ['key' => 'roles', 'label' => 'Roles', 'sortable' => false],
        ];
    }

    public function users()
    {
        return User::query()
            ->when(!auth()->user()->hasRole('superadmin'), function ($query) {
                // Completely hide superadmins from staff view
                $query->whereJsonDoesntContain('roles', 'superadmin');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->get();
    }

    public function with(): array
    {
        $roles = [
            ['id' => 'manage_staff', 'name' => 'Manage Staff (Create & Edit Accounts)'],
            ['id' => 'blog_posting', 'name' => 'Blog Posting (Manage Articles)'],
            ['id' => 'leads_management', 'name' => 'Leads Management'],
            ['id' => 'content_uploading', 'name' => 'Content Uploading (Blogs, FAQ, Locations, Policies)'],
            ['id' => 'support', 'name' => 'Support Staff (Helpdesk Tickets)']
        ];

        if (auth()->user()->hasRole('superadmin')) {
            array_unshift($roles, ['id' => 'superadmin', 'name' => 'Super Admin (All Permissions)']);
        }

        return [
            'rows' => $this->users(),
            'headers' => $this->headers(),
            'availableRoles' => $roles
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Staff Management" subtitle="Manage admin users and permissions" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search staff..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Staff" wire:click="showCreate" class="btn-primary" icon="o-plus" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$rows" :sort-by="$sortBy">
            @scope('cell_roles', $usr)
                <div class="flex flex-wrap gap-1">
                    @foreach($usr->roles ?: [] as $role)
                        <span class="badge badge-sm badge-neutral">{{ $role }}</span>
                    @endforeach
                </div>
            @endscope

            @scope('actions', $usr)
            <div class="flex gap-2">
                <x-button icon="o-pencil" wire:click="showEdit({{ $usr->id }})" class="btn-ghost btn-sm text-primary" />
                @if($usr->id !== auth()->id())
                    <x-button icon="o-trash" wire:click="delete({{ $usr->id }})" wire:confirm="Are you sure you want to delete this user?" class="btn-ghost btn-sm text-error" />
                @endif
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FORM DRAWER -->
    <x-drawer wire:model="drawer" title="{{ $selectedUser ? 'Edit Staff' : 'Add Staff' }}" right separator with-close-button class="lg:w-1/3">
        <x-form wire:submit="save">
            <x-input label="Name" wire:model="name" required />
            <x-input label="E-mail" type="email" wire:model="email" required />
            <x-input label="Password" type="password" wire:model="password" :placeholder="$selectedUser ? 'Leave blank to keep current password' : ''" />

            <div class="divider">Assign Roles</div>
            
            <div class="space-y-2">
                @foreach($availableRoles as $r)
                    <div class="flex items-start gap-2">
                        <input type="checkbox" id="role-{{ $r['id'] }}" value="{{ $r['id'] }}" wire:model="selectedRoles" class="checkbox checkbox-primary checkbox-sm mt-1" />
                        <label for="role-{{ $r['id'] }}" class="cursor-pointer">
                            <span class="font-bold text-sm block">{{ $r['id'] }}</span>
                            <span class="text-xs text-base-content/60">{{ $r['name'] }}</span>
                        </label>
                    </div>
                @endforeach
            </div>

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
