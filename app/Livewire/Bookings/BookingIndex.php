<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use App\Models\Booking;

class BookingIndex extends Component
{
    public function render()
    {
        $bookings = Booking::with('user')->get(); // admin view
        return view('livewire.bookings.booking-index', compact('bookings'));
    }    
}

