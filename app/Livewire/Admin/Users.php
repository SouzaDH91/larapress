<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public string $name = '';
    public string $email = '';
    public string $username = '';
    public string $password = '';
    public string $role = 'subscriber';

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'status' => 'active',
        ]);

        $user->assignRole($this->role);

        $this->reset(['name', 'email', 'username', 'password']);
        session()->flash('success', 'Usuário criado com sucesso!');
    }

    public function render()
    {
        $users = User::with('roles')->latest()->paginate(10);
        $roles = Role::all();

        return view('livewire.admin.users', [
            'users' => $users,
            'roles' => $roles,
        ])->layout('admin.layouts.master');
    }
}
