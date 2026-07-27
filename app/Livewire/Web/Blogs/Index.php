<?php

namespace App\Livewire\Web\Blogs;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Livewire\Component;
use App\Traits\HasSeo;

class Index extends Component
{
    use HasSeo;

    private function getPostCoverImage($post): string
    {
        if (is_object($post) && method_exists($post, 'getFeaturedImageUrlAttribute')) {
            return $post->featured_image_url;
        }

        $featuredImage = is_array($post) ? ($post['featured_image'] ?? null) : ($post->featured_image ?? null);
        $slug = is_array($post) ? ($post['slug'] ?? '') : ($post->slug ?? '');

        if ($featuredImage) {
            if (str_starts_with($featuredImage, 'assets/') || str_starts_with($featuredImage, '/assets/')) {
                return '/' . ltrim($featuredImage, '/');
            }
            return \Illuminate\Support\Facades\Storage::url($featuredImage);
        }

        if (str_contains($slug, 'styletto')) return "/assets/img/signia-styletto-ix-7ix-vs-5ix-vs-3ix.svg";
        if (str_contains($slug, 'severe')) return "/assets/img/best-hearing-aids-for-severe-to-profound-loss.svg";
        if (str_contains($slug, 'senior')) return "/assets/img/best-hearing-aids-for-senior-citizens.svg";
        return "/assets/img/logo.jpeg";
    }

    public function render()
    {
        $categories = BlogCategory::all()->toArray();
        $posts = BlogPost::with(['category', 'featuredImageMedia'])->latest()->get();
        
        $postsArray = $posts->map(function ($post) {
            $arr = $post->toArray();
            $arr['cover_image'] = $post->featured_image_url;
            return $arr;
        })->toArray();

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
                "image" => "https://fairfieldhearing.in" . $post->featured_image_url
            ])->toArray()
        ];

        return view('livewire.web.blogs.index', [
            'categories' => $categories,
            'posts' => $postsArray,
            'blogSchema' => $blogSchema
        ])->layout('layouts.web', $this->seo('blogs_index'));
    }
}
