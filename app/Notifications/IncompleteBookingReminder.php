<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncompleteBookingReminder extends Notification
{
    use Queueable;

    protected $booking;
    protected $customMessage;
    protected $assetDetails;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, $customMessage = null)
    {
        $this->booking = $booking;
        $this->customMessage = $customMessage;
        $this->assetDetails = $this->getAssetDetails();
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Reminder: Please Complete Your ' . $this->assetDetails['type'] . ' Booking')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('⚠️ **Action Required:** Your booking has ended, but it has not been marked as complete. Please mark your booking as done and provide the required details.')
            ->line('You had a booking that has ended. To ensure proper asset management and tracking, please mark your booking as complete and provide the necessary details.');

        // Add custom message if exists
        if ($this->customMessage) {
            $mail->line('---')
                 ->line('**Message from Admin:**')
                 ->line($this->customMessage)
                 ->line('---');
        }

        // Add booking details
        $mail->line('**Booking Information:**')
             ->line('Booking ID: #' . $this->booking->id)
             ->line('Status: **Approved** (Awaiting Completion)')
             ->line('Booking Type: ' . $this->assetDetails['type'])
             ->line($this->assetDetails['label'] . ': ' . $this->assetDetails['name']);

        // Add date/time information
        $startDate = \Carbon\Carbon::parse($this->booking->start_time);
        $endDate = \Carbon\Carbon::parse($this->booking->end_time);
        $isMultiDay = !$startDate->isSameDay($endDate);

        if ($isMultiDay) {
            $mail->line('Period: ' . $startDate->format('F j, Y') . ' - ' . $endDate->format('F j, Y') . ' (' . ($startDate->diffInDays($endDate) + 1) . ' days)')
                 ->line('Daily Time: ' . $startDate->format('g:i A') . ' - ' . $endDate->format('g:i A'));
        } else {
            $mail->line('Date: ' . $startDate->format('l, F j, Y'));

            $duration = $startDate->diff($endDate);
            $hours = $duration->h;
            $minutes = $duration->i;
            $durationText = '';
            if ($hours > 0) $durationText .= $hours . ' hour' . ($hours > 1 ? 's' : '');
            if ($minutes > 0) $durationText .= ($hours > 0 ? ' ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '');

            $mail->line('Time: ' . $startDate->format('g:i A') . ' - ' . $endDate->format('g:i A') . ($durationText ? ' (' . $durationText . ')' : ''));
        }

        $mail->line('Ended: **' . $endDate->diffForHumans() . '**')
             ->line('Purpose: ' . $this->booking->purpose);

        // Add destination if vehicle
        if (isset($this->assetDetails['destination'])) {
            $mail->line('Destination: ' . $this->assetDetails['destination']);
        }

        // Add passengers if vehicle
        if (isset($this->assetDetails['passengers'])) {
            $mail->line('Passengers: ' . $this->assetDetails['passengers']);
        }

        // Add what to do instructions
        $mail->line('---')
             ->line('**What you need to do:**');

        $instructions = $this->getInstructions();
        foreach ($instructions as $instruction) {
            $mail->line('• ' . $instruction);
        }

        $mail->action('Complete My Booking', route('bookings.index.user'))
             ->line('**Note:** Completing your booking helps us maintain accurate records and ensures assets are properly managed for other users.')
             ->salutation('Best regards, ' . config('app.name') . ' Team');

        return $mail;
    }

    /**
     * Get the asset details based on type
     */
    private function getAssetDetails()
    {
        $asset = $this->booking->asset;

        if (!$asset) {
            return [
                'name' => 'Unknown Asset',
                'type' => 'Unknown Type',
                'label' => 'Asset'
            ];
        }

        $assetType = class_basename($this->booking->asset_type);

        switch ($assetType) {
            case 'MeetingRoom':
                return [
                    'name' => $asset->name,
                    'type' => 'Meeting Room',
                    'label' => 'Location'
                ];
            case 'Vehicle':
                $details = [
                    'name' => $asset->model . ' (' . $asset->plate_number . ')',
                    'type' => 'Vehicle',
                    'label' => 'Model'
                ];

                if ($this->booking->destination) {
                    $details['destination'] = $this->booking->destination;
                }

                if ($this->booking->hasPassengers()) {
                    $details['passengers'] = $this->booking->passengerNames;
                }

                return $details;
            case 'ItAsset':
                return [
                    'name' => $asset->name,
                    'type' => 'IT Asset',
                    'label' => 'Asset'
                ];
            default:
                return [
                    'name' => $asset->name ?? $asset->model ?? 'Unknown',
                    'type' => $assetType,
                    'label' => 'Asset'
                ];
        }
    }

    /**
     * Get instructions based on asset type
     */
    private function getInstructions(): array
    {
        return match ($this->assetDetails['type']) {
            'Vehicle' => [
                'Provide the final odometer reading',
                'Indicate if you refueled the vehicle and the amount',
                'Add any remarks about the vehicle condition',
                'Mark the booking as "Done" in the system'
            ],
            'Meeting Room' => [
                'Confirm the meeting room has been cleaned and restored',
                'Report any issues or damage if applicable',
                'Add any remarks about your usage',
                'Mark the booking as "Done" in the system'
            ],
            'IT Asset' => [
                'Confirm the IT asset is in good condition',
                'Report any issues encountered during usage',
                'Add any remarks about your usage',
                'Mark the booking as "Done" in the system'
            ],
            default => [
                'Confirm the asset has been returned in good condition',
                'Report any issues if applicable',
                'Add any remarks about your usage',
                'Mark the booking as "Done" in the system'
            ]
        };
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'asset_type' => $this->assetDetails['type'],
            'sent_at' => now()->toDateTimeString(),
        ];
    }
}
