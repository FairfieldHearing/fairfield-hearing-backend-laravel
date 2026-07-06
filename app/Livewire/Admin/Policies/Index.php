<?php

namespace App\Livewire\Admin\Policies;

use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use App\Models\PolicyPage;
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
    public array $sortBy = ['column' => 'title', 'direction' => 'asc'];

    // Form fields
    public ?PolicyPage $policy = null;
    public string $title = '';
    public string $slug = '';
    public string $content = '';
    public string $meta_title = '';
    public string $meta_description = '';

    public bool $drawer = false;

    public function updatedTitle($value): void
    {
        $this->slug = str($value)->slug();
    }

    public function showCreate(): void
    {
        $this->resetValidation();
        $this->policy = null;
        $this->reset(['title', 'slug', 'content', 'meta_title', 'meta_description']);
        $this->drawer = true;
    }

    public function showEdit(PolicyPage $policy): void
    {
        $this->resetValidation();
        $this->policy = $policy;
        $this->title = $policy->title;
        $this->slug = $policy->slug;
        $this->content = $policy->content;
        $this->meta_title = $policy->meta_title ?? '';
        $this->meta_description = $policy->meta_description ?? '';
        $this->drawer = true;
    }

    public function save(): void
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:policy_pages,slug,' . ($this->policy?->id ?? 'NULL'),
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ];

        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
        ];

        if ($this->policy) {
            $this->policy->update($data);
            $this->success('Policy page updated successfully.', position: 'toast-bottom');
        } else {
            PolicyPage::create($data);
            $this->success('Policy page created successfully.', position: 'toast-bottom');
        }

        $this->drawer = false;
    }

    public function delete(PolicyPage $policy): void
    {
        $policy->delete();
        $this->success('Policy page deleted successfully.', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'title', 'label' => 'Title', 'sortable' => true],
            ['key' => 'slug', 'label' => 'Slug', 'sortable' => true],
            ['key' => 'updated_at', 'label' => 'Last Updated', 'sortable' => true],
        ];
    }

    public function policies()
    {
        return PolicyPage::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->get();
    }

    public function with(): array
    {
        return [
            'rows' => $this->policies(),
            'headers' => $this->headers(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.policies.index', $this->with())->layout('layouts.app');
    }

}
