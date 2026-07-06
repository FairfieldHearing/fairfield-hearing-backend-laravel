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