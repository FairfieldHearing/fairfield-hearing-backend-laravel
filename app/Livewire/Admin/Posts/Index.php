<?php

namespace App\Livewire\Admin\Posts;

use Mary\Traits\Toast;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Index extends Component
{
    use Toast;

    public string $search = '';
    public array $sortBy = ['column' => 'title', 'direction' => 'asc'];

    public function mount()
    {
        Gate::authorize('manage-blogs');
    }

    public function delete(BlogPost $post): void
    {
        $post->delete();
        $this->success('Post deleted successfully.', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'featured_image', 'label' => 'Image', 'class' => 'w-12'],
            ['key' => 'title', 'label' => 'Title', 'sortable' => true],
            ['key' => 'author_name', 'label' => 'Author', 'sortable' => true],
            ['key' => 'category.title', 'label' => 'Category', 'sortable' => false],
            ['key' => 'created_at', 'label' => 'Created At', 'sortable' => true],
        ];
    }

    public function posts()
    {
        return BlogPost::query()
            ->with('category')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('author_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->get();
    }

    public function with(): array
    {
        return [
            'rows' => $this->posts(),
            'headers' => $this->headers(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.posts.index', $this->with())->layout('layouts.app');
    }
}
