<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserCreate extends Component
{
    public $name, $email, $password, $confirm_password, $allRoles;
    public $roles = [];
    public $status = 'active'; // Added status field

    public function mount()
    {
        $this->allRoles = Role::all();
        // Set default role to 'User' if it exists
        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $this->roles = [$userRole->name];
        }
    }

    public function render()
    {
        return view('livewire.users.user-create');
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required',
            'password' => 'required|min:8|same:confirm_password',
            'status' => 'required|in:active,inactive'
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'status' => $this->status
        ]);

        $user->syncRoles($this->roles);

        return to_route('users.index')->with('success', 'User has been successfully created');
    }
}
