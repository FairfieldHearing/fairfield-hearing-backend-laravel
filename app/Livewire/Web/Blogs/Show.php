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
        $this->postModel = BlogPost::with('category')->where('slug', $slug)->firstOrFail();
    }

    public function getPostCoverImage(): string
    {
        if ($this->postModel->featured_image) {
            return \Illuminate\Support\Facades\Storage::url($this->postModel->featured_image);
        }

        $slug = $this->postModel->slug;
        if (str_contains($slug, 'styletto')) {
            return "/img/signia-styletto-ix-7ix-vs-5ix-vs-3ix.svg";
        }
        if (str_contains($slug, 'severe')) {
            return "/img/best-hearing-aids-for-severe-to-profound-loss.svg";
        }
        if (str_contains($slug, 'senior')) {
            return "/img/best-hearing-aids-for-senior-citizens.svg";
        }
        return "/img/logo.jpeg";
    }

    public function getAuthorPhoto(string $author): string
    {
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
        $postData['content'] = str_replace('\\n', "\n", $postData['content']);
        
        // Clean title: use meta_title prefix if available
        if (!empty($postData['meta_title'])) {
            $postData['title'] = explode(' | ', $postData['meta_title'])[0];
        }

        // Dynamically parse h2 headings for Table of Contents
        $toc = [];
        preg_match_all('/<h2[^>]*>(.*?)<\/h2>/i', $postData['content'], $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $heading) {
                $cleanHeading = strip_tags($heading);
                $id = str($cleanHeading)->slug()->toString();
                $toc[] = [
                    'text' => $cleanHeading,
                    'id' => $id,
                ];
            }
        }

        // Inject IDs into <h2> tags in the content HTML so anchors function
        $postData['content'] = preg_replace_callback('/<h2([^>]*)>(.*?)<\/h2>/i', function($matches) {
            $attrs = $matches[1];
            $text = $matches[2];
            $id = str(strip_tags($text))->slug()->toString();
            if (!str_contains($attrs, 'id=')) {
                return "<h2{$attrs} id=\"{$id}\">{$text}</h2>";
            }
            return $matches[0];
        }, $postData['content']);

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

        return view('livewire.web.blogs.show', [
            'post' => $postData,
            'category' => $category,
            'faqs' => $faqs,
            'toc' => $toc,
            'coverImage' => $this->getPostCoverImage(),
            'authorPhoto' => $this->getAuthorPhoto($this->postModel->author_name),
            'postSchema' => $postSchema,
            'faqSchema' => $faqSchema,
        ])->layout('layouts.web', $this->seoForModel($this->postModel, $this->postModel->title . ' | Fairfield Hearing Blogs', $this->postModel->summary));
    }
}
