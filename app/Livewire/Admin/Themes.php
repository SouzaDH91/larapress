<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Services\ThemeManager;
use Livewire\Component;
use Livewire\WithFileUploads;

final class Themes extends Component
{
    use WithFileUploads;

    public $zipFile = null;

    public function activateTheme(string $slug): void
    {
        $themeManager = app(ThemeManager::class);
        if ($themeManager->setActiveTheme($slug)) {
            session()->flash('success', "Tema '{$slug}' ativado com sucesso!");
        } else {
            session()->flash('error', "Erro ao ativar tema.");
        }
    }

    public function installZip(): void
    {
        $this->validate([
            'zipFile' => 'required|file|mimes:zip|max:51200', // max 50MB
        ]);

        try {
            $themeManager = app(ThemeManager::class);
            $installedFolder = $themeManager->installFromZip($this->zipFile);
            $this->zipFile = null;
            session()->flash('success', "Tema '{$installedFolder}' instalado com sucesso! Você pode ativá-lo na lista abaixo.");
        } catch (\Throwable $e) {
            session()->flash('error', 'Falha ao instalar o tema: ' . $e->getMessage());
        }
    }

    public function deleteTheme(string $slug): void
    {
        try {
            $themeManager = app(ThemeManager::class);
            $themeManager->deleteTheme($slug);
            session()->flash('success', "Tema '{$slug}' excluído com sucesso!");
        } catch (\Throwable $e) {
            session()->flash('error', 'Erro ao excluir tema: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $themeManager = app(ThemeManager::class);
        $themes = $themeManager->getInstalledThemes();

        return view('livewire.admin.themes', [
            'themes' => $themes,
            'activeTheme' => $themeManager->getActiveTheme(),
        ])->layout('admin.layouts.master');
    }
}
