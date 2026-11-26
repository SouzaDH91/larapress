<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Menu;

class MenuRenderer
{
    public static function render(?string $locationSlug = 'header'): string
    {
        $locationSlug = $locationSlug ?: 'header';
        $menu = Menu::where('location_slug', $locationSlug)->with(['items' => function ($q) {
            $q->whereNull('parent_id')->with('children');
        }])->first();

        if (!$menu || $menu->items->isEmpty()) {
            return '';
        }

        // 1. Verificar se o tema ativo possui template customizado para renderizar o menu
        if (view()->exists("theme::menu-{$locationSlug}")) {
            return view("theme::menu-{$locationSlug}", ['menu' => $menu, 'location' => $locationSlug])->render();
        }

        if (view()->exists("theme::menu")) {
            return view("theme::menu", ['menu' => $menu, 'location' => $locationSlug])->render();
        }

        // 2. Renderização padrão semântica com suporte a estados ativos e dropdowns
        $html = '<ul class="menu-list menu-' . e($locationSlug) . ' navbar-nav ms-auto mb-2 mb-lg-0">';
        foreach ($menu->items as $item) {
            $hasChildren = $item->children->isNotEmpty();
            $url = $item->url;
            $isCurrent = request()->fullUrlIs(url($url)) 
                || request()->is(ltrim($url, '/')) 
                || (request()->path() === '/' && ($url === '/' || $url === url('/')));

            $activeClass = $isCurrent ? ' active current-menu-item' : '';

            if ($hasChildren) {
                $html .= '<li class="nav-item menu-item dropdown' . $activeClass . '">';
                $html .= '<a class="nav-link menu-link dropdown-toggle ' . ($isCurrent ? 'active ' : '') . e($item->css_class ?? '') . '" href="#" role="button" data-bs-toggle="dropdown">' . e($item->title) . '</a>';
                $html .= '<ul class="dropdown-menu sub-menu">';
                foreach ($item->children as $child) {
                    $childUrl = $child->url;
                    $childActive = request()->fullUrlIs(url($childUrl)) || request()->is(ltrim($childUrl, '/'));
                    $html .= '<li class="menu-item' . ($childActive ? ' active' : '') . '">';
                    $html .= '<a class="dropdown-item ' . ($childActive ? 'active ' : '') . e($child->css_class ?? '') . '" href="' . e($child->url) . '" target="' . e($child->target ?? '_self') . '">' . e($child->title) . '</a>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
                $html .= '</li>';
            } else {
                $html .= '<li class="nav-item menu-item' . $activeClass . '">';
                $html .= '<a class="nav-link menu-link ' . ($isCurrent ? 'active ' : '') . e($item->css_class ?? '') . '" href="' . e($item->url) . '" target="' . e($item->target ?? '_self') . '">' . e($item->title) . '</a>';
                $html .= '</li>';
            }
        }
        $html .= '</ul>';

        return apply_filters('larapress_menu_html', $html, $menu, $locationSlug);
    }
}
