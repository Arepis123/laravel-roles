<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model
{
    protected $fillable = ['name', 'location', 'capacity', 'has_projector', 'notes'];

    public function bookings()
    {
        // return $this->morphMany(Booking::class, 'asset');
        return $this->morphMany(Booking::class, 'asset', 'asset_type', 'asset_id');
    }
}

