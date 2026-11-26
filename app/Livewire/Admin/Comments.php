<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Comment;
use Livewire\Component;
use Livewire\WithPagination;

class Comments extends Component
{
    use WithPagination;

    public function updateStatus(string $id, string $status)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['status' => $status]);
        session()->flash('success', 'Status do comentário atualizado!');
    }

    public function delete(string $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        session()->flash('success', 'Comentário excluído!');
    }

    public function render()
    {
        $comments = Comment::with('post')->latest()->paginate(15);

        return view('livewire.admin.comments', [
            'comments' => $comments,
        ])->layout('admin.layouts.master');
    }
}
