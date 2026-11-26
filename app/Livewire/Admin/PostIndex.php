<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Post;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class PostIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $type = 'post';

    public function mount(string $type = 'post')
    {
        $this->type = $type;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete(string $id)
    {
        $post = Post::where('type', $this->type)->findOrFail($id);
        $post->delete();
        session()->flash('success', 'Registro movido para a lixeira com sucesso!');
    }

    public function render()
    {
        $posts = Post::where('type', $this->type)
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->with(['author', 'termTaxonomies.term'])
            ->latest()
            ->paginate(10);

        return view('livewire.admin.post-index', [
            'posts' => $posts,
            'typeName' => $this->type === 'page' ? 'Páginas' : 'Artigos',
        ])->layout('admin.layouts.master');
    }
}
