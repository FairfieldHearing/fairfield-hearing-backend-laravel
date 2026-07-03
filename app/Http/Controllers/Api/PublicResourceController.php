<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Location;
use App\Models\PolicyPage;
use Illuminate\Http\Request;

class PublicResourceController extends Controller
{
    // Get all blog categories
    public function categories()
    {
        return response()->json([
            'success' => true,
            'categories' => BlogCategory::all()
        ]);
    }

    // Get blog posts (optional filter by category slug)
    public function posts(Request $request)
    {
        $categorySlug = $request->query('category');

        $posts = BlogPost::with('category')
            ->when($categorySlug, function ($query) use ($categorySlug) {
                $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'posts' => $posts
        ]);
    }

    // Get a single blog post by slug with its category and related FAQs
    public function post($slug)
    {
        $post = BlogPost::where('slug', $slug)->with('category')->firstOrFail();
        $faqs = Faq::where('blog_post_id', $post->id)->where('type', 'blog_post')->orderBy('sort_order', 'asc')->get();

        return response()->json([
            'success' => true,
            'post' => $post,
            'faqs' => $faqs
        ]);
    }

    // Get FAQs (general FAQs by default, or all FAQs)
    public function faqs(Request $request)
    {
        $type = $request->query('type', 'general');

        $faqs = Faq::when($type !== 'all', function ($query) use ($type) {
            $query->where('type', $type);
        })->orderBy('sort_order', 'asc')->get();

        return response()->json([
            'success' => true,
            'faqs' => $faqs
        ]);
    }

    // Get all office/centre locations
    public function locations()
    {
        return response()->json([
            'success' => true,
            'locations' => Location::orderBy('sort_order', 'asc')->orderBy('is_main', 'desc')->get()
        ]);
    }

    // Get a single location
    public function location($id)
    {
        $location = Location::findOrFail($id);

        return response()->json([
            'success' => true,
            'location' => $location
        ]);
    }

    // Get all policies
    public function policies()
    {
        return response()->json([
            'success' => true,
            'policies' => PolicyPage::select('id', 'title', 'slug', 'meta_title', 'meta_description', 'created_at', 'updated_at')->get()
        ]);
    }

    // Get single policy page by slug
    public function policy($slug)
    {
        $policy = PolicyPage::where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'policy' => $policy
        ]);
    }

    // Get all team members
    public function team()
    {
        return response()->json([
            'success' => true,
            'team' => \App\Models\TeamMember::orderBy('sort_order', 'asc')->get()
        ]);
    }

    // Get a single team member by slug
    public function teamMember($slug)
    {
        $member = \App\Models\TeamMember::where('slug', $slug)->firstOrFail();
        return response()->json([
            'success' => true,
            'member' => $member
        ]);
    }
}
