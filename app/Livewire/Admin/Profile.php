<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Profile extends Component
{
    public string $name = '';
    public string $email = '';
    public string $username = '';
    public string $bio = '';
    public string $current_password = '';
    public string $new_password = '';

    public function mount()
    {
        $user = auth()->user();
        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
            $this->username = $user->username ?? '';
            $this->bio = $user->bio ?? '';
        }
    }

    public function save()
    {
        $user = auth()->user();

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'username' => "nullable|string|unique:users,username,{$user->id}",
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'bio' => $this->bio,
        ]);

        if (!empty($this->new_password)) {
            $this->validate([
                'current_password' => 'required|current_password',
                'new_password' => 'required|min:6',
            ]);

            $user->update([
                'password' => Hash::make($this->new_password),
            ]);

            $this->reset(['current_password', 'new_password']);
        }

        session()->flash('success', 'Perfil atualizado com sucesso!');
    }

    public function render()
    {
        return view('livewire.admin.profile')->layout('admin.layouts.master');
    }
}
