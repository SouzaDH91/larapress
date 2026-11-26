<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_author_can_access_posts_and_media(): void
    {
        $author = User::create([
            'name' => 'Autor Teste',
            'username' => 'autor.teste',
            'email' => 'autor@larapress.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $author->assignRole('author');

        $this->actingAs($author);

        // Autor tem permissão para Posts e Mídia
        $this->get('/admin/posts/list')->assertStatus(200);
        $this->get('/admin/posts/create')->assertStatus(200);
        $this->get('/admin/media')->assertStatus(200);
    }

    public function test_author_cannot_access_pages_users_or_settings(): void
    {
        $author = User::create([
            'name' => 'Autor Teste 2',
            'username' => 'autor.teste2',
            'email' => 'autor2@larapress.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $author->assignRole('author');

        $this->actingAs($author);

        // Autor NÃO tem permissão para Páginas, Plugins, Usuários, Configurações ou Categorias
        $this->get('/admin/pages/list')->assertStatus(403);
        $this->get('/admin/categories')->assertStatus(403);
        $this->get('/admin/users')->assertStatus(403);
        $this->get('/admin/settings')->assertStatus(403);
        $this->get('/admin/plugins')->assertStatus(403);
        $this->get('/admin/themes')->assertStatus(403);
    }

    public function test_author_sidebar_does_not_render_restricted_menus(): void
    {
        $author = User::create([
            'name' => 'Autor Teste 3',
            'username' => 'autor.teste3',
            'email' => 'autor3@larapress.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $author->assignRole('author');

        $this->actingAs($author);

        $response = $this->get('/admin');
        $response->assertStatus(200);

        // Deve exibir Posts e Mídia
        $response->assertSee('Posts');
        $response->assertSee('Biblioteca de Mídia');

        // NÃO deve exibir Páginas, Configurações, Usuários, Temas nem Menus
        $response->assertDontSee('/admin/pages/list');
        $response->assertDontSee('/admin/settings');
        $response->assertDontSee('/admin/users');
        $response->assertDontSee('/admin/plugins');
        $response->assertDontSee('/admin/themes');
    }
}
