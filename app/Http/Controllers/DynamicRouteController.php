<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class DynamicRouteController extends Controller
{
    /**
     * Resolve the homepage.
     */
    public function home()
    {
        $posts = Post::where('type', 'post')
            ->where('status', 'published')
            ->with(['author'])
            ->latest('published_at')
            ->paginate(10);

        return view('theme::index', ['posts' => $posts]);
    }

    /**
     * Catch-all route to resolve permalinks for posts and pages.
     */
    public function resolve(string $slug)
    {
        // 1. Resolve through PermalinkService
        $resolvedSlugOrId = app(\App\Services\PermalinkService::class)->resolvePostSlugFromPath($slug);

        // 2. Try finding Post or Page by slug or UUID
        $post = Post::where(static function ($q) use ($slug, $resolvedSlugOrId) {
            $q->where('slug', $resolvedSlugOrId)
              ->orWhere('id', $resolvedSlugOrId)
              ->orWhere('slug', $slug);
        })
        ->where('status', 'published')
        ->first();

        if ($post) {
            if ($post->type === 'page') {
                return view('theme::page', ['page' => $post]);
            }
            return view('theme::single', ['post' => $post]);
        }

        abort(404, 'Página não encontrada.');
    }

    /**
     * Submit a comment from frontend.
     */
    public function postComment(Request $request, string $postId)
    {
        $request->validate([
            'author_name' => 'required|string|max:255',
            'author_email' => 'required|email|max:255',
            'content' => 'required|string|max:3000',
        ]);

        $post = Post::findOrFail($postId);

        if ($post->comment_status !== 'open') {
            return back()->with('error', 'Os comentários estão fechados para esta publicação.');
        }

        $needsModeration = (bool) get_option('comments_moderation', true);

        Comment::create([
            'post_id' => $post->id,
            'author_name' => $request->input('author_name'),
            'author_email' => $request->input('author_email'),
            'author_ip' => $request->ip(),
            'content' => strip_tags($request->input('content')),
            'status' => $needsModeration ? 'pending' : 'approved',
        ]);

        $message = $needsModeration
            ? 'Obrigado! Seu comentário foi enviado e aguarda aprovação da moderação.'
            : 'Comentário publicado com sucesso!';

        return back()->with('comment_success', $message);
    }

    /**
     * Dynamic sitemap.xml generator.
     */
    public function sitemap()
    {
        $posts = Post::where('status', 'published')->latest()->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $xml .= '<url><loc>' . url('/') . '</loc><changefreq>daily</changefreq><priority>1.0</priority></url>';

        foreach ($posts as $post) {
            $xml .= '<url>';
            $xml .= '<loc>' . url('/' . $post->slug) . '</loc>';
            $xml .= '<lastmod>' . ($post->updated_at?->toAtomString() ?? now()->toAtomString()) . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>' . ($post->type === 'page' ? '0.8' : '0.6') . '</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Dynamic robots.txt generator.
     */
    public function robots()
    {
        $content = "User-agent: *\nDisallow: /admin/\nDisallow: /api/\n\nSitemap: " . url('/sitemap.xml');
        return response($content, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * RSS 2.0 Feed generator.
     */
    public function feed()
    {
        $posts = Post::where('type', 'post')->where('status', 'published')->latest('published_at')->take(20)->get();

        $rss = '<?xml version="1.0" encoding="UTF-8"?>';
        $rss .= '<rss version="2.0"><channel>';
        $rss .= '<title>' . e(get_option('site_name', 'LaraPress CMS')) . '</title>';
        $rss .= '<link>' . url('/') . '</link>';
        $rss .= '<description>' . e(get_option('site_description', '')) . '</description>';
        $rss .= '<language>pt-BR</language>';

        foreach ($posts as $post) {
            $rss .= '<item>';
            $rss .= '<title>' . e($post->title) . '</title>';
            $rss .= '<link>' . url('/' . $post->slug) . '</link>';
            $rss .= '<guid>' . url('/' . $post->slug) . '</guid>';
            $rss .= '<pubDate>' . ($post->published_at?->toRfc2822String() ?? $post->created_at->toRfc2822String()) . '</pubDate>';
            $rss .= '<description><![CDATA[' . ($post->excerpt ?? Str::limit(strip_tags($post->content_html ?? ''), 250)) . ']]></description>';
            $rss .= '</item>';
        }

        $rss .= '</channel></rss>';

        return response($rss, 200)->header('Content-Type', 'application/rss+xml');
    }

    /**
     * Serve theme static assets safely.
     */
    public function themeAsset(string $theme, string $file)
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $theme)) {
            abort(404);
        }

        if (str_contains($file, '..') || str_contains($file, '\\')) {
            abort(403, 'Acesso não autorizado.');
        }

        $fullPath = base_path("themes/{$theme}/{$file}");

        if (!File::exists($fullPath) || File::isDirectory($fullPath)) {
            abort(404, 'Arquivo de tema não encontrado.');
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mimes = [
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'json' => 'application/json',
        ];

        $mime = $mimes[$ext] ?? File::mimeType($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=604800, stale-while-revalidate=86400',
        ]);
    }
}
