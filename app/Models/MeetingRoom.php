<?php

namespace App\Models;

use App\Traits\HasQrCode;
use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model
{
    use HasQrCode;

    protected $fillable = ['name', 'location', 'capacity', 'has_projector', 'notes', 'qr_code_identifier'];

    public function bookings()
    {
        // return $this->morphMany(Booking::class, 'asset');
        return $this->morphMany(Booking::class, 'asset', 'asset_type', 'asset_id');
    }
}

