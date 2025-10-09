<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleCreate extends Component
{
    public $name;
    public $permissions = [];
    public $allPermissions = [];

    public function mount()
    {
        $this->allPermissions = Permission::get();
    }

    public function render()
    {
        return view('livewire.roles.role-create');
    }

    public function selectAll()
    {
        $this->permissions = $this->allPermissions->pluck('name')->toArray();
    }

    public function deselectAll()
    {
        $this->permissions = [];
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required',
        ]);

        $role = Role::create([
            'name' => $this->name
        ]);

        $role->syncPermissions($this->permissions);

        return to_route('roles.index')->with('success', 'Role has been successfully created');
    }
}
