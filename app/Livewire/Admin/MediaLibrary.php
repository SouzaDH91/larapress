<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Media;
use App\Services\MediaService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MediaLibrary extends Component
{
    use WithFileUploads, WithPagination;

    public $uploads = [];
    public string $search = '';

    public function updatedUploads()
    {
        $this->validate([
            'uploads.*' => 'required|file|max:20480', // 20MB max
        ]);

        $mediaService = app(MediaService::class);
        foreach ($this->uploads as $file) {
            try {
                $mediaService->upload($file, auth()->user());
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
            }
        }

        $this->uploads = [];
        session()->flash('success', 'Arquivos enviados e processados em WebP com sucesso!');
    }

    public function delete(string $id)
    {
        $media = Media::findOrFail($id);
        $media->delete();
        session()->flash('success', 'Arquivo removido!');
    }

    public function render()
    {
        $mediaList = Media::when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(18);

        return view('livewire.admin.media-library', [
            'mediaList' => $mediaList,
        ])->layout('admin.layouts.master');
    }
}
