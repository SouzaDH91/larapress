<?php

declare(strict_types=1);

namespace Plugins\HelloLaraPress;

use Illuminate\Support\ServiceProvider;

class HelloLaraPressServiceProvider extends ServiceProvider
{
    /**
     * Register any plugin services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap plugin services and register hooks.
     */
    public function boot(): void
    {
        // 1. Injetar item de menu na sidebar do painel Velzon via Hook de Ação
        add_action('larapress_admin_sidebar_menu', function () {
            echo '<li class="menu-title">Extensão Ativa</li>';
            echo '<li>';
            echo '<a href="#" onclick="alert(\'Olá do Plugin Demo Hello LaraPress!\'); return false;">';
            echo '<i class="ri-sparkling-fill text-warning"></i> <span>Hello LaraPress</span>';
            echo '</a>';
            echo '</li>';
        });

        // 2. Registrar Shortcode [alerta tipo="info"]Conteúdo[/alerta] para o frontend
        add_shortcode('alerta', function ($attributes, $content = null) {
            $tipo = $attributes['tipo'] ?? 'info';
            $classe = match ($tipo) {
                'sucesso' => 'alert alert-success',
                'aviso' => 'alert alert-warning',
                'perigo' => 'alert alert-danger',
                default => 'alert alert-info',
            };

            return '<div class="' . e($classe) . ' my-3 shadow-sm" role="alert"><i class="ri-information-line me-1"></i> ' . e($content ?? '') . '</div>';
        });

        // 3. Registrar Filtro de Conteúdo de Post
        add_filter('larapress_sidebar_footer-1', function ($content) {
            return $content . '<div class="card p-3 border-0 bg-light mt-3"><small class="text-muted"><i class="ri-plug-fill text-success"></i> Rodapé enriquecido dinamicamente pelo plugin <strong>Hello LaraPress</strong>.</small></div>';
        });
    }
}
