<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItAsset extends Model
{
    protected $fillable = ['name', 'asset_tag', 'location', 'specs', 'notes'];

    public function bookings()
    {
        // return $this->morphMany(Booking::class, 'asset');
         return $this->morphMany(Booking::class, 'asset', 'asset_type', 'asset_id');
    }
}
