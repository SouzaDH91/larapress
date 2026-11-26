<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\HookManager;
use App\Services\OptionService;
use App\Services\PluginManager;
use App\Services\ThemeManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OptionService::class, fn () => new OptionService());
        $this->app->singleton(HookManager::class, fn () => new HookManager());
        $this->app->singleton(PluginManager::class, fn () => new PluginManager());
        $this->app->singleton(ThemeManager::class, fn () => new ThemeManager());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Custom Blade Directives
        Blade::directive('menu', function ($expression) {
            return "<?php echo \\App\\Services\\MenuRenderer::render({$expression}); ?>";
        });

        Blade::directive('dynamic_sidebar', function ($expression) {
            return "<?php echo \\App\\Services\\WidgetRenderer::render({$expression}); ?>";
        });

        // Boot active plugins & themes
        app(PluginManager::class)->bootActivePlugins();
        app(ThemeManager::class)->bootActiveTheme();
    }
}
