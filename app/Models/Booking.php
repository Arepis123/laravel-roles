<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'asset_type',
        'asset_id',
        'booked_by',
        'purpose',
        'start_time',
        'end_time',
        'status',
    ];
    
    public function asset()
    {
        return $this->morphTo(null, 'asset_type', 'asset_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'booked_by');
    }

    public function getAssetTypeLabelAttribute()
    {
        return match (class_basename($this->asset_type)) {
            'MeetingRoom' => 'Meeting Room',
            'Vehicle'     => 'Vehicle',
            'ItAsset'     => 'IT Asset',
            default       => 'Unknown',
        };
    }  
}
