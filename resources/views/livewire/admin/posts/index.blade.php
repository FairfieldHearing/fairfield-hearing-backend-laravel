<div>
    <!-- HEADER -->
    <x-header title="Blog Posts" subtitle="Write and manage articles" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="New Post" wire:click="showCreate" class="btn-primary" icon="o-plus" />
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
                <x-button icon="o-pencil" wire:click="showEdit({{ $post->id }})" class="btn-ghost btn-sm text-primary" />
                <x-button icon="o-trash" wire:click="delete({{ $post->id }})" wire:confirm="Are you sure you want to delete this article?" class="btn-ghost btn-sm text-error" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FORM DRAWER -->
    <x-drawer wire:model="drawer" title="{{ $post ? 'Edit Post' : 'Create Post' }}" right separator with-close-button class="lg:w-1/2">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-4">
                <x-select label="Category" wire:model="blog_category_id" :options="$categories" option-value="id" option-label="title" required />
                <x-input label="Author Name" wire:model="author_name" required />
            </div>

            <x-input label="Title" wire:model.live.debounce.500ms="title" required />
            <x-input label="Slug" wire:model="slug" required />
            
            <x-file label="Featured Image" wire:model="featured_image" accept="image/*">
                @if($existing_featured_image && !$featured_image)
                    <img src="{{ Storage::url($existing_featured_image) }}" class="h-20 object-cover rounded-md mt-2" />
                @endif
            </x-file>

            <x-textarea label="Summary" wire:model="summary" rows="2" />
            <x-textarea label="Content (Supports Markdown)" wire:model="content" rows="12" required />

            <!-- Post FAQ Section -->
            <div class="divider">Post Specific FAQs</div>
            <div class="space-y-4 bg-base-200 p-4 rounded-lg">
                <div class="space-y-2">
                    @foreach($linkedFaqs as $index => $lf)
                        <div class="flex items-start justify-between bg-base-100 p-2.5 rounded border border-base-300 text-sm">
                            <div>
                                <p class="font-bold text-xs">{{ $lf['question'] }}</p>
                                <p class="text-xs text-base-content/70 mt-1">{{ $lf['answer'] }}</p>
                            </div>
                            <x-button icon="o-trash" wire:click="removeFaq({{ $index }}, {{ $lf['id'] ?? null }})" class="btn-ghost btn-xs text-error" />
                        </div>
                    @endforeach
                </div>

                <div class="space-y-2 border-t border-base-300 pt-3">
                    <x-input label="New FAQ Question" wire:model="newFaqQuestion" placeholder="E.g. Is this trial option free?" />
                    <x-textarea label="New FAQ Answer" wire:model="newFaqAnswer" placeholder="FAQ Answer..." rows="2" />
                    <x-button label="Add FAQ to Article" wire:click="addFaq" class="btn-xs btn-outline btn-secondary" />
                </div>
            </div>

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