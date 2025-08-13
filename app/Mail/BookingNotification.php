<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;
use App\Models\User;

class BookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $user;
    public $assetDetails;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, User $user)
    {
        $this->booking = $booking;
        $this->user = $user;
        
        // Load the asset details
        $this->assetDetails = $this->getAssetDetails();
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
                'type' => 'Unknown Type'
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
                
                // Add destination if available
                if ($this->booking->destination) {
                    $details['destination'] = $this->booking->destination;
                }
                
                // Add passengers if available
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
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $assetType = $this->assetDetails['type'];
        
        return new Envelope(
            subject: "New {$assetType} Booking Request - {$this->user->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-notification',
            with: [
                'booking' => $this->booking,
                'user' => $this->user,
                'assetDetails' => $this->assetDetails,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}