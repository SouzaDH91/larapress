<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Post;
use Livewire\Component;

class PageIndex extends PostIndex
{
    public function mount(string $type = 'page')
    {
        parent::mount('page');
    }
}
