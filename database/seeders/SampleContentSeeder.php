<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Option;
use App\Models\Post;
use App\Models\PostMeta;
use App\Models\PostRevision;
use App\Models\Role;
use App\Models\Term;
use App\Models\TermTaxonomy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleContentSeeder extends Seeder
{
    /**
     * Seed realistic sample content for LaraPress CMS.
     */
    public function run(): void
    {
        $admin = User::first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Administrator',
                'username' => 'admin',
                'email' => 'admin@larapress.test',
                'password' => Hash::make('admin123'),
                'bio' => 'LaraPress Core Team Administrator',
                'status' => 'active',
            ]);
            $adminRole = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
            $admin->assignRole($adminRole);
        }

        // Criar autor secundário
        $editorUser = User::firstOrCreate(
            ['email' => 'editor@larapress.test'],
            [
                'name' => 'Mariana Silva',
                'username' => 'mariana.silva',
                'password' => Hash::make('editor123'),
                'bio' => 'Jornalista de tecnologia e editora-chefe do blog.',
                'status' => 'active',
            ]
        );
        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editorUser->assignRole($editorRole);

        // 1. Criar Categorias
        $categoriesData = [
            ['name' => 'Tecnologia & Inovação', 'slug' => 'tecnologia', 'desc' => 'Artigos sobre engenharia de software, novidades e arquitetura moderna.'],
            ['name' => 'Segurança Web', 'slug' => 'seguranca', 'desc' => 'Boas práticas para blindagem contra ataques cibernéticos, OWASP e criptografia.'],
            ['name' => 'Tutoriais Laravel', 'slug' => 'tutoriais-laravel', 'desc' => 'Dicas práticas de Laravel 13, Livewire v4 e ecossistema moderno PHP.'],
            ['name' => 'CMS & WordPress', 'slug' => 'cms-wordpress', 'desc' => 'Comparações, migrações e vantagens de transição do WordPress para LaraPress.'],
        ];

        $categoryTaxonomies = [];
        foreach ($categoriesData as $c) {
            $term = Term::firstOrCreate(
                ['slug' => $c['slug']],
                ['name' => $c['name'], 'description' => $c['desc']]
            );
            $tax = TermTaxonomy::firstOrCreate(
                ['term_id' => $term->id, 'taxonomy' => 'category'],
                ['count' => 0]
            );
            $categoryTaxonomies[$c['slug']] = $tax;
        }

        // 2. Criar Tags
        $tagsData = ['PHP 8.3', 'Laravel 13', 'UUID', 'Livewire', 'Intervention Image', 'Anti-Enumeração'];
        $tagTaxonomies = [];
        foreach ($tagsData as $tagName) {
            $term = Term::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
            $tax = TermTaxonomy::firstOrCreate(
                ['term_id' => $term->id, 'taxonomy' => 'post_tag'],
                ['count' => 0]
            );
            $tagTaxonomies[] = $tax;
        }

        // 3. Criar Artigos de Exemplo (Posts)
        $postsData = [
            [
                'title' => 'Bem-vindo ao LaraPress CMS: O Futuro da Gestão de Conteúdo',
                'slug' => 'bem-vindo-ao-larapress-cms',
                'excerpt' => 'Conheça o novo LaraPress CMS, construído do zero sobre Laravel 13 com UUIDs nativos, blindagem de uploads e zero execuções inseguras.',
                'content_html' => '
                    <p class="lead">O <strong>LaraPress CMS</strong> nasceu com um propósito claro: entregar a simplicidade e a flexibilidade que tornaram o WordPress famoso, mas sem as vulnerabilidades históricas de injeção de código, eval arbitrário e ataques de enumeração.</p>
                    
                    <h3>Por que escolher o LaraPress?</h3>
                    <p>Ao contrário dos CMS legados, o LaraPress segue práticas de engenharia de software de classe empresarial:</p>
                    <ul>
                        <li><strong>Chaves Primárias em UUID:</strong> Protege seus endpoints contra varreduras de enumeração sequencial de IDs.</li>
                        <li><strong>Blindagem de Arquivos:</strong> Uploads com validação estrita de Magic Bytes e conversão instantânea de imagens para WebP com geração de miniaturas.</li>
                        <li><strong>Arquitetura Modular:</strong> Plugins estruturados em ServiceProviders do Laravel sem recompilação de assets em tempo de execução.</li>
                        <li><strong>Performance Extrema:</strong> Cache de página inteira (FPC) para visitantes anônimos e compatibilidade com Redis.</li>
                    </ul>

                    <blockquote>"Segurança não deve ser um recurso adicional, mas a base de qualquer sistema web moderno."</blockquote>

                    <p>Explore o painel administrativo, experimente criar novas páginas e posts, e teste a velocidade de renderização em Blade!</p>
                ',
                'status' => 'published',
                'author_id' => $admin->id,
                'categories' => ['tecnologia', 'cms-wordpress'],
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Como Blindar sua Aplicação Web contra Falhas de Enumeração e Injeção',
                'slug' => 'como-blindar-aplicacao-contra-enumeracao',
                'excerpt' => 'Entenda como o uso sistemático de UUIDs em todas as chaves primárias e relacionamentos previne ataques cibernéticos comuns.',
                'content_html' => '
                    <p>Uma das vulnerabilidades mais recorrentes em aplicações web é a <em>Enumeração de Objetos Diretos Inseguros (IDOR)</em>. Quando você utiliza IDs inteiros sequenciais (como <code>/users/1</code>, <code>/users/2</code>), um invasor consegue varrer todo o seu banco de dados em segundos.</p>
                    
                    <h3>A Solução do LaraPress: UUID v4 Universal</h3>
                    <p>No LaraPress, 100% das tabelas principais usam UUIDs como chave primária:</p>
                    <pre><code class="language-php">$table->uuid(\'id\')->primary();
$table->foreignUuid(\'author_id\')->constrained(\'users\');</code></pre>
                    
                    <p>Com essa abordagem, adivinhar identificadores válidos torna-se matematicamente inviável, garantindo privacidade para usuários, transações e conteúdos restritos.</p>
                ',
                'status' => 'published',
                'author_id' => $editorUser->id,
                'categories' => ['seguranca', 'tutoriais-laravel'],
                'published_at' => now()->subDay(),
            ],
            [
                'title' => 'Laravel 13 & Livewire: Reatividade no Painel sem Compilação no Servidor',
                'slug' => 'laravel-13-livewire-reatividade-no-painel',
                'excerpt' => 'Como combinamos Blade, Livewire e Alpine.js sobre o template Velzon para entregar uma experiência SPA fluida.',
                'content_html' => '
                    <p>O painel administrativo do LaraPress adota a elegância do tema <strong>Velzon</strong> combinado ao poder do <strong>Livewire</strong>.</p>
                    <p>Isso permite que você tenha busca em tempo real, moderação instantânea de comentários, uploads com barra de progresso e ordenação por arraste de menus sem necessitar de ferramentas complexas de build (como Node.js/NPM) rodando no servidor de produção.</p>
                    <p>Para os desenvolvedores de plugins, criar uma nova tela administrativa é tão simples quanto registrar um componente Livewire e injetar um link na sidebar via hook:</p>
                    <pre><code class="language-php">add_action(\'larapress_admin_sidebar_menu\', function() {
    echo \'&lt;li&gt;&lt;a href="/admin/meu-plugin"&gt;&lt;i class="ri-rocket-line"&gt;&lt;/i&gt; Meu Plugin&lt;/a&gt;&lt;/li&gt;\';
});</code></pre>
                ',
                'status' => 'published',
                'author_id' => $admin->id,
                'categories' => ['tecnologia', 'tutoriais-laravel'],
                'published_at' => now(),
            ],
        ];

        foreach ($postsData as $p) {
            $post = Post::firstOrCreate(
                ['slug' => $p['slug']],
                [
                    'author_id' => $p['author_id'],
                    'title' => $p['title'],
                    'excerpt' => $p['excerpt'],
                    'content_html' => trim($p['content_html']),
                    'content_json' => ['html' => trim($p['content_html'])],
                    'status' => $p['status'],
                    'type' => 'post',
                    'comment_status' => 'open',
                    'published_at' => $p['published_at'],
                ]
            );

            // Associar Categorias
            $taxIds = [];
            foreach ($p['categories'] as $catSlug) {
                if (isset($categoryTaxonomies[$catSlug])) {
                    $taxIds[] = $categoryTaxonomies[$catSlug]->id;
                }
            }
            if (!empty($taxIds)) {
                $post->termTaxonomies()->syncWithoutDetaching($taxIds);
            }

            // Criar revisão inicial
            PostRevision::firstOrCreate(
                ['post_id' => $post->id, 'title' => $post->title],
                [
                    'user_id' => $p['author_id'],
                    'content_json' => ['html' => trim($p['content_html'])],
                    'created_at' => now(),
                ]
            );

            // Criar Comentários de Exemplo
            if ($p['slug'] === 'bem-vindo-ao-larapress-cms') {
                Comment::firstOrCreate(
                    ['post_id' => $post->id, 'author_email' => 'carlos@cliente.com'],
                    [
                        'author_name' => 'Carlos Eduardo',
                        'content' => 'Impressionante a velocidade de carregamento e o acabamento do painel! Finalmente uma alternativa moderna para quem ama Laravel.',
                        'status' => 'approved',
                        'author_ip' => '127.0.0.1',
                    ]
                );
                Comment::firstOrCreate(
                    ['post_id' => $post->id, 'author_email' => 'fernanda@dev.io'],
                    [
                        'author_name' => 'Fernanda Lima',
                        'content' => 'A decisão de usar UUIDs nativos em todas as tabelas foi excelente. Dá muito mais tranquilidade para projetos corporativos.',
                        'status' => 'approved',
                        'author_ip' => '127.0.0.1',
                    ]
                );
            }
        }

        // 4. Criar Páginas Estáticas (Pages)
        $pagesData = [
            [
                'title' => 'Sobre o LaraPress',
                'slug' => 'sobre',
                'content_html' => '
                    <p class="lead">O <strong>LaraPress CMS</strong> foi concebido para atender desenvolvedores e agências que precisam de um CMS robusto, confiável e fácil de manter em PHP moderno.</p>
                    <h3>Missão</h3>
                    <p>Eliminar as brechas de segurança de plugins maliciosos e códigos legados, mantendo a facilidade de criação de temas em Blade e a extensibilidade de código limpo.</p>
                    <h3>Tecnologias Empregadas</h3>
                    <ul>
                        <li>Laravel 13</li>
                        <li>Livewire v4 & Alpine.js</li>
                        <li>Bootstrap 5 + Velzon UI</li>
                        <li>Intervention Image (WebP nativo)</li>
                        <li>Spatie Laravel Permission (UUIDs)</li>
                    </ul>
                ',
            ],
            [
                'title' => 'Fale Conosco',
                'slug' => 'contato',
                'content_html' => '
                    <p class="lead">Precisa de suporte, gostaria de sugerir novas funcionalidades ou relatar um comportamento?</p>
                    <p>Entre em contato conosco através dos nossos canais oficiais:</p>
                    <ul>
                        <li><strong>E-mail:</strong> contato@larapress.test</li>
                        <li><strong>Repositório:</strong> github.com/larapress/cms</li>
                    </ul>
                ',
            ],
        ];

        foreach ($pagesData as $pg) {
            Post::firstOrCreate(
                ['slug' => $pg['slug'], 'type' => 'page'],
                [
                    'author_id' => $admin->id,
                    'title' => $pg['title'],
                    'content_html' => trim($pg['content_html']),
                    'content_json' => ['html' => trim($pg['content_html'])],
                    'status' => 'published',
                    'type' => 'page',
                    'comment_status' => 'closed',
                    'published_at' => now(),
                ]
            );
        }

        // 5. Criar Menus de Navegação
        $mainMenu = Menu::firstOrCreate(
            ['location_slug' => 'header'],
            ['name' => 'Menu Superior Principal']
        );

        $menuItems = [
            ['title' => 'Início', 'url' => '/', 'order' => 1],
            ['title' => 'Tecnologia', 'url' => '/bem-vindo-ao-larapress-cms', 'order' => 2],
            ['title' => 'Segurança', 'url' => '/como-blindar-aplicacao-contra-enumeracao', 'order' => 3],
            ['title' => 'Sobre', 'url' => '/sobre', 'order' => 4],
            ['title' => 'Contato', 'url' => '/contato', 'order' => 5],
        ];

        foreach ($menuItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $mainMenu->id, 'title' => $item['title']],
                ['url' => $item['url'], 'order' => $item['order'], 'target' => '_self']
            );
        }

        // 6. Atualizar contagem de termos nas taxonomias
        foreach (TermTaxonomy::with('posts')->get() as $tt) {
            $tt->update(['count' => $tt->posts()->count()]);
        }
    }
}
