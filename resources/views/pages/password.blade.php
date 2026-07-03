<?php

use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount()
    {
        $user = auth()->user();
        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
        }
    }

    public function updateSettings(): void
    {
        $user = auth()->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . ($user?->id ?? 'NULL')],
        ];

        if ($this->password) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $this->validate($rules);

        if ($user) {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);
        }

        $this->reset(['password', 'password_confirmation']);
        $this->success('Account settings updated successfully.', position: 'toast-bottom');
    }
}; ?>

<div>
    <x-header title="Account Settings" subtitle="Update your personal details and security password" separator />

    <div class="max-w-lg">
        <x-card shadow>
            <x-form wire:submit="updateSettings">
                <x-input 
                    label="Full Name" 
                    wire:model="name" 
                    icon="o-user" 
                    required 
                />

                <x-input 
                    label="Email Address" 
                    type="email" 
                    wire:model="email" 
                    icon="o-envelope" 
                    required 
                />

                <div class="divider">Change Password (Optional)</div>

                <x-input 
                    label="New Password" 
                    type="password" 
                    wire:model="password" 
                    icon="o-lock-closed" 
                    placeholder="Leave blank to keep current password"
                />

                <x-input 
                    label="Confirm New Password" 
                    type="password" 
                    wire:model="password_confirmation" 
                    icon="o-lock-closed" 
                    placeholder="Confirm new password"
                />

                <x-slot:actions>
                    <x-button label="Cancel" link="/" class="btn-ghost" />
                    <x-button label="Save Changes" type="submit" class="btn-primary" spinner="updateSettings" />
                </x-slot:actions>
            </x-form>
        </x-card>
    </div>
</div>
