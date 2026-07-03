<?php

use App\Models\PolicyPage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component {
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
}; ?>

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
