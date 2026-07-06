<?php

namespace App\Livewire\Admin\Faqs;

use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use App\Models\Faq;

class Index extends Component
{
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

    public function render()
    {
        return view('livewire.admin.faqs.index', $this->with())->layout('layouts.app');
    }

}
