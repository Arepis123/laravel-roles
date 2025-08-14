<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserEdit extends Component
{
    public $user, $name, $email, $password, $confirm_password, $allRoles;
    public $roles = [];
    public $status; // Added status field

    public function mount($id)
    {
        $this->user = User::find($id);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->status = $this->user->status;
        $this->allRoles = Role::all();
        $this->roles = $this->user->roles()->pluck('name')->toArray();
    }

    public function render()
    {
        return view('livewire.users.user-edit');
    }

    public function submit()
    {
        $this->validate([
            "name" => "required",
            "email" => "required|email|unique:users,email,{$this->user->id}",
            "password" => "nullable|min:8|same:confirm_password",
            "status" => "required|in:active,inactive"
        ]);
        
        $this->user->name = $this->name;
        $this->user->email = $this->email;
        $this->user->status = $this->status;

        if($this->password){
            $this->user->password = Hash::make($this->password);
        }

        $this->user->save();

        $this->user->syncRoles($this->roles);

        return to_route("users.index")->with("success", "User successfully updated.");
    }
}
