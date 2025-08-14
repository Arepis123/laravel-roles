<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $roleFilter = '';
    public $perPage = 10;
    
    // Sorting properties
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = ['search', 'statusFilter', 'roleFilter', 'sortField', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::with('roles')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->roleFilter, function ($q) {
                $q->whereHas('roles', function ($query) {
                    $query->where('name', $this->roleFilter);
                });
            });

        // Apply sorting
        if ($this->sortField === 'roles') {
            // Special handling for roles sorting
            $query->withCount('roles')
                  ->orderBy('roles_count', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $users = $query->paginate($this->perPage);
        
        $roles = \Spatie\Permission\Models\Role::pluck('name');

        return view('livewire.users.user-index', compact('users', 'roles'));
    }
    
    public function sort($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        
        $this->resetPage();
    }
    
    public function toggleStatus($id)
    {
        $user = User::find($id);
        $user->toggleStatus();
        
        $statusText = $user->status === 'active' ? 'activated' : 'deactivated';
        session()->flash("success", "User successfully {$statusText}.");
    }
    
    public function delete($id)
    {
        $user = User::find($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            session()->flash("error", "You cannot delete your own account.");
            return;
        }
        
        $user->delete();
        session()->flash("success", "User successfully deleted.");
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->roleFilter = '';
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }
}