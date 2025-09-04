<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// class BookingStatusChanged extends Notification implements ShouldQueue
class BookingStatusChanged extends Notification
{
    protected $shouldQueue;
    protected $booking;
    protected $oldStatus;
    protected $newStatus;
    protected $changedByName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, string $oldStatus, string $newStatus, string $changedByName,bool $shouldQueue = false)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->changedByName = $changedByName;
        $this->shouldQueue = $shouldQueue;
        
        \Log::info('📧 BookingStatusChanged notification created', [
            'booking_id' => $booking->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $changedByName
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->shouldQueue ? ['mail'] : ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        \Log::info('📧 Building email for notification', [
            'booking_id' => $this->booking->id,
            'recipient' => $notifiable->email,
            'new_status' => $this->newStatus
        ]);

        $statusMessage = $this->getStatusMessage();
        $statusColor = $this->getStatusColor();

        return (new MailMessage)
            ->subject('Booking Status Update - ' . ucfirst($this->newStatus))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your booking status has been updated.')
            ->line('**Booking Details:**')
            ->line('Booking ID: #' . $this->booking->id)
            ->line('Previous Status: ' . ucfirst($this->oldStatus))
            ->line('New Status: **' . ucfirst($this->newStatus) . '**')
            ->line('Updated by: ' . $this->changedByName)
            ->line('Updated at: ' . now()->format('F j, Y \a\t g:i A'))
            ->when($statusMessage, function ($mail) use ($statusMessage) {
                return $mail->line($statusMessage);
            })
            ->action('View Booking Details', url('/bookings/my/' . $this->booking->id))
            ->line('Thank you for using our booking system!')
            ->salutation('Best regards, ' . config('app.name') . ' Team');
    }

    /**
     * Get status-specific message
     */
    private function getStatusMessage(): string
    {
        return match ($this->newStatus) {
            'approved' => '🎉 Great news! Your booking has been approved and confirmed.',
            'rejected' => '❌ Unfortunately, your booking has been rejected. Please contact us if you have any questions.',
            'cancelled' => '🚫 Your booking has been cancelled. If this was unexpected, please reach out to us.',
            'done' => '✅ Your booking has been completed successfully. We hope you had a great experience!',
            'pending' => '⏳ Your booking is now pending review. We will update you once it has been processed.',
            default => ''
        };
    }

    /**
     * Get status color for styling
     */
    private function getStatusColor(): string
    {
        return match ($this->newStatus) {
            'approved' => '#10B981', // Green
            'rejected' => '#EF4444', // Red
            'cancelled' => '#F59E0B', // Amber
            'done' => '#8B5CF6', // Purple
            'pending' => '#6B7280', // Gray
            default => '#6B7280'
        };
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'changed_by' => $this->changedByName,
            'changed_at' => now()->toDateTimeString(),
        ];
    }
}