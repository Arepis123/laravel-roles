<?php
namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserCreate extends Component
{
    public $name, $email, $password, $confirm_password, $allRoles;
    public $selectedRole; // Changed from $roles array
    public $status = 'active';
    public $position = 'Non-executive';

    public function mount()
    {
        $this->allRoles = Role::all();
        // Set default role to 'User' if it exists
        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $this->selectedRole = $userRole->name;
        }
    }

    public function render()
    {
        $positions = User::getPositions();
        return view('livewire.users.user-create', compact('positions'));
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'selectedRole' => 'required',
            'password' => 'required|min:8|same:confirm_password',
            'status' => 'required|in:active,inactive',
            'position' => 'required|in:CEO,Manager,Executive,Non-executive'
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'status' => $this->status,
            'position' => $this->position
        ]);

        // Assign the single selected role
        $user->assignRole($this->selectedRole);

        return to_route('users.index')->with('success', 'User has been successfully created');
    }
}