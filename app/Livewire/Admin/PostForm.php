<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Post;
use App\Models\PostRevision;
use App\Models\TermTaxonomy;
use Illuminate\Support\Str;
use Livewire\Component;

class PostForm extends Component
{
    public ?Post $post = null;
    public string $postId = '';
    public string $type = 'post';

    public string $title = '';
    public string $slug = '';
    public string $excerpt = '';
    public string $content_html = '';
    public string $status = 'draft';
    public array $selectedTerms = [];

    // Controle de Prévia (Preview Modal)
    public bool $showPreview = false;

    public function mount(?string $id = null, string $type = 'post')
    {
        $this->type = $type;

        if ($id) {
            $this->post = Post::with(['termTaxonomies', 'revisions'])->findOrFail($id);
            $this->postId = $this->post->id;
            $this->title = $this->post->title;
            $this->slug = $this->post->slug;
            $this->excerpt = $this->post->excerpt ?? '';
            $this->content_html = $this->post->content_html ?? '';
            $this->status = $this->post->status;
            $this->selectedTerms = $this->post->termTaxonomies->pluck('id')->toArray();
        }
    }

    public function updatedTitle($value)
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
    }

    public function restoreRevision(string $revisionId)
    {
        $rev = PostRevision::findOrFail($revisionId);
        $this->title = $rev->title;
        if (isset($rev->content_json['html'])) {
            $this->content_html = $rev->content_json['html'];
        }
        session()->flash('success', 'Revisão restaurada no editor! Lembre-se de salvar.');
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'status' => 'required|in:draft,pending,published,private',
        ]);

        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->slug),
            'excerpt' => $this->excerpt,
            'content_html' => $this->content_html,
            'content_json' => ['html' => $this->content_html],
            'status' => $this->status,
            'type' => $this->type,
            'author_id' => auth()->id() ?? \App\Models\User::first()->id,
            'published_at' => $this->status === 'published' ? now() : null,
        ];

        if ($this->post) {
            $this->post->update($data);

            // Gravar revisão histórica
            PostRevision::create([
                'post_id' => $this->post->id,
                'user_id' => auth()->id(),
                'title' => $this->title,
                'content_json' => ['html' => $this->content_html],
                'created_at' => now(),
            ]);

            $post = $this->post;
        } else {
            $post = Post::create($data);

            PostRevision::create([
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'title' => $this->title,
                'content_json' => ['html' => $this->content_html],
                'created_at' => now(),
            ]);
        }

        if ($this->type === 'post') {
            $post->termTaxonomies()->sync($this->selectedTerms);
        }

        session()->flash('success', ($this->type === 'page' ? 'Página' : 'Artigo') . ' salvo com sucesso!');
        return redirect()->route('admin.' . ($this->type === 'page' ? 'pages' : 'posts'));
    }

    public function render()
    {
        $categories = TermTaxonomy::where('taxonomy', 'category')->with('term')->get();

        return view('livewire.admin.post-form', [
            'categories' => $categories,
            'typeName' => $this->type === 'page' ? 'Página' : 'Artigo',
        ])->layout('admin.layouts.master');
    }
}
