<?php

use App\Models\BlogCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithFileUploads, WithPagination;

    public function mount()
    {
        Gate::authorize('manage-blogs');
    }

    public string $search = '';
    public array $sortBy = ['column' => 'title', 'direction' => 'asc'];

    // Form fields
    public ?BlogCategory $category = null;
    public string $title = '';
    public string $slug = '';
    public $image = null;
    public ?string $existing_image = null;
    public string $short_description = '';
    public string $meta_title = '';
    public string $meta_description = '';
    public string $json_schema = '';

    public bool $drawer = false;

    public function updatedTitle($value): void
    {
        $this->slug = str($value)->slug();
    }

    public function showCreate(): void
    {
        $this->resetValidation();
        $this->category = null;
        $this->reset(['title', 'slug', 'image', 'existing_image', 'short_description', 'meta_title', 'meta_description', 'json_schema']);
        $this->drawer = true;
    }

    public function showEdit(BlogCategory $category): void
    {
        $this->resetValidation();
        $this->category = $category;
        $this->title = $category->title;
        $this->slug = $category->slug;
        $this->image = null;
        $this->existing_image = $category->image;
        $this->short_description = $category->short_description ?? '';
        $this->meta_title = $category->meta_title ?? '';
        $this->meta_description = $category->meta_description ?? '';
        $this->json_schema = $category->json_schema ? json_encode($category->json_schema, JSON_PRETTY_PRINT) : '';
        $this->drawer = true;
    }

    public function save(): void
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_categories,slug,' . ($this->category?->id ?? 'NULL'),
            'image' => 'nullable|image|max:2048',
            'short_description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'json_schema' => 'nullable|json',
        ];

        $this->validate($rules);

        $imagePath = $this->existing_image;
        if ($this->image) {
            $imagePath = $this->image->store('categories', 'public');
        }

        $decodedSchema = $this->json_schema ? json_decode($this->json_schema, true) : null;

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'image' => $imagePath,
            'short_description' => $this->short_description,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'json_schema' => $decodedSchema,
        ];

        if ($this->category) {
            $this->category->update($data);
            $this->success('Category updated successfully.', position: 'toast-bottom');
        } else {
            BlogCategory::create($data);
            $this->success('Category created successfully.', position: 'toast-bottom');
        }

        $this->drawer = false;
    }

    public function delete(BlogCategory $category): void
    {
        $category->delete();
        $this->success('Category deleted successfully.', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'image', 'label' => 'Image', 'class' => 'w-20', 'sortable' => false],
            ['key' => 'title', 'label' => 'Title', 'sortable' => true],
            ['key' => 'slug', 'label' => 'Slug', 'sortable' => true],
            ['key' => 'short_description', 'label' => 'Description', 'sortable' => false],
        ];
    }

    public function categories()
    {
        return BlogCategory::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(10);
    }

    public function with(): array
    {
        return [
            'rows' => $this->categories(),
            'headers' => $this->headers(),
        ];
    }
}; ?>

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
