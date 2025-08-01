<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Models\MeetingRoom;
use App\Models\Vehicle;
use App\Models\ItAsset;

class BookingCreate extends Component
{
    public $asset_type;
    public $asset_id;
    public $start_time;
    public $end_time;
    public $purpose;
    public $asset_options = [];    

    public function save()
    {
        $this->validate([
            'asset_type' => 'required|string',
            'asset_id' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'purpose' => 'required|string',
        ]);
 
        auth()->user()->bookings()->create([           
            'asset_type' => $this->asset_type,
            'asset_id' => $this->asset_id,
            'purpose' => $this->purpose,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => 'pending',
        ]);

        session()->flash('success', 'Booking submitted successfully.');
        return redirect()->route('bookings.index');
    }

    public function render()
    {
        return view('livewire.bookings.booking-create');
    }

    public function updatedAssetType($value)
    {
        if ($value === 'meeting_room') {
            $this->asset_options = MeetingRoom::all();
        } elseif ($value === 'vehicle') {
            $this->asset_options = Vehicle::all();
        } elseif ($value === 'it_asset') {
            $this->asset_options = ItAsset::all();
        } else {
            $this->asset_options = [];
        }
    }   
    
}
