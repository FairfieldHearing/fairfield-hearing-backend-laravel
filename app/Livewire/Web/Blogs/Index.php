<?php

namespace App\Livewire\Web\Blogs;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Livewire\Component;
use App\Traits\HasSeo;

class Index extends Component
{
    use HasSeo;

    private function getPostCoverImage($slug)
    {
        if (str_contains($slug, 'styletto')) return "/assets/img/signia-styletto-ix-7ix-vs-5ix-vs-3ix.svg";
        if (str_contains($slug, 'severe')) return "/assets/img/best-hearing-aids-for-severe-to-profound-loss.svg";
        if (str_contains($slug, 'senior')) return "/assets/img/best-hearing-aids-for-senior-citizens.svg";
        return "/assets/img/logo.jpeg";
    }

    public function render()
    {
        $categories = BlogCategory::all()->toArray();
        $posts = BlogPost::with('category')->latest()->get();
        
        $blogSchema = [
            "@context" => "https://schema.org",
            "@type" => "Blog",
            "name" => "Fairfield Hearing Clinics Blog",
            "description" => "Expert articles, buying guides, comparisons and tips on hearing aids and hearing care from Fairfield's RCI-certified audiologists.",
            "publisher" => [
                "@type" => "Organization",
                "name" => "Fairfield Hearing Clinics",
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => "https://fairfieldhearing.in/assets/img/logo.jpeg"
                ]
            ],
            "blogPost" => collect($posts)->map(fn($post) => [
                "@type" => "BlogPosting",
                "headline" => $post->title,
                "description" => $post->summary,
                "datePublished" => $post->created_at,
                "url" => "https://fairfieldhearing.in/blogs/" . ($post->category->slug ?? 'general') . "/" . $post->slug,
                "image" => "https://fairfieldhearing.in" . $this->getPostCoverImage($post->slug)
            ])->toArray()
        ];

        return view('livewire.web.blogs.index', [
            'categories' => $categories,
            'posts' => $posts->toArray(),
            'blogSchema' => $blogSchema
        ])->layout('layouts.web', $this->seo('blogs_index'));
    }
}
