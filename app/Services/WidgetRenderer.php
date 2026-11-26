<?php

declare(strict_types=1);

namespace App\Services;

class WidgetRenderer
{
    public static function render(string $sidebarArea): string
    {
        // Custom dynamic widget output or filter hook
        $content = apply_filters("larapress_sidebar_{$sidebarArea}", '');

        if (!empty($content)) {
            return $content;
        }

        // Default placeholder fallback
        return '<div class="widget-area widget-' . e($sidebarArea) . '"><p class="text-muted small">Área de Widgets (' . e($sidebarArea) . ')</p></div>';
    }
}
