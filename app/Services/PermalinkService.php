<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;

class PermalinkService
{
    /**
     * Preset structures identical to WordPress.
     */
    public const PRESETS = [
        'plain' => [
            'name' => 'Padrão / Simples',
            'structure' => '/?p=%post_id%',
            'example' => '/?p=123',
        ],
        'day_and_name' => [
            'name' => 'Dia e Nome',
            'structure' => '/%year%/%month%/%day%/%postname%/',
            'example' => '/2026/11/24/exemplo-post/',
        ],
        'month_and_name' => [
            'name' => 'Mês e Nome',
            'structure' => '/%year%/%month%/%postname%/',
            'example' => '/2026/11/exemplo-post/',
        ],
        'numeric' => [
            'name' => 'Numérico',
            'structure' => '/arquivos/%post_id%/',
            'example' => '/arquivos/123/',
        ],
        'post_name' => [
            'name' => 'Nome do Post',
            'structure' => '/%postname%/',
            'example' => '/exemplo-post/',
        ],
        'custom' => [
            'name' => 'Estrutura Personalizada',
            'structure' => '',
            'example' => '',
        ],
    ];

    /**
     * Build the public URL for a given Post or Page.
     */
    public function getUrl(Post $post): string
    {
        // Pages always use plain slug hierarchy /pagina-nome
        if ($post->type === 'page') {
            return url('/' . $post->slug);
        }

        $structure = (string) get_option('permalink_structure', '/%postname%/');
        $structure = trim($structure);

        if (empty($structure) || $structure === '/%postname%' || $structure === '/%postname%/') {
            return url('/' . $post->slug);
        }

        $publishedAt = $post->published_at ?? $post->created_at ?? now();

        // Get primary category slug
        $categorySlug = 'uncategorized';
        $firstCat = $post->termTaxonomies()
            ->where('taxonomy', 'category')
            ->with('term')
            ->first();

        if ($firstCat && $firstCat->term) {
            $categorySlug = $firstCat->term->slug;
        }

        $replacements = [
            '%year%' => $publishedAt->format('Y'),
            '%month%' => $publishedAt->format('m'),
            '%day%' => $publishedAt->format('d'),
            '%hour%' => $publishedAt->format('H'),
            '%minute%' => $publishedAt->format('i'),
            '%second%' => $publishedAt->format('s'),
            '%post_id%' => $post->id,
            '%postname%' => $post->slug,
            '%category%' => $categorySlug,
            '%author%' => $post->author?->username ?? 'admin',
        ];

        $path = str_replace(array_keys($replacements), array_values($replacements), $structure);
        $path = '/' . ltrim($path, '/');

        return url($path);
    }

    /**
     * Extract the post slug or ID from a request path given the current permalink structure.
     */
    public function resolvePostSlugFromPath(string $path): ?string
    {
        $path = '/' . trim($path, '/');
        $structure = (string) get_option('permalink_structure', '/%postname%/');
        $structure = '/' . trim($structure, '/');

        // Fast path for /%postname%
        if ($structure === '/%postname' || $structure === '') {
            return ltrim($path, '/');
        }

        // Convert permalink tags to regex pattern
        $pattern = preg_quote($structure, '#');
        $pattern = str_replace([
            '\%year\%',
            '\%month\%',
            '\%day\%',
            '\%hour\%',
            '\%minute\%',
            '\%second\%',
            '\%post_id\%',
            '\%postname\%',
            '\%category\%',
            '\%author\%',
        ], [
            '(\d{4})',
            '(\d{2})',
            '(\d{2})',
            '(\d{2})',
            '(\d{2})',
            '(\d{2})',
            '([a-zA-Z0-9\-]+)',
            '([a-zA-Z0-9\-_]+)',
            '([a-zA-Z0-9\-_]+)',
            '([a-zA-Z0-9\-_]+)',
        ], $pattern);

        // Capture named groups for %postname% and %post_id%
        $tagsRegex = $structure;
        $tagsRegex = str_replace('%postname%', '(?P<slug>[a-zA-Z0-9\-_]+)', $tagsRegex);
        $tagsRegex = str_replace('%post_id%', '(?P<id>[a-zA-Z0-9\-]+)', $tagsRegex);
        $tagsRegex = str_replace('%category%', '(?P<category>[a-zA-Z0-9\-_]+)', $tagsRegex);
        $tagsRegex = str_replace(['%year%', '%month%', '%day%'], ['\d{4}', '\d{2}', '\d{2}'], $tagsRegex);
        $tagsRegex = str_replace(['%hour%', '%minute%', '%second%'], ['\d{2}', '\d{2}'], $tagsRegex);
        $tagsRegex = str_replace('%author%', '[a-zA-Z0-9\-_]+', $tagsRegex);

        $regex = '#^' . $tagsRegex . '$#';

        if (preg_match($regex, $path, $matches)) {
            if (!empty($matches['slug'])) {
                return $matches['slug'];
            }
            if (!empty($matches['id'])) {
                return $matches['id'];
            }
        }

        // Fallback: take last segment
        $segments = explode('/', trim($path, '/'));
        return end($segments) ?: null;
    }
}
