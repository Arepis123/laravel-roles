<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleShow extends Component
{
    public $name, $role;
    public $permissions = [];
    public $allPermissions = [];

    public function mount($id)
    {
        $this->role = Role::find($id);
        $this->allPermissions = Permission::get();
        $this->name = $this->role->name;
        $this->permissions = $this->role->permissions()->pluck('name');
    }

    public function render()
    {
        return view('livewire.roles.role-show');
    }
}
