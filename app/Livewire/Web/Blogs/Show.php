<?php

namespace App\Livewire\Web\Blogs;

use App\Models\BlogPost;
use App\Models\Faq;
use Livewire\Component;
use App\Traits\HasSeo;

class Show extends Component
{
    use HasSeo;

    /** @var \App\Models\BlogPost */
    public $postModel;

    public function mount(string $category, string $slug): void
    {
        $this->postModel = BlogPost::with(['category', 'author', 'reviewer'])->where('slug', $slug)->firstOrFail();
    }

    public function getPostCoverImage(): string
    {
        return $this->postModel->featured_image_url;
    }

    public function getAuthorPhoto(string $author): string
    {
        if ($this->postModel->author && $this->postModel->author->photo) {
            return str_starts_with($this->postModel->author->photo, 'assets/') || str_starts_with($this->postModel->author->photo, '/assets/')
                ? '/' . ltrim($this->postModel->author->photo, '/')
                : \Illuminate\Support\Facades\Storage::url($this->postModel->author->photo);
        }

        if (str_contains($author, 'Wasiq')) {
            return "/assets/img/wasiq-ali-khan.jpg";
        }
        if (str_contains($author, 'Dr. Nayeem') || str_contains($author, 'Nayeem')) {
            return "/assets/img/dr-nayeem.jpg";
        }
        if (str_contains($author, 'Farhan')) {
            return "/assets/img/farhan-khan.jpg";
        }
        return "/img/logo.jpeg";
    }

