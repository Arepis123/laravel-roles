<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Notifications\IncompleteBookingReminder;
use Carbon\Carbon;

class SendIncompleteBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-incomplete-reminders {--hours=1 : Hours after end time to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders to users who have not marked their bookings as done after the end time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get settings from database
        $settings = \App\Models\BookingReminderSetting::getSettings();

        // Check if reminders are enabled
        if (!$settings->enabled) {
            $this->info('Booking reminders are currently disabled in settings.');
            return 0;
        }

        // Check if we should skip weekends
        $today = Carbon::now();
        if ($settings->skip_weekends && $today->isWeekend()) {
            $this->info('Skipping reminders - today is a weekend (' . $today->format('l') . ') and weekend skip is enabled.');
            return 0;
        }

        $hoursAfter = $this->option('hours') ?: $settings->hours_after_end;

        // Find bookings that:
        // 1. Are approved (users have used the asset)
        // 2. End time has passed by specified hours
        // 3. Status is still 'approved' (not marked as 'done')
        $query = Booking::where('status', 'approved')
            ->where('end_time', '<=', Carbon::now()->subHours($hoursAfter))
            ->with(['bookedBy', 'asset']);

        // Exclude certain asset types if configured
        if ($settings->excluded_asset_types && count($settings->excluded_asset_types) > 0) {
            $query->whereNotIn('asset_type', $settings->excluded_asset_types);
        }

        $incompleteBookings = $query->get();

        if ($incompleteBookings->isEmpty()) {
            $this->info('No incomplete bookings found.');
            return 0;
        }

        $remindersSent = 0;

        foreach ($incompleteBookings as $booking) {
            try {
                // Get the user who made the booking
                $user = $booking->bookedBy;

                if (!$user || !$user->email) {
                    $this->warn("Skipping booking #{$booking->id}: No valid user email found.");
                    continue;
                }

                // Check if max reminders reached
                if (\App\Models\BookingReminderLog::maxRemindersReached($booking->id, $user->id, $settings->max_reminders)) {
                    $this->info("Max reminders reached for booking #{$booking->id} (user: {$user->name})");
                    continue;
                }

                // Get current reminder count
                $reminderCount = \App\Models\BookingReminderLog::getReminderCount($booking->id, $user->id) + 1;

                // Send reminder notification
                $user->notify(new IncompleteBookingReminder($booking, $settings->custom_message));

                // Log successful send
                \App\Models\BookingReminderLog::create([
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'status' => 'sent',
                    'reminder_count' => $reminderCount,
                ]);

                $remindersSent++;
                $this->info("Sent reminder #{$reminderCount} to {$user->name} for booking #{$booking->id}");

                // Send to passengers if enabled
                if ($settings->send_to_passengers && $booking->hasPassengers()) {
                    foreach ($booking->passengerUsers() as $passenger) {
                        if ($passenger->email && !$passenger->is($user)) {
                            // Check if max reminders reached for this passenger
                            if (\App\Models\BookingReminderLog::maxRemindersReached($booking->id, $passenger->id, $settings->max_reminders)) {
                                continue;
                            }

                            $passengerReminderCount = \App\Models\BookingReminderLog::getReminderCount($booking->id, $passenger->id) + 1;

                            $passenger->notify(new IncompleteBookingReminder($booking, $settings->custom_message));

                            \App\Models\BookingReminderLog::create([
                                'booking_id' => $booking->id,
                                'user_id' => $passenger->id,
                                'status' => 'sent',
                                'reminder_count' => $passengerReminderCount,
                            ]);

                            $remindersSent++;
                            $this->info("Sent reminder #{$passengerReminderCount} to passenger {$passenger->name} for booking #{$booking->id}");
                        }
                    }
                }

            } catch (\Exception $e) {
                // Log failed send
                \App\Models\BookingReminderLog::create([
                    'booking_id' => $booking->id,
                    'user_id' => $user->id ?? null,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'reminder_count' => \App\Models\BookingReminderLog::getReminderCount($booking->id, $user->id ?? 0) + 1,
                ]);

                $this->error("Failed to send reminder for booking #{$booking->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully sent {$remindersSent} reminder(s).");
        return 0;
    }
}
