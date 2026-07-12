<?php

namespace App\Livewire\Admin\PageSettings;

use Mary\Traits\Toast;
use App\Models\PageSetting;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use Toast;

    public function mount()
    {
        Gate::authorize('manage-content');
    }

    public string $search = '';
    public array $sortBy = ['column' => 'page_name', 'direction' => 'asc'];

    // Form fields
    public ?PageSetting $pageSetting = null;
    public string $page_key = '';
    public string $page_name = '';
    public string $meta_title = '';
    public string $meta_description = '';
    public string $meta_keywords = '';
    public string $canonical_url = '';
    public string $json_schema = '';

    public bool $drawer = false;

    public function getAutomaticCanonicalProperty(): string
    {
        if (!$this->pageSetting) {
            return '';
        }
        $key = $this->pageSetting->page_key;
        $path = match($key) {
            'home' => '/',
            'about' => '/about',
            'book_test' => '/book-a-test',
            'exchange' => '/exchange',
            'blogs_index' => '/blogs',
            default => '/' . str_replace('tech_', '', $key)
        };
        return url($path);
    }

    public function showEdit(PageSetting $pageSetting): void
    {
        $this->resetValidation();
        $this->pageSetting = $pageSetting;
        $this->page_key = $pageSetting->page_key;
        $this->page_name = $pageSetting->page_name;
        $this->meta_title = $pageSetting->meta_title ?? '';
        $this->meta_description = $pageSetting->meta_description ?? '';
        $this->meta_keywords = $pageSetting->meta_keywords ?? '';
        $this->canonical_url = $pageSetting->canonical_url ?? '';
        $this->json_schema = $pageSetting->json_schema ? json_encode($pageSetting->json_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '';
        $this->drawer = true;
    }

    public function save(): void
    {
        $rules = [
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
            'json_schema' => 'nullable|json',
        ];

        $this->validate($rules);

        $decodedSchema = $this->json_schema ? json_decode($this->json_schema, true) : null;

        $data = [
            'meta_title' => $this->meta_title ?: null,
            'meta_description' => $this->meta_description ?: null,
            'meta_keywords' => $this->meta_keywords ?: null,
            'canonical_url' => $this->canonical_url ?: null,
            'json_schema' => $decodedSchema,
        ];

        if ($this->pageSetting) {
            $this->pageSetting->update($data);
            $this->success('Page SEO settings updated successfully.', position: 'toast-bottom');
        }

        $this->drawer = false;
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'page_name', 'label' => 'Page Name', 'sortable' => true],
            ['key' => 'page_key', 'label' => 'Route Key', 'sortable' => true],
            ['key' => 'meta_title', 'label' => 'Meta Title', 'sortable' => false],
            ['key' => 'updated_at', 'label' => 'Last Updated', 'sortable' => true],
        ];
    }

    public function pageSettings()
    {
        return PageSetting::query()
            ->when($this->search, function ($query) {
                $query->where('page_name', 'like', '%' . $this->search . '%')
                    ->orWhere('page_key', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->get();
    }

    public function with(): array
    {
        return [
            'rows' => $this->pageSettings(),
            'headers' => $this->headers(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.page-settings.index', $this->with())->layout('layouts.app');
    }
}