    public function render()
    {
        $postData = $this->postModel->toArray();

        // Convert JSON array blocks or HTML content string to rendered HTML
        $renderedHtml = '';
        
        $hasNewTakeaways = !empty($this->postModel->key_takeaways);
        if ($hasNewTakeaways) {
            $renderedHtml .= '<div class="takeaways"><h2>Key takeaways</h2><ul>';
            foreach ($this->postModel->key_takeaways as $takeaway) {
                $renderedHtml .= '<li>' . strip_tags($takeaway, '<strong><b>') . '</li>';
            }
            $renderedHtml .= '</ul></div>';
        }

        $content = $this->postModel->content;

        // Loop un-wrapping in case stored content is double-encoded or stringified
        while (is_string($content)) {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_string($decoded))) {
                $content = $decoded;
            } else {
                break;
            }
        }

        if (is_array($content)) {
            foreach ($content as $block) {
                if (is_string($block)) {
                    $renderedHtml .= $block;
                    continue;
                }

                if (!is_array($block)) {
                    continue;
                }

                $type = $block['type'] ?? '';
                $data = $block['data'] ?? [];

                if ($type === 'paragraph') {
                    $renderedHtml .= '<p>' . ($data['text'] ?? '') . '</p>';
                } elseif ($type === 'heading') {
                    $level = $data['level'] ?? 2;
                    $text = $data['text'] ?? '';
                    $id = str(strip_tags($text))->slug()->toString();
                    $renderedHtml .= "<h{$level} id=\"{$id}\">{$text}</h{$level}>";
                } elseif ($type === 'takeaways') {
                    if (!$hasNewTakeaways) {
                        $title = $data['title'] ?? 'Key takeaways';
                        $items = $data['items'] ?? [];
                        $renderedHtml .= '<div class="takeaways"><h2>' . e($title) . '</h2><ul>';
                        foreach ($items as $item) {
                            $renderedHtml .= '<li>' . $item . '</li>';
                        }
                        $renderedHtml .= '</ul></div>';
                    }
                } elseif ($type === 'markdown') {
                    $renderedHtml .= \Illuminate\Support\Str::markdown($data['text'] ?? '');
                } elseif ($type === 'list') {
                    $items = $data['items'] ?? [];
                    $renderedHtml .= '<ul>';
                    foreach ($items as $item) {
                        $renderedHtml .= '<li>' . $item . '</li>';
                    }
                    $renderedHtml .= '</ul>';
                } elseif ($type === 'table') {
                    $headers = $data['headers'] ?? [];
                    $rows = $data['rows'] ?? [];
                    $renderedHtml .= '<table><thead><tr>';
                    foreach ($headers as $th) {
                        $renderedHtml .= '<th>' . e($th) . '</th>';
                    }
                    $renderedHtml .= '</tr></thead><tbody>';
                    foreach ($rows as $row) {
                        $renderedHtml .= '<tr>';
                        foreach ($row as $td) {
                            $renderedHtml .= '<td>' . $td . '</td>';
                        }
                        $renderedHtml .= '</tr>';
                    }
                    $renderedHtml .= '</tbody></table>';
                } else {
                    if (isset($data['text'])) {
                        $renderedHtml .= '<p>' . $data['text'] . '</p>';
                    }
                }
            }
        }

        // Fallback if renderedHtml is still empty but raw content exists
        if (empty(trim($renderedHtml))) {
            $rawContent = is_string($this->postModel->content) ? $this->postModel->content : json_encode($this->postModel->content);
            $renderedHtml = str_replace('\\n', "\n", (string)$rawContent);
            $renderedHtml = preg_replace_callback('/<h2([^>]*)>(.*?)<\/h2>/i', function($matches) {
                $attrs = $matches[1];
                $text = $matches[2];
                $id = str(strip_tags($text))->slug()->toString();
                if (!str_contains($attrs, 'id=')) {
                    return "<h2{$attrs} id=\"{$id}\">{$text}</h2>";
                }
                return $matches[0];
            }, $renderedHtml);
        }

        $postData['content'] = $renderedHtml;

        // Clean title: use meta_title prefix if available
        if (!empty($postData['meta_title'])) {
            $postData['title'] = explode(' | ', $postData['meta_title'])[0];
        }

        $category = $this->postModel->category ? $this->postModel->category->toArray() : ['title' => 'Hearing Health', 'slug' => 'hearing-health'];
        
        $faqs = Faq::where('blog_post_id', $this->postModel->id)->get()->map(function($f) {
            $f->answer = str_replace('\\n', "\n", $f->answer);
            return $f;
        })->toArray();

        $postSchema = [
            "@context" => "https://schema.org",
            "@type" => "BlogPosting",
            "headline" => $postData['title'],
            "description" => $this->postModel->summary,
            "image" => "https://fairfieldhearing.in" . $this->getPostCoverImage(),
            "datePublished" => $this->postModel->created_at,
            "dateModified" => $this->postModel->updated_at ?: $this->postModel->created_at,
            "author" => [
                "@type" => "Person",
                "name" => $this->postModel->author_name,
                "url" => "https://fairfieldhearing.in/team/wasiq-ali-khan"
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => "Fairfield Hearing Clinics",
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => "https://fairfieldhearing.in/assets/img/logo.jpeg"
                ]
            ],
            "mainEntityOfPage" => "https://fairfieldhearing.in/blogs/" . $category['slug'] . "/" . $this->postModel->slug
        ];

        $faqSchema = count($faqs) > 0 ? [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => collect($faqs)->map(fn($f) => [
                "@type" => "Question",
                "name" => $f['question'],
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => strip_tags($f['answer'])
                ]
            ])->toArray()
        ] : null;

        $relatedPosts = BlogPost::with('category')
            ->where('id', '!=', $this->postModel->id)
            ->latest()
            ->take(2)
            ->get();

        return view('livewire.web.blogs.show', [
            'post' => $postData,
            'category' => $category,
            'faqs' => $faqs,
            'coverImage' => $this->getPostCoverImage(),
            'authorPhoto' => $this->getAuthorPhoto($this->postModel->author_name),
            'relatedPosts' => $relatedPosts,
            'postSchema' => $postSchema,
            'faqSchema' => $faqSchema,
        ])->layout('layouts.web', $this->seoForModel($this->postModel, $this->postModel->title . ' | Fairfield Hearing Blogs', $this->postModel->summary));
    }
}
