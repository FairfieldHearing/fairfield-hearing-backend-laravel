<?php

use App\Models\Faq;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public string $search = '';
    public array $sortBy = ['column' => 'sort_order', 'direction' => 'asc'];

    // Form fields
    public ?Faq $faq = null;
    public string $question = '';
    public string $answer = '';
    public ?string $json_schema = null;

    public bool $drawer = false;

    public function mount()
    {
        Gate::authorize('manage-content');
    }

    public function showCreate(): void
    {
        $this->resetValidation();
        $this->faq = null;
        $this->reset(['question', 'answer', 'json_schema']);
        $this->drawer = true;
    }

    public function showEdit(Faq $faq): void
    {
        $this->resetValidation();
        $this->faq = $faq;
        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->json_schema = $faq->json_schema ? json_encode($faq->json_schema, JSON_PRETTY_PRINT) : null;
        $this->drawer = true;
    }

    public function save(): void
    {
        $rules = [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'json_schema' => 'nullable|json',
        ];

        $this->validate($rules);

        $decodedSchema = $this->json_schema ? json_decode($this->json_schema, true) : null;

        $data = [
            'blog_post_id' => null,
            'question' => $this->question,
            'answer' => $this->answer,
            'type' => 'general',
            'json_schema' => $decodedSchema,
        ];

        if ($this->faq) {
            $this->faq->update($data);
            $this->success('General FAQ updated successfully.', position: 'toast-bottom');
        } else {
            // Put new FAQs at the end of the sort order
            $maxOrder = Faq::where('type', 'general')->max('sort_order') ?? 0;
            $data['sort_order'] = $maxOrder + 1;

            Faq::create($data);
            $this->success('General FAQ created successfully.', position: 'toast-bottom');
        }

        $this->drawer = false;
    }

    public function delete(Faq $faq): void
    {
        $faq->delete();
        $this->success('FAQ deleted successfully.', position: 'toast-bottom');
    }

    public function updateOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Faq::where('id', $id)->update(['sort_order' => $index]);
        }
        $this->success('FAQ display order updated.', position: 'toast-bottom');
    }

    public function faqs()
    {
        return Faq::query()
            ->where('type', 'general')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('question', 'like', '%' . $this->search . '%')
                        ->orWhere('answer', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->get();
    }

    public function with(): array
    {
        return [
            'rows' => $this->faqs(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="General FAQs" subtitle="Manage website-wide general questions (Drag & Drop to reorder)" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search FAQs..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add FAQ" wire:click="showCreate" class="btn-primary" icon="o-plus" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card shadow class="overflow-x-auto">
        <div x-data="{
            initSortable() {
                let el = document.getElementById('faqs-table-body');
                if (!el) return;
                Sortable.create(el, {
                    animation: 150,
                    handle: '.drag-handle',
                    onEnd: () => {
                        let ids = Array.from(el.querySelectorAll('tr')).map(tr => tr.getAttribute('data-id'));
                        $wire.updateOrder(ids);
                    }
                });
            }
        }" x-init="initSortable()">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th class="w-10"></th>
                        <th class="w-16">#</th>
                        <th>Question</th>
                        <th class="w-24 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="faqs-table-body">
                    @forelse($rows as $faq)
                        <tr data-id="{{ $faq->id }}" class="hover">
                            <td class="align-middle">
                                <span class="drag-handle cursor-grab active:cursor-grabbing text-base-content/40 hover:text-base-content">
                                    <x-icon name="o-bars-4" class="w-5 h-5" />
                                </span>
                            </td>
                            <td class="font-mono text-xs align-middle">#{{ $faq->id }}</td>
                            <td class="align-middle font-semibold">{{ $faq->question }}</td>
                            <td class="text-right align-middle">
                                <div class="flex justify-end gap-1">
                                    <x-button icon="o-pencil" wire:click="showEdit({{ $faq->id }})" class="btn-ghost btn-xs text-primary" />
                                    <x-button icon="o-trash" wire:click="delete({{ $faq->id }})" wire:confirm="Are you sure you want to delete this FAQ?" class="btn-ghost btn-xs text-error" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-base-content/50">No FAQs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- FORM DRAWER -->
    <x-drawer wire:model="drawer" title="{{ $faq ? 'Edit FAQ' : 'Add FAQ' }}" right separator with-close-button class="lg:w-1/3">
        <x-form wire:submit="save">
            <x-input label="Question" wire:model="question" required />
            <x-textarea label="Answer" wire:model="answer" required rows="5" />
            <x-textarea label="JSON Schema (Optional)" wire:model="json_schema" placeholder='{ "@context": "https://schema.org", ... }' rows="6" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
