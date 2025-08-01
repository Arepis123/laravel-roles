<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleIndex extends Component
{
    public $selectedRoleId = null;

    public function render()
    {
        $roles = Role::with('permissions')->get();
        return view('livewire.roles.role-index', compact('roles'));
    }

    public function confirmDelete($id)
    {
        $this->selectedRoleId = $id;
    }    

    public function delete()
    {
        if ($this->selectedRoleId) {
            $role = Role::find($this->selectedRoleId);

            if ($role) {
                $role->delete();
                session()->flash('success', 'Role has been successfully deleted.');
            }

            $this->dispatch('close-modal', name: 'delete-role'); // optional
            $this->selectedRoleId = null;
        }
    }
}
