<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['model', 'plate_number', 'capacity', 'driver_name', 'notes'];

    public function bookings()
    {
        return $this->morphMany(Booking::class, 'asset');
    }
}

