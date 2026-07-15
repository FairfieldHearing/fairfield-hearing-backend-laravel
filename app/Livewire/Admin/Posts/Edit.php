<?php

namespace App\Livewire\Admin\Posts;

use App\Helpers\ImageHelper;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Faq;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Edit extends Component
{
    use Toast, WithFileUploads;

    public ?BlogPost $post = null;

    // Form fields
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
    public string $meta_keywords = '';
    public string $canonical_url = '';

    // FAQs inline management
    public array $linkedFaqs = [];
    public string $newFaqQuestion = '';
    public string $newFaqAnswer = '';

    public function mount(?BlogPost $post = null)
    {
        Gate::authorize('manage-blogs');

        if ($post && $post->exists) {
            $this->post = $post;
            $this->blog_category_id = $post->blog_category_id;
            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->summary = $post->summary ?? '';
            $this->existing_featured_image = $post->featured_image;
            $this->content = $post->content;
            $this->author_name = $post->author_name;
            $this->meta_title = $post->meta_title ?? '';
            $this->meta_description = $post->meta_description ?? '';
            $this->json_schema = $post->json_schema ? json_encode($post->json_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '';
            $this->meta_keywords = $post->meta_keywords ?? '';
            $this->canonical_url = $post->canonical_url ?? '';

            $this->linkedFaqs = Faq::where('blog_post_id', $post->id)->where('type', 'blog_post')->get()->toArray();
        } else {
            $firstCategory = BlogCategory::first();
            if ($firstCategory) {
                $this->blog_category_id = $firstCategory->id;
            }
        }
    }

    public function updatedTitle($value): void
    {
        $this->slug = str($value)->slug();
    }

    public function getAutomaticCanonicalProperty(): string
    {
        $category = BlogCategory::find($this->blog_category_id);
        $categorySlug = $category?->slug ?? 'general';
        return url("/blogs/{$categorySlug}/{$this->slug}");
    }

    public function addFaq(): void
    {
        $this->validate([
            'newFaqQuestion' => 'required|string|max:255',
            'newFaqAnswer' => 'required|string',
        ]);

        if ($this->post && $this->post->exists) {
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
        $this->success('FAQ added to list.', position: 'toast-bottom');
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
            'meta_keywords' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
        ];

        $this->validate($rules);

        $imagePath = $this->existing_featured_image;
        if ($this->featured_image) {
            ImageHelper::compressAndResize($this->featured_image);
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
            'meta_keywords' => $this->meta_keywords ?: null,
            'canonical_url' => $this->canonical_url ?: null,
        ];

        if ($this->post && $this->post->exists) {
            $this->post->update($data);
            $this->success('Post updated successfully.', position: 'toast-bottom', redirectTo: route('admin.posts'));
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
            $this->success('Post created successfully.', position: 'toast-bottom', redirectTo: route('admin.posts'));
        }
    }

    public function render()
    {
        return view('livewire.admin.posts.edit', [
            'categories' => BlogCategory::all()
        ])->layout('layouts.app');
    }
}
