<?php

namespace App\Livewire\Admin\Users;

use Mary\Traits\Toast;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
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

    public function render()
    {
        return view('livewire.admin.users.index', $this->with())->layout('layouts.app');
    }

}
