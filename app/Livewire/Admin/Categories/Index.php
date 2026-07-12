<?php

namespace App\Livewire\Admin\Categories;

use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use Livewire\Component;
use App\Models\BlogCategory;
use Illuminate\Support\Facades\Gate;
use Livewire\WithPagination;

class Index extends Component
{
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
    public string $meta_keywords = '';
    public string $canonical_url = '';

    public bool $drawer = false;

    public function getAutomaticCanonicalProperty(): string
    {
        if (!$this->category) {
            return '';
        }
        return url("/blogs/{$this->slug}");
    }

    public function updatedTitle($value): void
    {
        $this->slug = str($value)->slug();
    }

    public function showCreate(): void
    {
        $this->resetValidation();
        $this->category = null;
        $this->reset(['title', 'slug', 'image', 'existing_image', 'short_description', 'meta_title', 'meta_description', 'json_schema', 'meta_keywords', 'canonical_url']);
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
        $this->meta_keywords = $category->meta_keywords ?? '';
        $this->canonical_url = $category->canonical_url ?? '';
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
            'meta_keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
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
            'meta_keywords' => $this->meta_keywords ?: null,
            'canonical_url' => $this->canonical_url ?: null,
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

    public function render()
    {
        return view('livewire.admin.categories.index', $this->with())->layout('layouts.app');
    }

}
