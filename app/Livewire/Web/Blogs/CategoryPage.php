<?php

namespace App\Livewire\Web\Blogs;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Livewire\Component;

class CategoryPage extends Component
{
    public $category;

    public function mount($category)
    {
        $this->category = BlogCategory::where('slug', $category)->firstOrFail();
    }

    public function render()
    {
        $posts = BlogPost::where('blog_category_id', $this->category->id)->latest()->get()->toArray();

        $collectionSchema = [
            "@context" => "https://schema.org",
            "@type" => "CollectionPage",
            "name" => $this->category->title . " | Fairfield Hearing Clinics",
            "description" => $this->category->short_description,
            "mainEntity" => [
                "@type" => "ItemList",
                "itemListElement" => collect($posts)->map(fn($post, $index) => [
                    "@type" => "ListItem",
                    "position" => $index + 1,
                    "url" => "https://fairfieldhearing.in/blogs/" . $this->category->slug . "/" . $post['slug'],
                    "name" => $post['title']
                ])->toArray()
            ]
        ];

        return view('livewire.web.blogs.category', [
            'category' => $this->category->toArray(),
            'posts' => $posts,
            'collectionSchema' => $collectionSchema
        ])->layout('layouts.web');
    }
}
