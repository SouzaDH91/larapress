<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Services\PluginManager;
use Livewire\Component;
use Livewire\WithFileUploads;

class Plugins extends Component
{
    use WithFileUploads;

    public $zipFile = null;
    public string $search = '';
    public string $filter = 'all'; // all, active, inactive

    public function togglePlugin(string $slug, bool $activate)
    {
        $pluginManager = app(PluginManager::class);
        if ($activate) {
            $pluginManager->activate($slug);
            session()->flash('success', "Plugin '{$slug}' ativado com sucesso!");
        } else {
            $pluginManager->deactivate($slug);
            session()->flash('success', "Plugin '{$slug}' desativado!");
        }
    }

    public function installZip()
    {
        $this->validate([
            'zipFile' => 'required|file|mimes:zip|max:51200', // 50MB
        ]);

        try {
            $pluginManager = app(PluginManager::class);
            $installedFolder = $pluginManager->installFromZip($this->zipFile);
            $this->zipFile = null;
            session()->flash('success', "Plugin instalado com sucesso! Você pode ativá-lo na lista abaixo.");
        } catch (\Throwable $e) {
            session()->flash('error', 'Falha ao instalar o plugin: ' . $e->getMessage());
        }
    }

    public function deletePlugin(string $slug)
    {
        $pluginManager = app(PluginManager::class);
        // Primeiro desativar se estiver ativo
        $pluginManager->deactivate($slug);
        $pluginManager->delete($slug);

        session()->flash('success', "Plugin '{$slug}' excluído permanentemente com sucesso!");
    }

    public function render()
    {
        $pluginManager = app(PluginManager::class);
        $allPlugins = $pluginManager->getInstalledPlugins();

        // Filtrar por busca e status
        $plugins = array_filter($allPlugins, function ($p) {
            if ($this->filter === 'active' && !$p['is_active']) return false;
            if ($this->filter === 'inactive' && $p['is_active']) return false;

            if (!empty($this->search)) {
                $term = strtolower($this->search);
                return str_contains(strtolower($p['name']), $term) || str_contains(strtolower($p['description']), $term);
            }

            return true;
        });

        $activeCount = count(array_filter($allPlugins, fn ($p) => $p['is_active']));
        $inactiveCount = count($allPlugins) - $activeCount;

        return view('livewire.admin.plugins', [
            'plugins' => $plugins,
            'totalCount' => count($allPlugins),
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
        ])->layout('admin.layouts.master');
    }
}
