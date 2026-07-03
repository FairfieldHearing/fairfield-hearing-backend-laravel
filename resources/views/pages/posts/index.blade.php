<?php

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\Faq;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
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
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Blog Posts" subtitle="Write and manage articles" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="New Post" wire:click="showCreate" class="btn-primary" icon="o-plus" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$rows" :sort-by="$sortBy">
            @scope('cell_featured_image', $post)
                @if($post->featured_image)
                    <img src="{{ Storage::url($post->featured_image) }}" class="w-12 h-12 object-cover rounded-md" />
                @else
                    <div class="w-12 h-12 bg-base-300 rounded-md flex items-center justify-center text-xs text-base-content/50">No Image</div>
                @endif
            @endscope

            @scope('cell_created_at', $post)
                {{ $post->created_at->format('M d, Y') }}
            @endscope

            @scope('actions', $post)
            <div class="flex gap-2">
                <x-button icon="o-pencil" wire:click="showEdit({{ $post->id }})" class="btn-ghost btn-sm text-primary" />
                <x-button icon="o-trash" wire:click="delete({{ $post->id }})" wire:confirm="Are you sure you want to delete this article?" class="btn-ghost btn-sm text-error" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FORM DRAWER -->
    <x-drawer wire:model="drawer" title="{{ $post ? 'Edit Post' : 'Create Post' }}" right separator with-close-button class="lg:w-1/2">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-4">
                <x-select label="Category" wire:model="blog_category_id" :options="$categories" option-value="id" option-label="title" required />
                <x-input label="Author Name" wire:model="author_name" required />
            </div>

            <x-input label="Title" wire:model.live.debounce.500ms="title" required />
            <x-input label="Slug" wire:model="slug" required />
            
            <x-file label="Featured Image" wire:model="featured_image" accept="image/*">
                @if($existing_featured_image && !$featured_image)
                    <img src="{{ Storage::url($existing_featured_image) }}" class="h-20 object-cover rounded-md mt-2" />
                @endif
            </x-file>

            <x-textarea label="Summary" wire:model="summary" rows="2" />
            <x-textarea label="Content (Supports Markdown)" wire:model="content" rows="12" required />

            <!-- Post FAQ Section -->
            <div class="divider">Post Specific FAQs</div>
            <div class="space-y-4 bg-base-200 p-4 rounded-lg">
                <div class="space-y-2">
                    @foreach($linkedFaqs as $index => $lf)
                        <div class="flex items-start justify-between bg-base-100 p-2.5 rounded border border-base-300 text-sm">
                            <div>
                                <p class="font-bold text-xs">{{ $lf['question'] }}</p>
                                <p class="text-xs text-base-content/70 mt-1">{{ $lf['answer'] }}</p>
                            </div>
                            <x-button icon="o-trash" wire:click="removeFaq({{ $index }}, {{ $lf['id'] ?? null }})" class="btn-ghost btn-xs text-error" />
                        </div>
                    @endforeach
                </div>

                <div class="space-y-2 border-t border-base-300 pt-3">
                    <x-input label="New FAQ Question" wire:model="newFaqQuestion" placeholder="E.g. Is this trial option free?" />
                    <x-textarea label="New FAQ Answer" wire:model="newFaqAnswer" placeholder="FAQ Answer..." rows="2" />
                    <x-button label="Add FAQ to Article" wire:click="addFaq" class="btn-xs btn-outline btn-secondary" />
                </div>
            </div>

            <div class="divider">SEO Metadata</div>
            <x-input label="Meta Title" wire:model="meta_title" />
            <x-textarea label="Meta Description" wire:model="meta_description" rows="2" />
            <x-textarea label="JSON Schema (JSON-LD)" wire:model="json_schema" placeholder='{ "@context": "https://schema.org", ... }' rows="5" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
