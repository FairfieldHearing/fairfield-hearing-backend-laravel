<?php

namespace App\Livewire\Admin\Categories;

use App\Helpers\ImageHelper;
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
    public ?string $image = null;
    public ?string $short_description = null;
    public ?string $meta_title = null;
    public ?string $meta_description = null;
    public ?string $json_schema = null;
    public ?string $meta_keywords = null;
    public ?string $canonical_url = null;

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
        $this->reset(['title', 'slug', 'image', 'short_description', 'meta_title', 'meta_description', 'json_schema', 'meta_keywords', 'canonical_url']);
        $this->drawer = true;
    }

    public function showEdit(BlogCategory $category): void
    {
        $this->resetValidation();
        $this->category = $category;
        $this->title = $category->title;
        $this->slug = $category->slug;
        $this->image = $category->image;
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
        // Clean empty strings to null to pass validation
        $this->json_schema = $this->json_schema ?: null;
        $this->canonical_url = $this->canonical_url ?: null;

        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_categories,slug,' . ($this->category?->id ?? 'NULL'),
            'image' => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'json_schema' => 'nullable|json',
            'meta_keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
        ];

        $this->validate($rules);

        $imageMediaId = null;
        if ($this->image) {
            $media = \App\Models\Media::where('filepath', $this->image)->first();
            if ($media) {
                $imageMediaId = $media->id;
            }
        }

        $decodedSchema = $this->json_schema ? json_decode($this->json_schema, true) : null;

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'image' => $this->image,
            'image_media_id' => $imageMediaId,
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
