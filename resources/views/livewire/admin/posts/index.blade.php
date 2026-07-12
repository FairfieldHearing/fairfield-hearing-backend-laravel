<div>
    <!-- HEADER -->
    <x-header title="Blog Posts" subtitle="Write and manage articles" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="New Post" link="{{ route('admin.posts.create') }}" class="btn-primary" icon="o-plus" no-wire-navigate />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$rows" :sort-by="$sortBy">
            @scope('cell_featured_image', $post)
                @if($post->featured_image)
                    <img src="{{ Storage::url($post->featured_image) }}" class="w-12 h-12 object-cover rounded-md" />
                @else
                    <div class="w-12 h-12 bg-base-300 rounded-md flex items-center justify-center text-xs text-base-content/50">No Image</div>
                @endif
            @endscope

            @scope('cell_created_at', $post)
                {{ $post->created_at->format('M d, Y') }}
            @endscope

            @scope('actions', $post)
            <div class="flex gap-2">
                <x-button icon="o-pencil" link="{{ route('admin.posts.edit', $post->id) }}" class="btn-ghost btn-sm text-primary" no-wire-navigate />
                <x-button icon="o-trash" wire:click="delete({{ $post->id }})" wire:confirm="Are you sure you want to delete this article?" class="btn-ghost btn-sm text-error" />
            </div>
            @endscope
        </x-table>
    </x-card>
</div>