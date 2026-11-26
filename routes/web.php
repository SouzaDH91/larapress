<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\DynamicRouteController;
use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Comments;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\MediaLibrary;
use App\Livewire\Admin\Menus;
use App\Livewire\Admin\Plugins;
use App\Livewire\Admin\PostForm;
use App\Livewire\Admin\PostIndex;
use App\Livewire\Admin\Profile;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\Themes;
use App\Livewire\Admin\Users;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - LaraPress CMS
|--------------------------------------------------------------------------
*/

// SEO & Feeds
Route::get('/sitemap.xml', [DynamicRouteController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [DynamicRouteController::class, 'robots'])->name('robots');
Route::get('/feed', [DynamicRouteController::class, 'feed'])->name('feed');

// Frontend Comments
Route::post('/comment/{post}', [DynamicRouteController::class, 'postComment'])->name('frontend.comment.submit');

// Fallback named login route
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

// Admin Auth Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected Backoffice Routes (Velzon Theme)
    Route::middleware('auth')->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');
        
        // Posts CRUD (Author, Editor, Admin)
        Route::middleware('permission:edit_posts')->group(function () {
            Route::get('/posts', fn () => redirect()->route('admin.posts.index'));
            Route::get('/posts/list', PostIndex::class)->name('posts');
            Route::get('/posts/index', PostIndex::class)->name('posts.index');
            Route::get('/posts/create', PostForm::class)->name('posts.create');
            Route::get('/posts/{id}/edit', PostForm::class)->name('posts.edit');
        });

        // Pages CRUD (Editor, Admin)
        Route::middleware('permission:edit_pages')->group(function () {
            Route::get('/pages', fn () => redirect()->route('admin.pages.index'));
            Route::get('/pages/list', \App\Livewire\Admin\PageIndex::class)->name('pages');
            Route::get('/pages/index', \App\Livewire\Admin\PageIndex::class)->name('pages.index');
            Route::get('/pages/create', \App\Livewire\Admin\PageForm::class)->name('pages.create');
            Route::get('/pages/{id}/edit', \App\Livewire\Admin\PageForm::class)->name('pages.edit');
        });

        // Taxonomies (Editor, Admin)
        Route::middleware('permission:manage_categories')->group(function () {
            Route::get('/categories', Categories::class)->name('categories');
            Route::get('/tags', \App\Livewire\Admin\Tags::class)->name('tags');
        });

        // Media (Author, Editor, Admin)
        Route::middleware('permission:upload_files')->group(function () {
            Route::get('/media', MediaLibrary::class)->name('media');
        });

        // Comments (Editor, Admin)
        Route::middleware('permission:moderate_comments')->group(function () {
            Route::get('/comments', Comments::class)->name('comments');
        });

        // Appearance & Menus (Admin)
        Route::middleware('permission:switch_themes')->group(function () {
            Route::get('/themes', Themes::class)->name('themes');
        });
        Route::middleware('permission:edit_theme_options')->group(function () {
            Route::get('/menus', Menus::class)->name('menus');
        });

        // Plugins (Admin)
        Route::middleware('permission:activate_plugins')->group(function () {
            Route::get('/plugins', Plugins::class)->name('plugins');
        });

        // Users (Admin)
        Route::middleware('permission:list_users')->group(function () {
            Route::get('/users', Users::class)->name('users');
        });

        // Settings (Admin)
        Route::middleware('permission:manage_options')->group(function () {
            Route::get('/settings', Settings::class)->name('settings');
        });

        // Profile (Qualquer usuário autenticado)
        Route::get('/profile', Profile::class)->name('profile');
    });
});

// Frontend Public Routes
Route::get('/', [DynamicRouteController::class, 'home'])->name('home');

// Theme Assets Delivery Route
Route::get('/themes/{theme}/{file}', [DynamicRouteController::class, 'themeAsset'])
    ->where('file', '.*')
    ->name('theme.asset');

// Dynamic Permalink Catch-all Route (supports slashes like /2026/11/slug)
Route::get('/{slug}', [DynamicRouteController::class, 'resolve'])->where('slug', '^(?!admin|api|livewire|storage|login|feed|robots\.txt|sitemap\.xml).*$')->name('permalink');
