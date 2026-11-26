<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Services\PermalinkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermalinksTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_post_name_permalink_resolution(): void
    {
        update_option('permalink_structure', '/%postname%/');

        $post = Post::where('slug', 'bem-vindo-ao-larapress-cms')->first();
        $this->assertNotNull($post);

        $this->assertEquals(url('/bem-vindo-ao-larapress-cms'), $post->url);

        $response = $this->get('/bem-vindo-ao-larapress-cms');
        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    public function test_year_month_postname_permalink_resolution(): void
    {
        update_option('permalink_structure', '/%year%/%month%/%postname%/');

        $post = Post::where('slug', 'bem-vindo-ao-larapress-cms')->first();
        $this->assertNotNull($post);

        $year = $post->published_at->format('Y');
        $month = $post->published_at->format('m');
        $expectedUrl = url("/{$year}/{$month}/bem-vindo-ao-larapress-cms");

        $this->assertEquals($expectedUrl, $post->url);

        // Testar requisição HTTP na URL dinâmica com ano e mês
        $response = $this->get("/{$year}/{$month}/bem-vindo-ao-larapress-cms");
        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    public function test_day_year_month_postname_permalink_resolution(): void
    {
        update_option('permalink_structure', '/%year%/%month%/%day%/%postname%/');

        $post = Post::where('slug', 'bem-vindo-ao-larapress-cms')->first();
        $year = $post->published_at->format('Y');
        $month = $post->published_at->format('m');
        $day = $post->published_at->format('d');

        $response = $this->get("/{$year}/{$month}/{$day}/bem-vindo-ao-larapress-cms");
        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    public function test_category_postname_permalink_resolution(): void
    {
        update_option('permalink_structure', '/%category%/%postname%/');

        $post = Post::where('slug', 'bem-vindo-ao-larapress-cms')->first();
        $this->assertNotNull($post);

        $url = $post->url;
        $this->assertStringContainsString('tecnologia/bem-vindo-ao-larapress-cms', $url);

        $response = $this->get('/tecnologia/bem-vindo-ao-larapress-cms');
        $response->assertStatus(200);
        $response->assertSee($post->title);
    }
}
