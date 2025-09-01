<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;

class UseShow extends Component
{
    public $user;

    public function mount($id)
    {
        $this->user = User::find($id);
        
        if (!$this->user) {
            abort(404, 'User not found');
        }
    }

    public function render()
    {
        return view('livewire.users.use-show');
    }

    public function toggleStatus($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            session()->flash("error", "User not found.");
            return;
        }
        
        $user->toggleStatus();
        
        // Refresh the current user data
        $this->user = $user->fresh();
        
        $statusText = $user->status === 'active' ? 'activated' : 'deactivated';
        session()->flash("success", "User successfully {$statusText}.");
    }
    
    public function delete($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            session()->flash("error", "User not found.");
            return;
        }
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            session()->flash("error", "You cannot delete your own account.");
            return;
        }
        
        $userName = $user->name;
        $user->delete();
        
        session()->flash("success", "User {$userName} successfully deleted.");
        
        // Redirect to users index after deletion
        return to_route('users.index');
    }
}