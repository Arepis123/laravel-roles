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
            'meeting_room' => 'Meeting Room',
            'it_asset'     => 'IT Asset',
            'vehicle'      => 'Vehicle',
            default        => 'Unknown',
        };
    }  
    
    public function getAssetNameAttribute()
    {
        switch ($this->asset_type) {
            case 'meeting_room':
                return \App\Models\MeetingRoom::find($this->asset_id)?->name ?? '-';
            case 'vehicle':
                return \App\Models\Vehicle::find($this->asset_id)?->model ?? '-';
            case 'it_asset':
                return \App\Models\ItAsset::find($this->asset_id)?->name ?? '-';
            default:
                return '-';
        }
    }    
}
