<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingReminderSetting extends Model
{
    protected $fillable = [
        'enabled',
        'hours_after_end',
        'frequency',
        'send_to_passengers',
        'skip_weekends',
        'max_reminders',
        'custom_message',
        'excluded_asset_types',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'send_to_passengers' => 'boolean',
        'skip_weekends' => 'boolean',
        'hours_after_end' => 'integer',
        'max_reminders' => 'integer',
        'excluded_asset_types' => 'array',
    ];

    /**
     * Get the singleton instance
     */
    public static function getSettings()
    {
        return self::first() ?? self::create([
            'enabled' => true,
            'hours_after_end' => 1,
            'frequency' => 'hourly',
            'send_to_passengers' => false,
            'max_reminders' => 3,
        ]);
    }

    /**
     * Get available frequencies
     */
    public static function getFrequencies(): array
    {
        return [
            'hourly' => 'Every Hour',
            'every_4_hours' => 'Every 4 Hours',
            'daily' => 'Daily (9 AM)',
        ];
    }

    /**
     * Get available asset types
     */
    public static function getAssetTypes(): array
    {
        return [
            'App\Models\Vehicle' => 'Vehicles',
            'App\Models\MeetingRoom' => 'Meeting Rooms',
            'App\Models\ItAsset' => 'IT Assets',
        ];
    }
}
