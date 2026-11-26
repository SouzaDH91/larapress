<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_all_admin_pages_render_content_properly(): void
    {
        $admin = User::first();
        $this->actingAs($admin);

        $routes = [
            '/admin',
            '/admin/posts/list',
            '/admin/posts/create',
            '/admin/pages/list',
            '/admin/pages/create',
            '/admin/categories',
            '/admin/tags',
            '/admin/media',
            '/admin/comments',
            '/admin/themes',
            '/admin/menus',
            '/admin/plugins',
            '/admin/users',
            '/admin/settings',
            '/admin/profile',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
            // Garantir que a área de conteúdo não ficou vazia
            $content = $response->getContent();
            $this->assertStringContainsString('page-content', $content);
            $this->assertStringNotContainsString('<div class="page-content">\s*</div>', $content);
        }
    }
}
