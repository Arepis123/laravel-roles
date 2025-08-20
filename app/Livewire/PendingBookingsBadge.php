<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;

class PendingBookingsBadge extends Component
{
    public $count = 0;
    public $urgentCount = 0; // Bookings pending for more than 24 hours
    public $todayCount = 0;  // Bookings starting today that are pending

    public function mount()
    {
        $this->loadCount();
    }

    public function loadCount()
    {
        // Only load count if user is admin
        if (!auth()->check() || !auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            $this->count = 0;
            return;
        }

        // Get all pending bookings
        $pendingBookings = Booking::where('status', 'pending')->get();
        
        $this->count = $pendingBookings->count();
        
        // Count urgent bookings (pending for more than 36 hours)
        $this->urgentCount = $pendingBookings->filter(function ($booking) {
            return $booking->created_at->diffInHours(now()) > 36;
        })->count();
        
        // Count today's pending bookings
        $this->todayCount = $pendingBookings->filter(function ($booking) {
            return \Carbon\Carbon::parse($booking->start_time)->isToday();
        })->count();
    }

    public function render()
    {
        return view('livewire.pending-bookings-badge');
    }

    // Listen for events when booking status changes
    protected $listeners = ['bookingStatusUpdated' => 'loadCount'];
}