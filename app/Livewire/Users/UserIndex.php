<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;

class UserIndex extends Component
{
    public function render()
    {
        $users = User::get();

        return view('livewire.users.user-index', compact('users'));
    }
    
    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();

        session()->flash("success", "User succesfully deleted.");
    }
}
