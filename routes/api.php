<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ContentApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - LaraPress CMS (Headless)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::get('/posts', [ContentApiController::class, 'posts']);
    Route::get('/posts/{slug}', [ContentApiController::class, 'post']);
    Route::get('/pages', [ContentApiController::class, 'pages']);
    Route::get('/media', [ContentApiController::class, 'media']);
});
