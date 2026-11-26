<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Comment;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Livewire\Component;

final class Dashboard extends Component
{
    public function render()
    {
        $postsCount = Post::where('type', 'post')->count();
        $pagesCount = Post::where('type', 'page')->count();
        $commentsCount = Comment::count();
        $mediaCount = Media::count();
        $usersCount = User::count();

        $recentPosts = Post::where('type', 'post')->with('author')->latest()->take(5)->get();
        $recentComments = Comment::with('post')->latest()->take(5)->get();

        return view('livewire.admin.dashboard', [
            'postsCount' => $postsCount,
            'pagesCount' => $pagesCount,
            'commentsCount' => $commentsCount,
            'mediaCount' => $mediaCount,
            'usersCount' => $usersCount,
            'recentPosts' => $recentPosts,
            'recentComments' => $recentComments,
        ])->layout('admin.layouts.master');
    }
}
