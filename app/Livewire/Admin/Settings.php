<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Services\OptionService;
use App\Services\PermalinkService;
use Livewire\Component;

class Settings extends Component
{
    public string $site_name = '';
    public string $site_description = '';
    public string $admin_email = '';
    public string $timezone = 'America/Sao_Paulo';
    public bool $comments_moderation = true;

    // Permalinks
    public string $permalink_type = 'post_name';
    public string $permalink_structure = '/%postname%/';
    public string $custom_permalink_structure = '/%year%/%month%/%postname%/';

    public function mount()
    {
        $this->site_name = (string) get_option('site_name', 'LaraPress CMS');
        $this->site_description = (string) get_option('site_description', '');
        $this->admin_email = (string) get_option('admin_email', 'admin@larapress.test');
        $this->timezone = (string) get_option('timezone', 'America/Sao_Paulo');
        $this->comments_moderation = (bool) get_option('comments_moderation', true);

        $savedStructure = (string) get_option('permalink_structure', '/%postname%/');
        $this->permalink_structure = $savedStructure;

        // Detect matching preset
        $matched = false;
        foreach (PermalinkService::PRESETS as $key => $preset) {
            if ($key !== 'custom' && $preset['structure'] === $savedStructure) {
                $this->permalink_type = $key;
                $matched = true;
                break;
            }
        }

        if (!$matched) {
            $this->permalink_type = 'custom';
            $this->custom_permalink_structure = $savedStructure;
        }
    }

    public function updatedPermalinkType($value)
    {
        if ($value !== 'custom' && isset(PermalinkService::PRESETS[$value])) {
            $this->permalink_structure = PermalinkService::PRESETS[$value]['structure'];
        } elseif ($value === 'custom') {
            $this->permalink_structure = $this->custom_permalink_structure;
        }
    }

    public function insertTag(string $tag)
    {
        $this->permalink_type = 'custom';
        $current = rtrim($this->custom_permalink_structure, '/');
        $this->custom_permalink_structure = $current . '/' . $tag . '/';
        $this->permalink_structure = $this->custom_permalink_structure;
    }

    public function save()
    {
        $this->validate([
            'site_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
        ]);

        $finalStructure = $this->permalink_type === 'custom'
            ? $this->custom_permalink_structure
            : ($this->permalink_structure ?: '/%postname%/');

        // Normalizar com barras
        if (!empty($finalStructure) && !str_starts_with($finalStructure, '/?')) {
            $finalStructure = '/' . trim($finalStructure, '/') . '/';
        }

        update_option('site_name', $this->site_name);
        update_option('site_description', $this->site_description);
        update_option('admin_email', $this->admin_email);
        update_option('timezone', $this->timezone);
        update_option('permalink_structure', $finalStructure);
        update_option('comments_moderation', $this->comments_moderation);

        $this->permalink_structure = $finalStructure;

        session()->flash('success', 'Configurações e estrutura de Permalinks salvas com sucesso!');
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'presets' => PermalinkService::PRESETS,
        ])->layout('admin.layouts.master');
    }
}
