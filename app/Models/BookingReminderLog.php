<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingReminderLog extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'status',
        'error_message',
        'reminder_count',
    ];

    protected $casts = [
        'reminder_count' => 'integer',
    ];

    /**
     * Get the booking
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get reminder count for a booking and user
     */
    public static function getReminderCount($bookingId, $userId): int
    {
        return self::where('booking_id', $bookingId)
            ->where('user_id', $userId)
            ->where('status', 'sent')
            ->count();
    }

    /**
     * Check if max reminders reached
     */
    public static function maxRemindersReached($bookingId, $userId, $maxReminders): bool
    {
        return self::getReminderCount($bookingId, $userId) >= $maxReminders;
    }
}
