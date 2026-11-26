<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use Livewire\Component;

class PageForm extends PostForm
{
    public function mount(?string $id = null, string $type = 'page')
    {
        parent::mount($id, 'page');
    }
}
