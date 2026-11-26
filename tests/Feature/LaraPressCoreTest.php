<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Services\MediaService;
use App\Services\OptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LaraPressCoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_all_models_use_uuid_primary_keys(): void
    {
        $user = User::first();
        $this->assertNotNull($user);
        $this->assertIsString($user->id);
        $this->assertEquals(36, strlen($user->id));

        $post = Post::create([
            'author_id' => $user->id,
            'title' => 'Primeiro Artigo',
            'slug' => 'primeiro-artigo',
            'content_html' => '<p>Conteúdo de teste</p>',
            'status' => 'published',
            'type' => 'post',
        ]);

        $this->assertIsString($post->id);
        $this->assertEquals(36, strlen($post->id));
        $this->assertEquals('Primeiro Artigo', $post->title);
    }

    public function test_options_service_and_helpers_work(): void
    {
        update_option('test_setting', 'lara_val');
        $this->assertEquals('lara_val', get_option('test_setting'));

        $this->assertEquals('LaraPress CMS', get_option('site_name'));
    }

    public function test_dangerous_files_are_strictly_blocked(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $fakePhp = UploadedFile::fake()->create('malicious.php', 10, 'text/x-php');
        $mediaService = app(MediaService::class);
        $mediaService->upload($fakePhp);
    }

    public function test_frontend_routes_and_security_headers(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_sitemap_and_robots(): void
    {
        $sitemap = $this->get('/sitemap.xml');
        $sitemap->assertStatus(200);
        $this->assertStringContainsString('urlset', $sitemap->getContent());

        $robots = $this->get('/robots.txt');
        $robots->assertStatus(200);
        $this->assertStringContainsString('Disallow: /admin/', $robots->getContent());
    }

    public function test_api_headless_endpoints(): void
    {
        $response = $this->getJson('/api/v1/posts');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }
}
