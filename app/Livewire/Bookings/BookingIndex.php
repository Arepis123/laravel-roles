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
    public $booking;
    public $highlightId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'assetTypeFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'highlightId' => ['except' => null],
    ];

    public function render()
    {
        $bookings = $this->getBookingsQuery();
        
        return view('livewire.bookings.booking-index', compact('bookings'));
    }

    private function getBookingsQuery()
    {
        $query = Booking::with(['user', 'asset']);

        // Apply search filter - SUPER SIMPLE VERSION
        if (!empty($this->search)) {
            $query->where(function ($q) {
                // Search in user data (booking owner)
                $q->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                // Search by booking ID
                ->orWhere('id', 'like', '%' . $this->search . '%')
                // Search by status
                ->orWhere('status', 'like', '%' . $this->search . '%')
                // Search by date
                ->orWhere('start_time', 'like', '%' . $this->search . '%')
                ->orWhere('end_time', 'like', '%' . $this->search . '%');

                // Get asset IDs that match search term, then search bookings
                $vehicleIds = \DB::table('vehicles')->where('model', 'like', '%' . $this->search . '%')->pluck('id');
                if ($vehicleIds->isNotEmpty()) {
                    $q->orWhere(function($subQuery) use ($vehicleIds) {
                        $subQuery->where('asset_type', 'App\Models\Vehicle')
                                ->whereIn('asset_id', $vehicleIds);
                    });
                }

                $meetingRoomIds = \DB::table('meeting_rooms')->where('name', 'like', '%' . $this->search . '%')->pluck('id');
                if ($meetingRoomIds->isNotEmpty()) {
                    $q->orWhere(function($subQuery) use ($meetingRoomIds) {
                        $subQuery->where('asset_type', 'App\Models\MeetingRoom')
                                ->whereIn('asset_id', $meetingRoomIds);
                    });
                }

                $itAssetIds = \DB::table('it_assets')->where('name', 'like', '%' . $this->search . '%')->pluck('id');
                if ($itAssetIds->isNotEmpty()) {
                    $q->orWhere(function($subQuery) use ($itAssetIds) {
                        $subQuery->where('asset_type', 'App\Models\ItAsset')
                                ->whereIn('asset_id', $itAssetIds);
                    });
                }
            });
        }

        // Apply status filter
        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        // Apply asset type filter
        if (!empty($this->assetTypeFilter)) {
            $query->where('asset_type', $this->assetTypeFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        // Return paginated results
        return $query->paginate(15);
    }

    public function changeStatus($newStatus,$booking_id)
    {
        try {                 
            $this->booking = Booking::with('user')->findOrFail($booking_id);

            \Log::info('🔄 changeStatus called', [
                'new_status' => $newStatus,
                'current_status' => $this->booking->status,
                'booking_id' => $this->booking->id,
                'auth_user' => auth()->id(),
                'booked_by' => $this->booking->booked_by
            ]);

            // Validate status
            $validStatuses = ['pending', 'approved', 'rejected', 'cancelled', 'done'];
            
            if (!in_array($newStatus, $validStatuses)) {
                \Log::warning('❌ Invalid status provided: ' . $newStatus);
                session()->flash('error', 'Invalid status selected.');
                return;
            }

            // Don't update if status is the same
            if ($this->booking->status === $newStatus) {
                \Log::info('ℹ️ Status unchanged - no update needed');
                session()->flash('info', 'Booking is already in ' . ucfirst($newStatus) . ' status.');
                return;
            }

            // Store the old status before updating
            $oldStatus = $this->booking->status;
            \Log::info('🔍 Status change confirmed', [
                'from' => $oldStatus,
                'to' => $newStatus
            ]);

            // Get current status history
            $statusHistory = $this->booking->status_history ?? [];
            
            // Add new status change to history
            $statusHistory[] = [
                'status' => $newStatus,
                'previous_status' => $oldStatus,
                'changed_by' => auth()->id(),
                'changed_by_name' => auth()->user()->name,
                'changed_at' => now()->toDateTimeString(),
                'reason' => $this->getStatusChangeReason($oldStatus, $newStatus)
            ];

            // Update booking with new status and history
            $updateResult = $this->booking->update([
                'status' => $newStatus,
                'status_history' => $statusHistory
            ]);

            \Log::info('💾 Database update result', [
                'success' => $updateResult,
                'booking_id' => $this->booking->id
            ]);
            
            // Update local property
            $this->status = $newStatus;
            
            // Refresh the booking model to get updated data
            $this->booking->refresh();
            
            // Show success message
            session()->flash('success', "Booking status changed from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus) . " successfully.");
            
            // ALWAYS send email notification (remove the condition check)
            \Log::info('📧 About to send notification...');
            $this->notifyBookingOwner($oldStatus, $newStatus);
            
            // Emit event to update the badge in real-time
            $this->dispatch('bookingStatusUpdated');
            
            \Log::info('✅ changeStatus completed successfully');
            
        } catch (\Exception $e) {
            \Log::error('❌ changeStatus failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'booking_id' => $this->booking->id ?? 'unknown'
            ]);
            session()->flash('error', 'Failed to update booking status. Please try again.');
        }
    }
    
    private function notifyBookingOwner($oldStatus, $newStatus)
    {
        try {
            \Log::info('📧 notifyBookingOwner called', [
                'booking_id' => $this->booking->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'booked_by' => $this->booking->booked_by,
                'auth_user' => auth()->id()
            ]);
            
            // Load the booking owner
            $bookingOwner = $this->booking->bookedBy;
            
            if (!$bookingOwner) {
                \Log::error('❌ Booking owner not found', [
                    'booking_id' => $this->booking->id,
                    'booked_by_id' => $this->booking->booked_by
                ]);
                return;
            }

            \Log::info('👤 Booking owner found', [
                'user_id' => $bookingOwner->id,
                'user_name' => $bookingOwner->name,
                'user_email' => $bookingOwner->email
            ]);

            \Log::info('📤 Sending notification...');

            // Send notification
            $bookingOwner->notify(new \App\Notifications\BookingStatusChanged(
                $this->booking,
                $oldStatus,
                $newStatus,
                auth()->user()->name
            ));

            \Log::info('✅ Status change notification sent successfully', [
                'booking_id' => $this->booking->id,
                'recipient' => $bookingOwner->email,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Failed to send booking status notification', [
                'booking_id' => $this->booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Generate reason for status change
     */    
    private function getStatusChangeReason($oldStatus, $newStatus): string
    {
        return match([$oldStatus, $newStatus]) {
            ['pending', 'approved'] => 'Booking approved by admin',
            ['pending', 'rejected'] => 'Booking rejected by admin',
            ['approved', 'cancelled'] => 'Booking cancelled',
            ['approved', 'done'] => 'Booking completed',
            ['rejected', 'pending'] => 'Booking reopened for review',
            default => "Status changed from {$oldStatus} to {$newStatus}"
        };
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