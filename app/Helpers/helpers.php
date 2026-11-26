<?php

declare(strict_types=1);

use App\Services\HookManager;
use App\Services\OptionService;

if (!function_exists('get_option')) {
    function get_option(string $name, mixed $default = null): mixed
    {
        return app(OptionService::class)->get($name, $default);
    }
}

if (!function_exists('update_option')) {
    function update_option(string $name, mixed $value, bool $autoload = true): void
    {
        app(OptionService::class)->set($name, $value, $autoload);
    }
}

if (!function_exists('delete_option')) {
    function delete_option(string $name): bool
    {
        return app(OptionService::class)->delete($name);
    }
}

if (!function_exists('add_action')) {
    function add_action(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        app(HookManager::class)->addAction($tag, $callback, $priority, $acceptedArgs);
    }
}

if (!function_exists('do_action')) {
    function do_action(string $tag, ...$args): void
    {
        app(HookManager::class)->doAction($tag, ...$args);
    }
}

if (!function_exists('add_filter')) {
    function add_filter(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        app(HookManager::class)->addFilter($tag, $callback, $priority, $acceptedArgs);
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters(string $tag, mixed $value, ...$args): mixed
    {
        return app(HookManager::class)->applyFilters($tag, $value, ...$args);
    }
}

if (!function_exists('add_shortcode')) {
    function add_shortcode(string $tag, callable $callback): void
    {
        app(HookManager::class)->addShortcode($tag, $callback);
    }
}

if (!function_exists('do_shortcode')) {
    function do_shortcode(string $content): string
    {
        return app(HookManager::class)->parseShortcodes($content);
    }
}

if (!function_exists('theme_asset')) {
    function theme_asset(string $path, ?string $theme = null): string
    {
        $targetTheme = $theme ?? app(\App\Services\ThemeManager::class)->getActiveTheme();
        return route('theme.asset', [
            'theme' => $targetTheme,
            'file' => ltrim($path, '/'),
        ]);
    }
}
