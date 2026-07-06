<div>
    <!-- HEADER -->
    <x-header title="Blog Categories" subtitle="Manage categories for your blog posts" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Category" wire:click="showCreate" class="btn-primary" icon="o-plus" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$rows" :sort-by="$sortBy" with-pagination>
            @scope('cell_image', $cat)
                @if($cat->image)
                    <img src="{{ Storage::url($cat->image) }}" class="w-12 h-12 object-cover rounded-md" />
                @else
                    <div class="w-12 h-12 bg-base-300 rounded-md flex items-center justify-center text-xs text-base-content/50">No Image</div>
                @endif
            @endscope

            @scope('actions', $cat)
            <div class="flex gap-2">
                <x-button icon="o-pencil" wire:click="showEdit({{ $cat->id }})" class="btn-ghost btn-sm text-primary" />
                <x-button icon="o-trash" wire:click="delete({{ $cat->id }})" wire:confirm="Are you sure you want to delete this category?" class="btn-ghost btn-sm text-error" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FORM DRAWER -->
    <x-drawer wire:model="drawer" title="{{ $category ? 'Edit Category' : 'Create Category' }}" right separator with-close-button class="lg:w-1/3">
        <x-form wire:submit="save">
            <x-input label="Title" wire:model.live.debounce.500ms="title" required />
            <x-input label="Slug" wire:model="slug" required />
            
            <x-file label="Category Image" wire:model="image" accept="image/*">
                @if($existing_image && !$image)
                    <img src="{{ Storage::url($existing_image) }}" class="h-20 object-cover rounded-md mt-2" />
                @endif
            </x-file>

            <x-textarea label="Short Description" wire:model="short_description" rows="3" />

            <div class="divider">SEO Metadata</div>
            <x-input label="Meta Title" wire:model="meta_title" />
            <x-textarea label="Meta Description" wire:model="meta_description" rows="2" />
            <x-textarea label="JSON Schema (JSON-LD)" wire:model="json_schema" placeholder='{ "@context": "https://schema.org", ... }' rows="5" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>