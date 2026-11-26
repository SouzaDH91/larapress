<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Plugin;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use ZipArchive;

class PluginManager
{
    protected string $pluginPath;

    public function __construct()
    {
        $this->pluginPath = base_path('plugins');
        if (!File::isDirectory($this->pluginPath)) {
            File::makeDirectory($this->pluginPath, 0755, true);
        }
    }

    /**
     * Boot all active plugins.
     */
    public function bootActivePlugins(): void
    {
        try {
            $activePlugins = Plugin::where('is_active', true)->get();
        } catch (\Throwable $e) {
            return;
        }

        foreach ($activePlugins as $plugin) {
            $dir = $this->pluginPath . DIRECTORY_SEPARATOR . $plugin->slug;
            $manifestPath = $dir . DIRECTORY_SEPARATOR . 'plugin.json';

            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true);

                // Register blade views namespace if views directory exists
                $viewsPath = $dir . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';
                if (File::isDirectory($viewsPath)) {
                    View::addNamespace($plugin->slug, $viewsPath);
                }

                // Register ServiceProvider if declared
                $providerClass = $manifest['provider'] ?? null;
                if ($providerClass && class_exists($providerClass)) {
                    app()->register($providerClass);
                }
            }
        }
    }

    /**
     * Get list of all installed plugins on disk.
     */
    public function getInstalledPlugins(): array
    {
        $plugins = [];
        $directories = File::directories($this->pluginPath);

        foreach ($directories as $dir) {
            $slug = basename($dir);
            $manifestPath = $dir . DIRECTORY_SEPARATOR . 'plugin.json';

            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true) ?? [];
                $dbPlugin = Plugin::where('slug', $slug)->first();

                $plugins[] = [
                    'slug' => $slug,
                    'name' => $manifest['name'] ?? $slug,
                    'description' => $manifest['description'] ?? '',
                    'version' => $manifest['version'] ?? '1.0.0',
                    'author' => $manifest['author'] ?? 'Unknown',
                    'is_active' => $dbPlugin?->is_active ?? false,
                ];
            }
        }

        return $plugins;
    }

    /**
     * Activate a plugin.
     */
    public function activate(string $slug): bool
    {
        $dir = $this->pluginPath . DIRECTORY_SEPARATOR . $slug;
        $manifestPath = $dir . DIRECTORY_SEPARATOR . 'plugin.json';

        if (!File::exists($manifestPath)) {
            return false;
        }

        $manifest = json_decode(File::get($manifestPath), true);
        Plugin::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $manifest['name'] ?? $slug,
                'version' => $manifest['version'] ?? '1.0.0',
                'is_active' => true,
            ]
        );

        return true;
    }

    /**
     * Deactivate a plugin.
     */
    public function deactivate(string $slug): bool
    {
        $plugin = Plugin::where('slug', $slug)->first();
        if ($plugin) {
            $plugin->update(['is_active' => false]);
            return true;
        }

        return false;
    }

    /**
     * Delete an uninstalled/deactivated plugin folder and record.
     */
    public function delete(string $slug): bool
    {
        $dir = $this->pluginPath . DIRECTORY_SEPARATOR . $slug;
        if (File::isDirectory($dir)) {
            File::deleteDirectory($dir);
        }

        Plugin::where('slug', $slug)->delete();
        return true;
    }

    /**
     * Securely install a plugin from an uploaded ZIP archive.
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
                throw new \SecurityException('Arquivo ZIP com tentativa de Directory Traversal detectado.');
            }

            // Bloquear arquivos executáveis soltos
            $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
            if (in_array($ext, ['sh', 'exe', 'bat', 'cmd', 'bin', 'phtml'])) {
                $zip->close();
                throw new \SecurityException("Arquivo executável perigoso detectado no ZIP: {$entry}");
            }

            if (basename($entry) === 'plugin.json') {
                $hasManifest = true;
                $parts = explode('/', str_replace('\\', '/', $entry));
                if (count($parts) > 1) {
                    $targetFolderName = $parts[0];
                }
            }
        }

        if (!$hasManifest) {
            $zip->close();
            throw new \InvalidArgumentException('O pacote ZIP é inválido: nenhum manifesto plugin.json foi encontrado.');
        }

        // 2. Extrair com segurança para o diretório de plugins
        $zip->extractTo($this->pluginPath);
        $zip->close();

        // Se o plugin foi compactado sem pasta raiz, detectar pasta onde o plugin.json foi parar
        if (!$targetFolderName) {
            $targetFolderName = pathinfo($zipFile->getClientOriginalName(), PATHINFO_FILENAME);
        }

        return $targetFolderName;
    }
}
