<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Booking;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookingIndex extends Component
{
    use AuthorizesRequests, WithPagination;

    public $selectedBookingId = null;
    public $statusFilter = '';
    public $assetTypeFilter = '';
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'assetTypeFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function render()
    {
        $bookings = $this->getBookingsQuery();
        
        return view('livewire.bookings.booking-index', compact('bookings'));
    }

    private function getBookingsQuery()
    {
        $query = Booking::with(['user', 'asset']);

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                // Search in user data
                $q->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                // Search by booking ID
                ->orWhere('id', 'like', '%' . $this->search . '%')
                // Search in vehicles (has model and plate_number, no name)
                ->orWhere(function ($assetQuery) {
                    $assetQuery->where('asset_type', 'vehicle')
                               ->whereHasMorph('asset', 'App\Models\Vehicle', function ($vehicleQuery) {
                                   $vehicleQuery->where('model', 'like', '%' . $this->search . '%')
                                              ->orWhere('plate_number', 'like', '%' . $this->search . '%');
                               });
                })
                // Search in meeting rooms (has name, no model or plate_number)
                ->orWhere(function ($assetQuery) {
                    $assetQuery->where('asset_type', 'meeting_room')
                               ->whereHasMorph('asset', 'App\Models\MeetingRoom', function ($roomQuery) {
                                   $roomQuery->where('name', 'like', '%' . $this->search . '%')
                                           ->orWhere('location', 'like', '%' . $this->search . '%');
                               });
                })
                // Search in IT assets (has name and asset_tag, no model or plate_number)
                ->orWhere(function ($assetQuery) {
                    $assetQuery->where('asset_type', 'it_asset')
                               ->whereHasMorph('asset', 'App\Models\ItAsset', function ($itQuery) {
                                   $itQuery->where('name', 'like', '%' . $this->search . '%')
                                          ->orWhere('asset_tag', 'like', '%' . $this->search . '%')
                                          ->orWhere('location', 'like', '%' . $this->search . '%');
                               });
                });
            });
        }

        // Apply status filter
        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        // Apply asset type filter
        if (!empty($this->assetTypeFilter)) {
            // Map the filter values to actual asset_type values
            $assetTypeMap = [
                'Vehicle' => 'vehicle',
                'Room' => 'meeting_room',
                'Equipment' => 'it_asset'
            ];
            
            $actualAssetType = $assetTypeMap[$this->assetTypeFilter] ?? strtolower($this->assetTypeFilter);
            $query->where('asset_type', $actualAssetType);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        // Return paginated results
        return $query->paginate(15);
    }

    public function changeStatus($status, $bookingId = null)
    {
        try {
            // If bookingId is passed directly, use it. Otherwise use selectedBookingId
            $id = $bookingId ?? $this->selectedBookingId;
            
            if (!$id) {
                session()->flash('error', 'No booking selected');
                return;
            }

            $booking = Booking::findOrFail($id);

            // Authorization check using Spatie permissions
            if (!$this->canUserChangeStatus($booking)) {
                session()->flash('error', 'You are not authorized to change this booking status');
                return;
            }

            // Validate status
            $validStatuses = ['pending', 'approved', 'rejected', 'cancelled', 'done'];
            if (!in_array($status, $validStatuses)) {
                session()->flash('error', 'Invalid status provided');
                return;
            }

            // Additional business logic validation
            if (!$this->canChangeStatus($booking, $status)) {
                return;
            }

            // Update the status
            $booking->update([
                'status' => $status,
                'updated_at' => now(),
            ]);

            // Success message
            session()->flash('success', "Booking status changed to " . ucfirst($status));

            // Reset selection
            $this->selectedBookingId = null;

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the booking status');
            
            // Log the error for debugging
            \Log::error('Booking status change error: ' . $e->getMessage(), [
                'booking_id' => $id ?? 'unknown',
                'status' => $status,
                'user_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Check if user can change booking status using Spatie permissions
     */
    private function canUserChangeStatus($booking)
    {
        $user = auth()->user();

        // Admin and managers can change any booking status
        if ($user->hasRole(['Admin', 'Super Admin'])) {
            return true;
        }

        // Users with specific permission can change status
        if ($user->hasPermissionTo('book.edit')) {
            return true;
        }

        // Booking owner can only cancel their own pending/approved bookings
        if ($user->id === $booking->booked_by) {
            return in_array($booking->status, ['pending', 'approved']);
        }

        return false;
    }

    /**
     * Business logic to determine if status can be changed
     */
    private function canChangeStatus($booking, $newStatus)
    {
        $currentStatus = $booking->status;

        // Define allowed status transitions
        $allowedTransitions = [
            'pending' => ['approved', 'rejected', 'cancelled'],
            'approved' => ['done', 'cancelled'],
            'rejected' => ['pending'], // Maybe allow re-opening rejected bookings
            'cancelled' => [], // Usually final
            'done' => [], // Usually final
        ];

        // Check if transition is allowed
        if (!isset($allowedTransitions[$currentStatus]) || 
            !in_array($newStatus, $allowedTransitions[$currentStatus])) {
            
            session()->flash('error', "Cannot change status from " . ucfirst($currentStatus) . " to " . ucfirst($newStatus));
            return false;
        }

        // Additional checks based on booking date
        if ($newStatus === 'done' && $booking->start_time > now()) {
            session()->flash('error', 'Cannot mark future bookings as done');
            return false;
        }

        return true;
    }

    /**
     * Set the selected booking for status change
     */
    public function selectBooking($bookingId)
    {
        $this->selectedBookingId = $bookingId;
    } 
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
        
        // Reset to first page when sorting changes
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingAssetTypeFilter()
    {
        $this->resetPage();
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->assetTypeFilter = '';
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }
}