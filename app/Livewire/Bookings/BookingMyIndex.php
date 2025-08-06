<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use App\Models\Booking;

class BookingMyIndex extends Component
{
    public function render()
    {
        // $bookings = Booking::where('booked_by', auth()->id())->get();
        // $bookings = Booking::with('user')->where('booked_by', auth()->id())->get();
        $bookings = Booking::with('user')
            ->where('booked_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('livewire.bookings.booking-my-index', compact('bookings'));
    }
}
