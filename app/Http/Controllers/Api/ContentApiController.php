<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\Request;

class ContentApiController extends Controller
{
    public function posts(Request $request)
    {
        $posts = Post::where('type', 'post')
            ->where('status', 'published')
            ->with(['author:id,name,username', 'featuredMedia'])
            ->latest('published_at')
            ->paginate((int) $request->input('per_page', 10));

        return response()->json($posts);
    }

    public function post(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->with(['author:id,name,username', 'featuredMedia', 'termTaxonomies.term'])
            ->firstOrFail();

        return response()->json($post);
    }

    public function pages(Request $request)
    {
        $pages = Post::where('type', 'page')
            ->where('status', 'published')
            ->with(['author:id,name,username'])
            ->latest()
            ->paginate((int) $request->input('per_page', 10));

        return response()->json($pages);
    }

    public function media(Request $request)
    {
        $media = Media::latest()->paginate((int) $request->input('per_page', 15));
        return response()->json($media);
    }
}
