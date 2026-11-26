<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use ZipArchive;

class ThemeManager
{
    protected string $themesPath;

    public function __construct()
    {
        $this->themesPath = base_path('themes');
        if (!File::isDirectory($this->themesPath)) {
            File::makeDirectory($this->themesPath, 0755, true);
        }
    }

    /**
     * Boot and register active theme namespace and routes.
     */
    public function bootActiveTheme(): void
    {
        $activeTheme = get_option('active_theme', 'laratwentysix');
        $themeDir = $this->themesPath . DIRECTORY_SEPARATOR . $activeTheme;

        if (File::isDirectory($themeDir)) {
            View::addNamespace('theme', $themeDir);
        }
    }

    /**
     * Get active theme name.
     */
    public function getActiveTheme(): string
    {
        return (string) get_option('active_theme', 'laratwentysix');
    }

    /**
     * Set active theme.
     */
    public function setActiveTheme(string $themeSlug): bool
    {
        $themeDir = $this->themesPath . DIRECTORY_SEPARATOR . $themeSlug;
        if (!File::isDirectory($themeDir)) {
            return false;
        }

        update_option('active_theme', $themeSlug);
        View::replaceNamespace('theme', $themeDir);
        return true;
    }

    /**
     * List all installed themes.
     */
    public function getInstalledThemes(): array
    {
        $themes = [];
        $directories = File::directories($this->themesPath);

        foreach ($directories as $dir) {
            $slug = basename($dir);
            $manifestPath = $dir . DIRECTORY_SEPARATOR . 'theme.json';

            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true) ?? [];
                
                $screenshot = null;
                foreach (['screenshot.png', 'screenshot.jpg', 'screenshot.svg', 'screenshot.webp'] as $shotFile) {
                    if (File::exists($dir . DIRECTORY_SEPARATOR . $shotFile)) {
                        $screenshot = route('theme.asset', ['theme' => $slug, 'file' => $shotFile]);
                        break;
                    }
                }

                $themes[] = [
                    'slug' => $slug,
                    'name' => $manifest['name'] ?? $slug,
                    'description' => $manifest['description'] ?? '',
                    'version' => $manifest['version'] ?? '1.0.0',
                    'author' => $manifest['author'] ?? 'LaraPress Team',
                    'screenshot' => $screenshot,
                    'is_active' => $slug === $this->getActiveTheme(),
                ];
            }
        }

        return $themes;
    }

    /**
     * Delete an uninstalled/inactive theme folder.
     */
    public function deleteTheme(string $slug): bool
    {
        if ($slug === $this->getActiveTheme()) {
            throw new \RuntimeException('Não é possível excluir o tema atualmente ativo.');
        }

        $dir = $this->themesPath . DIRECTORY_SEPARATOR . $slug;
        if (File::isDirectory($dir)) {
            File::deleteDirectory($dir);
            return true;
        }

        return false;
    }

    /**
     * Securely install a theme from an uploaded ZIP archive.
     */
    public function installFromZip(UploadedFile $zipFile): string
    {
        if (strtolower($zipFile->getClientOriginalExtension()) !== 'zip') {
            throw new \InvalidArgumentException('O arquivo enviado deve ser um pacote .ZIP válido.');
        }

        $zip = new ZipArchive();
        $res = $zip->open($zipFile->getRealPath());

        if ($res !== true) {
            throw new \RuntimeException('Não foi possível abrir o arquivo ZIP.');
        }

        // 1. Validar estrutura do ZIP contra arquivos maliciosos ou directory traversal
        $hasManifest = false;
        $targetFolderName = null;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);

            // Bloquear directory traversal
            if (str_contains($entry, '../') || str_contains($entry, '..\\')) {
                $zip->close();
                throw new \RuntimeException('Arquivo ZIP com tentativa de Directory Traversal detectado.');
            }

            // Bloquear arquivos executáveis perigosos
            $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
            if (in_array($ext, ['sh', 'exe', 'bat', 'cmd', 'bin', 'phtml', 'phar'])) {
                $zip->close();
                throw new \RuntimeException("Arquivo executável perigoso detectado no ZIP: {$entry}");
            }

            if (basename($entry) === 'theme.json') {
                $hasManifest = true;
                $parts = explode('/', str_replace('\\', '/', $entry));
                if (count($parts) > 1) {
                    $targetFolderName = $parts[0];
                }
            }
        }

        if (!$hasManifest) {
            $zip->close();
            throw new \InvalidArgumentException('O pacote ZIP é inválido: nenhum manifesto theme.json foi encontrado.');
        }

        // 2. Extrair com segurança para o diretório de temas
        $zip->extractTo($this->themesPath);
        $zip->close();

        if (!$targetFolderName) {
            $targetFolderName = pathinfo($zipFile->getClientOriginalName(), PATHINFO_FILENAME);
        }

        return $targetFolderName;
    }
}
