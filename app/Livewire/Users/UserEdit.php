<?php
namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserEdit extends Component
{
    public $user, $name, $email, $password, $confirm_password, $allRoles;
    public $selectedRole;
    public $status;
    public $position;
    public $returnPage = 1; // Store the page to return to

    public function mount($id)
    {
        $this->user = User::find($id);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->status = $this->user->status;
        $this->position = $this->user->position;
        $this->allRoles = Role::all();

        // Get the first role (since user can only have one now)
        $this->selectedRole = $this->user->roles()->first()?->name;

        // Capture the page number from query string
        $this->returnPage = request()->query('page', 1);
    }

    public function render()
    {
        $positions = User::getPositions();
        return view('livewire.users.user-edit', compact('positions'));
    }

    public function submit()
    {
        $this->validate([
            "name" => "required",
            "email" => "required|email|unique:users,email,{$this->user->id}",
            "password" => "nullable|min:8|same:confirm_password",
            "status" => "required|in:active,inactive",
            "position" => "required|in:CEO,Manager,Executive,Non-executive",
            "selectedRole" => "required"
        ]);
        
        $this->user->name = $this->name;
        $this->user->email = $this->email;
        $this->user->status = $this->status;
        $this->user->position = $this->position;

        if($this->password){
            $this->user->password = Hash::make($this->password);
        }

        $this->user->save();

        // Sync the single selected role
        $this->user->syncRoles([$this->selectedRole]);

        return to_route("users.index", ['page' => $this->returnPage])->with("success", "User successfully updated.");
    }
}