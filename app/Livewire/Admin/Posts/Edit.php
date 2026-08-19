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

    public int $blog_category_id;
    public ?int $author_id = null;
    public ?int $reviewer_id = null;
    public string $title = '';
    public string $slug = '';
    public ?string $summary = null;
    public ?string $featured_image = null;
    public string $content = '';
    public string $author_name = '';
    public string $reviewer_name = '';
    public ?string $meta_title = null;
    public ?string $meta_description = null;
    public ?string $json_schema = null;
    public ?string $meta_keywords = null;
    public ?string $canonical_url = null;
    public array $key_takeaways = [];
    public string $editor_type = 'markdown';
    public string $markdown_content = '';

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
            $this->author_id = $post->author_id;
            $this->reviewer_id = $post->reviewer_id;
            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->summary = $post->summary ?? '';
            $this->featured_image = $post->featured_image;
            
            $this->key_takeaways = $post->key_takeaways ?? [];
            $this->editor_type = $post->editor_type ?: 'html';

            // Render content cleanly into HTML for TinyMCE editor
            if (is_array($post->content)) {
                if ($this->editor_type === 'markdown' || (isset($post->content[0]['type']) && $post->content[0]['type'] === 'markdown')) {
                    $this->editor_type = 'markdown';
                    $this->markdown_content = $post->content[0]['data']['text'] ?? '';
                    $this->content = '';
                } else {
                    $html = '';
                    foreach ($post->content as $block) {
                        $type = $block['type'] ?? '';
                        $data = $block['data'] ?? [];

                        if ($type === 'paragraph') {
                            $html .= '<p>' . ($data['text'] ?? '') . '</p>';
                        } elseif ($type === 'heading') {
                            $level = $data['level'] ?? 2;
                            $text = $data['text'] ?? '';
                            $html .= "<h{$level}>{$text}</h{$level}>";
                        } elseif ($type === 'takeaways') {
                            if (empty($this->key_takeaways)) {
                                $this->key_takeaways = $data['items'] ?? [];
                            }
                        } elseif ($type === 'list') {
                            $items = $data['items'] ?? [];
                            $html .= '<ul>';
                            foreach ($items as $item) {
                                $html .= '<li>' . $item . '</li>';
                            }
                            $html .= '</ul>';
                        } elseif ($type === 'table') {
                            $headers = $data['headers'] ?? [];
                            $rows = $data['rows'] ?? [];
                            $html .= '<table><thead><tr>';
                            foreach ($headers as $th) {
                                $html .= '<th>' . e($th) . '</th>';
                            }
                            $html .= '</tr></thead><tbody>';
                            foreach ($rows as $row) {
                                $html .= '<tr>';
                                foreach ($row as $td) {
                                    $html .= '<td>' . $td . '</td>';
                                }
                                $html .= '</tr>';
                            }
                            $html .= '</tbody></table>';
                        }
                    }
                    $this->content = $html;
                }
            } else {
                $this->content = $post->content ?? '';
                $this->editor_type = 'html';
            }
            $this->author_name = $post->author_name;
            $this->reviewer_name = $post->reviewer_name ?? '';
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
            $authors = \App\Models\TeamMember::all();
            if ($authors->count() > 0) {
                $this->author_id = $authors->first()->id;
                $this->author_name = $authors->first()->name;
                $reviewer = $authors->where('category', 'ent_specialist')->first() ?: $authors->skip(1)->first();
                if ($reviewer) {
                    $this->reviewer_id = $reviewer->id;
                    $this->reviewer_name = $reviewer->name;
                }
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

    public function updatedAuthorId($value): void
    {
        $author = \App\Models\TeamMember::find($value);
        if ($author) {
            $this->author_name = $author->name;
        }
    }

    public function updatedReviewerId($value): void
    {
        $reviewer = \App\Models\TeamMember::find($value);
        if ($reviewer) {
            $this->reviewer_name = $reviewer->name;
        }
    }

    public function save(): void
    {
        try {
            // Resolve author_name and reviewer_name dynamically
            if ($this->author_id) {
                $author = \App\Models\TeamMember::find($this->author_id);
                if ($author) {
                    $this->author_name = $author->name;
                }
            }
            if ($this->reviewer_id) {
                $reviewer = \App\Models\TeamMember::find($this->reviewer_id);
                if ($reviewer) {
                    $this->reviewer_name = $reviewer->name;
                }
            } else {
                $this->reviewer_name = null;
            }

            // Sanitize optional SEO fields — bad values are cleared, not rejected.
            // This prevents the json_schema and canonical_url validation errors
            // from blocking the entire save (15+ prod log hits confirmed).
            if ($this->json_schema) {
                $decoded = json_decode($this->json_schema, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->json_schema = null;
                    $this->warning('JSON Schema was invalid and has been cleared. Post saved without it.', position: 'toast-bottom');
                }
            } else {
                $this->json_schema = null;
            }

            if ($this->canonical_url) {
                $url = trim($this->canonical_url);
                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    $this->canonical_url = null;
                    $this->warning('Canonical URL was invalid and has been cleared. Post saved without it.', position: 'toast-bottom');
                }
            } else {
                $this->canonical_url = null;
            }

            $rules = [
                'blog_category_id' => 'required|exists:blog_categories,id',
                'author_id'        => 'required|exists:team_members,id',
                'reviewer_id'      => 'nullable|exists:team_members,id',
                'title'            => 'required|string|max:255',
                'slug'             => 'required|string|max:255|unique:blog_posts,slug,' . ($this->post?->id ?? 'NULL'),
                'summary'          => 'nullable|string',
                'featured_image'   => 'nullable|string|max:255',
                'content'          => $this->editor_type === 'html' ? 'required|string' : 'nullable|string',
                'markdown_content' => $this->editor_type === 'markdown' ? 'required|string' : 'nullable|string',
                'author_name'      => 'required|string|max:255',
                'reviewer_name'    => 'nullable|string|max:255',
                'meta_title'       => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'json_schema'      => 'nullable|json',
                'meta_keywords'    => 'nullable|string',
                'canonical_url'    => 'nullable|url|max:255',
                'key_takeaways'    => 'nullable|array',
                'key_takeaways.*'  => 'nullable|string',
            ];

            $this->validate($rules);

            $featuredImageMediaId = null;
            if ($this->featured_image) {
                $media = \App\Models\Media::where('filepath', $this->featured_image)->first();
                if ($media) {
                    $featuredImageMediaId = $media->id;
                }
            }

            $blocks = [];

            if ($this->editor_type === 'markdown') {
                $blocks = [
                    [
                        'type' => 'markdown',
                        'data' => [
                            'text' => $this->markdown_content
                        ]
                    ]
                ];
            } else {
                // Parse HTML content string from TinyMCE into clean JSON block structure
                $rawHtml = $this->content;

                if (!empty(trim($rawHtml))) {
                    // ── Pre-processing: strip Word/Libre Office CSS artifacts ──────────────
                    // Word HTML paste includes raw @font-face / MsoNormal style blocks as
                    // text. Strip them before DOMDocument sees the markup.
                    $rawHtml = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $rawHtml);
                    // Strip inline Word class attributes that pollute the block detection
                    $rawHtml = preg_replace('/\s*class="[^"]*Mso[^"]*"/i', '', $rawHtml);
                    // Strip any leading CSS-looking text (SourceURL, @font-face etc.)
                    // that Word injects as a plain-text prefix before the actual HTML
                    $rawHtml = preg_replace('/^(\s*(SourceURL:[^\n]*|@[a-z\-]+\{[^}]*\}|[a-z.#][^{]*\{[^}]*\}|\s*))+/si', '', $rawHtml);
                    $rawHtml = trim($rawHtml);

                    // Use a unique wrapper ID so getElementsByTagName('div')->item(0) can
                    // never accidentally resolve to a *child* div (e.g. .takeaways block)
                    $wrapperId = 'tinymce_root_' . uniqid();

                    $dom = new \DOMDocument();
                    libxml_use_internal_errors(true);
                    $dom->loadHTML(
                        '<?xml encoding="utf-8" ?><div id="' . $wrapperId . '">' . $rawHtml . '</div>',
                        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
                    );
                    libxml_clear_errors();

                    /** @var \DOMElement|null $container */
                    $container = $dom->getElementById($wrapperId);
                    if ($container && $container->hasChildNodes()) {
                        foreach ($container->childNodes as $node) {
                            if (!$node instanceof \DOMElement) {
                                continue;
                            }

                            $tagName = strtolower($node->tagName);

                            if (in_array($tagName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
                                $level = (int) substr($tagName, 1);
                                $blocks[] = [
                                    'type' => 'heading',
                                    'data' => [
                                        'level' => $level,
                                        'text' => trim($node->ownerDocument->saveHTML($node)) ? trim(strip_tags($node->textContent)) : ''
                                    ]
                                ];
                            } elseif ($tagName === 'div' && str_contains($node->getAttribute('class'), 'takeaways')) {
                                /** @var \DOMElement|null $titleNode */
                                $titleNode = $node->getElementsByTagName('h2')->item(0);
                                $titleText = $titleNode ? trim($titleNode->textContent) : 'Key takeaways';
                                $listItems = [];
                                foreach ($node->getElementsByTagName('li') as $li) {
                                    $innerHtml = '';
                                    foreach ($li->childNodes as $child) {
                                        $innerHtml .= $child->ownerDocument->saveHTML($child);
                                    }
                                    $listItems[] = trim($innerHtml);
                                }
                                $blocks[] = [
                                    'type' => 'takeaways',
                                    'data' => [
                                        'title' => $titleText,
                                        'items' => $listItems
                                    ]
                                ];
                            } elseif ($tagName === 'ul' || $tagName === 'ol') {
                                $listItems = [];
                                foreach ($node->getElementsByTagName('li') as $li) {
                                    $innerHtml = '';
                                    foreach ($li->childNodes as $child) {
                                        $innerHtml .= $child->ownerDocument->saveHTML($child);
                                    }
                                    $listItems[] = trim($innerHtml);
                                }
                                $blocks[] = [
                                    'type' => 'list',
                                    'data' => [
                                        'items' => $listItems
                                    ]
                                ];
                            } elseif ($tagName === 'table') {
                                $headers = [];
                                foreach ($node->getElementsByTagName('th') as $th) {
                                    $headers[] = trim($th->textContent);
                                }
                                $rows = [];
                                foreach ($node->getElementsByTagName('tr') as $tr) {
                                    /** @var \DOMElement $tr */
                                    $row = [];
                                    foreach ($tr->getElementsByTagName('td') as $td) {
                                        $innerHtml = '';
                                        foreach ($td->childNodes as $child) {
                                            $innerHtml .= $child->ownerDocument->saveHTML($child);
                                        }
                                        $row[] = trim($innerHtml);
                                    }
                                    if (!empty($row)) {
                                        $rows[] = $row;
                                    }
                                }
                                $blocks[] = [
                                    'type' => 'table',
                                    'data' => [
                                        'headers' => $headers,
                                        'rows' => $rows
                                    ]
                                ];
                            } else {
                                // Paragraph or fallback element
                                $innerHtml = '';
                                foreach ($node->childNodes as $child) {
                                    $innerHtml .= $child->ownerDocument->saveHTML($child);
                                }
                                $trimmed = trim($innerHtml);
                                // Strip blocks that are empty or contain only whitespace/nbsp after decoding
                                $decoded = trim(html_entity_decode($trimmed, ENT_HTML5 | ENT_QUOTES, 'UTF-8'));
                                $decoded = preg_replace('/[\x{00A0}\x{200B}\x{FEFF}]/u', '', $decoded); // strip nbsp, zero-width, BOM
                                if ($trimmed !== '' && trim(strip_tags($decoded)) !== '') {
                                    $blocks[] = [
                                        'type' => 'paragraph',
                                        'data' => [
                                            'text' => $trimmed
                                        ]
                                    ];
                                }
                            }
                        }
                    }
                }

                if (empty($blocks) && !empty(trim($rawHtml))) {
                    $blocks = [
                        ['type' => 'paragraph', 'data' => ['text' => trim($rawHtml)]]
                    ];
                }
            }

            $decodedSchema = $this->json_schema ? json_decode($this->json_schema, true) : null;
            
            $finalTakeaways = array_values(array_filter($this->key_takeaways, fn($val) => !empty(trim($val))));

            $data = [
                'blog_category_id' => $this->blog_category_id,
                'author_id' => $this->author_id,
                'reviewer_id' => $this->reviewer_id,
                'title' => $this->title,
                'slug' => $this->slug,
                'summary' => $this->summary,
                'featured_image' => $this->featured_image,
                'featured_image_media_id' => $featuredImageMediaId,
                'content' => $blocks,
                'key_takeaways' => count($finalTakeaways) > 0 ? $finalTakeaways : null,
                'editor_type' => $this->editor_type,
                'author_name' => $this->author_name,
                'reviewer_name' => $this->reviewer_name,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'json_schema' => $decodedSchema,
                'meta_keywords' => $this->meta_keywords ?: null,
                'canonical_url' => $this->canonical_url ?: null,
            ];

            if ($this->post && $this->post->exists) {
                $this->post->update($data);
                $this->success('Post updated successfully.', position: 'toast-bottom');
                $this->redirect(route('admin.posts'), navigate: false);
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
                $this->redirect(route('admin.posts'), navigate: false);
            }
        } catch (\Illuminate\Validation\ValidationException $ve) {
            \Illuminate\Support\Facades\Log::warning('Blog Post save validation error', ['errors' => $ve->errors()]);
            $this->error('Please fix the validation errors shown at the top of the form.');
            throw $ve;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Blog Post save failed', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->error('Failed to save article: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.posts.edit', [
            'categories' => BlogCategory::all(),
            'authors' => \App\Models\TeamMember::select('id', 'name', 'role')->get(),
        ])->layout('layouts.app');
    }
}
