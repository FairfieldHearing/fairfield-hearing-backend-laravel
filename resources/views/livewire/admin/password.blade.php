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
                    <x-button label="Cancel" link="{{ route('admin.dashboard') }}" class="btn-ghost" />
                    <x-button label="Save Changes" type="submit" class="btn-primary" spinner="updateSettings" />
                </x-slot:actions>
            </x-form>
        </x-card>
    </div>
</div>