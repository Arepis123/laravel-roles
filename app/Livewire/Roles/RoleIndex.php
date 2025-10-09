<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleIndex extends Component
{
    public $selectedRoleId = null;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function updatedSearch()
    {
        // Reset to first page when search changes if using pagination
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $roles = Role::with('permissions')
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

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
