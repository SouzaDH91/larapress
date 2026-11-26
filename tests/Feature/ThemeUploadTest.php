<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Services\ThemeManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ThemeUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_upload_and_install_theme_zip(): void
    {
        $zipPath = base_path('../nova-pulse.zip');
        $this->assertFileExists($zipPath);

        $admin = User::where('email', 'admin@larapress.test')->first();
        $this->actingAs($admin);

        $uploadedFile = new UploadedFile(
            $zipPath,
            'nova-pulse.zip',
            'application/zip',
            null,
            true
        );

        $themeManager = app(ThemeManager::class);
        $slug = $themeManager->installFromZip($uploadedFile);

        $this->assertEquals('nova-pulse', $slug);
        $this->assertDirectoryExists(base_path('themes/nova-pulse'));
        $this->assertFileExists(base_path('themes/nova-pulse/theme.json'));
        $this->assertFileExists(base_path('themes/nova-pulse/css/style.css'));

        // Ativar tema
        $themeManager->setActiveTheme('nova-pulse');
        $this->assertEquals('nova-pulse', $themeManager->getActiveTheme());

        // Testar rota de asset do tema
        $response = $this->get('/themes/nova-pulse/css/style.css');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/css; charset=UTF-8');

        // Testar asset de script
        $jsResponse = $this->get('/themes/nova-pulse/js/theme.js');
        $jsResponse->assertStatus(200);
        $jsResponse->assertHeader('Content-Type', 'application/javascript; charset=UTF-8');

        // Testar renderização da home com novo tema
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('NovaPulse');
        $homeResponse->assertSee('reading-progress');

        // Restaurar estado mantendo nova-pulse instalado
        $themeManager->setActiveTheme('nova-pulse');
        $this->assertDirectoryExists(base_path('themes/nova-pulse'));
    }
}
