<?php

namespace App\Livewire\Admin\Posts;

use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Gate;
use App\Models\Faq;
use Livewire\Component;

class Index extends Component
{
use Toast, WithFileUploads;

    public function mount()
    {
        Gate::authorize('manage-blogs');
    }

    public string $search = '';
    public array $sortBy = ['column' => 'title', 'direction' => 'asc'];

    // Form fields
    public ?BlogPost $post = null;
    public int $blog_category_id;
    public string $title = '';
    public string $slug = '';
    public string $summary = '';
    public $featured_image;
    public ?string $existing_featured_image = null;
    public string $content = '';
    public string $author_name = '';
    public string $meta_title = '';
    public string $meta_description = '';
    public string $json_schema = '';

    // FAQs inline management
    public array $linkedFaqs = [];
    public string $newFaqQuestion = '';
    public string $newFaqAnswer = '';

    public bool $drawer = false;

    public function updatedTitle($value): void
    {
        $this->slug = str($value)->slug();
    }

    public function showCreate(): void
    {
        $this->resetValidation();
        $this->post = null;
        $this->reset([
            'title', 'slug', 'summary', 'featured_image', 'existing_featured_image', 'content', 'author_name',
            'meta_title', 'meta_description', 'json_schema', 'linkedFaqs', 'newFaqQuestion', 'newFaqAnswer'
        ]);
        // Set default category if any exists
        $firstCategory = BlogCategory::first();
        if ($firstCategory) {
            $this->blog_category_id = $firstCategory->id;
        }
        $this->drawer = true;
    }

    public function showEdit(BlogPost $post): void
    {
        $this->resetValidation();
        $this->post = $post;
        $this->blog_category_id = $post->blog_category_id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->summary = $post->summary ?? '';
        $this->featured_image = null;
        $this->existing_featured_image = $post->featured_image;
        $this->content = $post->content;
        $this->author_name = $post->author_name;
        $this->meta_title = $post->meta_title ?? '';
        $this->meta_description = $post->meta_description ?? '';
        $this->json_schema = $post->json_schema ? json_encode($post->json_schema, JSON_PRETTY_PRINT) : '';
        
        $this->linkedFaqs = Faq::where('blog_post_id', $post->id)->where('type', 'blog_post')->get()->toArray();
        $this->reset(['newFaqQuestion', 'newFaqAnswer']);
        $this->drawer = true;
    }

    public function addFaq(): void
    {
        $this->validate([
            'newFaqQuestion' => 'required|string|max:255',
            'newFaqAnswer' => 'required|string',
        ]);

        if ($this->post) {
            $faq = Faq::create([
                'blog_post_id' => $this->post->id,
                'question' => $this->newFaqQuestion,
                'answer' => $this->newFaqAnswer,
                'type' => 'blog_post'
            ]);
            $this->linkedFaqs = Faq::where('blog_post_id', $this->post->id)->where('type', 'blog_post')->get()->toArray();
        } else {
            $this->linkedFaqs[] = [
                'question' => $this->newFaqQuestion,
                'answer' => $this->newFaqAnswer,
                'type' => 'blog_post'
            ];
        }

        $this->reset(['newFaqQuestion', 'newFaqAnswer']);
        $this->success('FAQ added to post list.', position: 'toast-bottom');
    }

    public function removeFaq($index, $faqId = null): void
    {
        if ($faqId) {
            Faq::destroy($faqId);
        }
        unset($this->linkedFaqs[$index]);
        $this->linkedFaqs = array_values($this->linkedFaqs);
        $this->success('FAQ removed.', position: 'toast-bottom');
    }

    public function save(): void
    {
        $rules = [
            'blog_category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug,' . ($this->post?->id ?? 'NULL'),
            'summary' => 'nullable|string',
            'featured_image' => 'nullable|image|max:4096',
            'content' => 'required|string',
            'author_name' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'json_schema' => 'nullable|json',
        ];

        $this->validate($rules);

        $imagePath = $this->existing_featured_image;
        if ($this->featured_image) {
            $imagePath = $this->featured_image->store('blog_posts', 'public');
        }

        $decodedSchema = $this->json_schema ? json_decode($this->json_schema, true) : null;

        $data = [
            'blog_category_id' => $this->blog_category_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'featured_image' => $imagePath,
            'content' => $this->content,
            'author_name' => $this->author_name,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'json_schema' => $decodedSchema,
        ];

        if ($this->post) {
            $this->post->update($data);
            $this->success('Post updated successfully.', position: 'toast-bottom');
        } else {
            $createdPost = BlogPost::create($data);
            // Save temporary FAQs
            foreach ($this->linkedFaqs as $faqData) {
                Faq::create([
                    'blog_post_id' => $createdPost->id,
                    'question' => $faqData['question'],
                    'answer' => $faqData['answer'],
                    'type' => 'blog_post'
                ]);
            }
            $this->success('Post created successfully.', position: 'toast-bottom');
        }

        $this->drawer = false;
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
            'categories' => BlogCategory::all(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.posts.index', $this->with())->layout('layouts.app');
    }

}
