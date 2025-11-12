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
    public $positionFilter = ''; // Added position filter
    public $perPage = 15;

    // Delete Modal properties
    public $showDeleteModal = false;
    public $userToDelete = [];
    
    // Sorting properties
    public $sortField = 'status';
    public $sortDirection = 'asc';

    protected $queryString = ['search', 'statusFilter', 'roleFilter', 'positionFilter', 'sortField', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::with('roles')
            ->notDeleted() // Exclude deleted users
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
            })
            ->when($this->positionFilter, function ($q) {
                $q->where('position', $this->positionFilter);
            });

        // Apply sorting with custom position order
        if ($this->sortField === 'roles') {
            // Special handling for roles sorting
            $query->withCount('roles')
                  ->orderBy('roles_count', $this->sortDirection);
        } else {
            // Default multi-level sorting: Status (active first) -> Position (CEO->Manager->Executive->Non-executive)
            $query->orderByRaw("CASE
                    WHEN status = 'active' THEN 0
                    WHEN status = 'inactive' THEN 1
                    ELSE 2
                END ASC")
                  ->orderByRaw("CASE
                    WHEN position = 'CEO' THEN 1
                    WHEN position = 'Manager' THEN 2
                    WHEN position = 'Executive' THEN 3
                    WHEN position = 'Non-executive' THEN 4
                    ELSE 5
                END ASC");

            // If user manually sorts by a specific field, apply that as the primary sort
            if ($this->sortField !== 'status') {
                $query->orderBy($this->sortField, $this->sortDirection);
            }
        }

        $users = $query->paginate($this->perPage);
        
        $roles = \Spatie\Permission\Models\Role::pluck('name');
        $positions = User::getPositions();

        return view('livewire.users.user-index', compact('users', 'roles', 'positions'));
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
    
    public function confirmDelete($id, $name)
    {
        // Prevent deleting yourself
        if ($id == auth()->id()) {
            session()->flash("error", "You cannot delete your own account.");
            return;
        }

        $this->userToDelete = [
            'id' => $id,
            'name' => $name
        ];
        $this->showDeleteModal = true;
    }

    public function confirmDeleteUser()
    {
        $this->delete($this->userToDelete['id']);
        $this->cancelDelete();
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->userToDelete = [];
    }

    public function delete($id)
    {
        $user = User::find($id);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            session()->flash("error", "You cannot delete your own account.");
            return;
        }

        $user->softDelete();
        session()->flash("success", "User successfully deleted.");
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->roleFilter = '';
        $this->positionFilter = ''; // Reset position filter
        $this->sortField = 'status';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }
}