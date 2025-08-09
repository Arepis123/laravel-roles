<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use App\Models\Booking;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookingIndex extends Component
{
    use AuthorizesRequests;

    public $selectedBookingId = null;
    public function render()
    {
        $bookings = Booking::with('user')->get(); // admin view
        return view('livewire.bookings.booking-index', compact('bookings'));
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
        if ($newStatus === 'done' && $booking->booking_date > now()) {
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
}

