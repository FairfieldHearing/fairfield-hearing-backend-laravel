<div>
    <!-- HEADER -->
    <x-header title="Policy Pages" subtitle="Manage terms of service, privacy policies and legality disclosures" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Page" wire:click="showCreate" class="btn-primary" icon="o-plus" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$rows" :sort-by="$sortBy">
            @scope('cell_updated_at', $policy)
                {{ $policy->updated_at->format('M d, Y H:i') }}
            @endscope

            @scope('actions', $policy)
            <div class="flex gap-2">
                <x-button icon="o-pencil" wire:click="showEdit({{ $policy->id }})" class="btn-ghost btn-sm text-primary" />
                <x-button icon="o-trash" wire:click="delete({{ $policy->id }})" wire:confirm="Are you sure you want to delete this policy page?" class="btn-ghost btn-sm text-error" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FORM DRAWER -->
    <x-drawer wire:model="drawer" title="{{ $policy ? 'Edit Policy Page' : 'Create Policy Page' }}" right separator with-close-button class="lg:w-1/2">
        <x-form wire:submit="save">
            <x-input label="Title" wire:model.live.debounce.500ms="title" placeholder="e.g. Terms of Service" required />
            <x-input label="Slug" wire:model="slug" required />

            <x-textarea label="Content (Markdown Format)" wire:model="content" placeholder="Write terms or policies using markdown styling..." rows="16" required />

            <div class="divider">SEO Metadata</div>
            <x-input label="Meta Title" wire:model="meta_title" />
            <x-textarea label="Meta Description" wire:model="meta_description" rows="2" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>