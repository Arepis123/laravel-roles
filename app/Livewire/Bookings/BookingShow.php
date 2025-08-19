<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Vehicle;
use App\Models\ItAsset;

class BookingShow extends Component
{
    public $booking;
    public string $asset_type = '';
    public string $asset_id = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $purpose = '';
    public string $capacity = '';    
    public array $additional_booking = [];
    public string $refreshment_details = '';
    
    // Status tracking properties
    public string $status = '';
    public bool $showStatusHistory = false;

    protected $assetTypeConfig = [
        'meeting_room' => [
            'label' => 'Meeting Room',
            'model' => MeetingRoom::class,
            'name_field' => 'name'
        ],
        'vehicle' => [
            'label' => 'Vehicle',
            'model' => Vehicle::class,
            'name_field' => 'model'
        ],
        'it_asset' => [
            'label' => 'IT Asset',
            'model' => ItAsset::class,
            'name_field' => 'name'
        ],
    ];

    public function mount($id)
    {
        $this->booking = Booking::with('user')->findOrFail($id);

        $this->asset_type = $this->getAssetTypeKey($this->booking->asset_type);
        $this->asset_id = (string) $this->booking->asset_id;
        $this->start_time = $this->booking->start_time ?? '';
        $this->end_time = $this->booking->end_time ?? '';
        $this->purpose = $this->booking->purpose ?? '';
        $this->capacity = $this->booking->capacity ?? '';
        $this->additional_booking = $this->booking->additional_booking ?? [];
        $this->refreshment_details = $this->booking->refreshment_details ?? '';
        $this->status = $this->booking->status ?? 'pending';
    }

    /**
     * Change booking status with history tracking
     */
    public function changeStatus($newStatus)
    {
        try {
            \Log::info('🔄 changeStatus called', [
                'new_status' => $newStatus,
                'current_status' => $this->booking->status,
                'booking_id' => $this->booking->id,
                'auth_user' => auth()->id(),
                'booked_by' => $this->booking->booked_by
            ]);

            // Validate status
            $validStatuses = ['pending', 'approved', 'rejected', 'cancelled', 'done'];
            
            if (!in_array($newStatus, $validStatuses)) {
                \Log::warning('❌ Invalid status provided: ' . $newStatus);
                session()->flash('error', 'Invalid status selected.');
                return;
            }

            // Don't update if status is the same
            if ($this->booking->status === $newStatus) {
                \Log::info('ℹ️ Status unchanged - no update needed');
                session()->flash('info', 'Booking is already in ' . ucfirst($newStatus) . ' status.');
                return;
            }

            // Store the old status before updating
            $oldStatus = $this->booking->status;
            \Log::info('📝 Status change confirmed', [
                'from' => $oldStatus,
                'to' => $newStatus
            ]);

            // Get current status history
            $statusHistory = $this->booking->status_history ?? [];
            
            // Add new status change to history
            $statusHistory[] = [
                'status' => $newStatus,
                'previous_status' => $oldStatus,
                'changed_by' => auth()->id(),
                'changed_by_name' => auth()->user()->name,
                'changed_at' => now()->toDateTimeString(),
                'reason' => $this->getStatusChangeReason($oldStatus, $newStatus)
            ];

            // Update booking with new status and history
            $updateResult = $this->booking->update([
                'status' => $newStatus,
                'status_history' => $statusHistory
            ]);

            \Log::info('💾 Database update result', [
                'success' => $updateResult,
                'booking_id' => $this->booking->id
            ]);
            
            // Update local property
            $this->status = $newStatus;
            
            // Refresh the booking model to get updated data
            $this->booking->refresh();
            
            // Show success message
            session()->flash('success', "Booking status changed from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus) . " successfully.");
            
            // ALWAYS send email notification (remove the condition check)
            \Log::info('📧 About to send notification...');
            $this->notifyBookingOwner($oldStatus, $newStatus);
            
            \Log::info('✅ changeStatus completed successfully');
            
        } catch (\Exception $e) {
            \Log::error('❌ changeStatus failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'booking_id' => $this->booking->id ?? 'unknown'
            ]);
            session()->flash('error', 'Failed to update booking status. Please try again.');
        }
    }
    
    private function notifyBookingOwner($oldStatus, $newStatus)
    {
        try {
            \Log::info('📧 notifyBookingOwner called', [
                'booking_id' => $this->booking->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'booked_by' => $this->booking->booked_by,
                'auth_user' => auth()->id()
            ]);
            
            // Load the booking owner
            $bookingOwner = $this->booking->bookedBy;
            
            if (!$bookingOwner) {
                \Log::error('❌ Booking owner not found', [
                    'booking_id' => $this->booking->id,
                    'booked_by_id' => $this->booking->booked_by
                ]);
                return;
            }

            \Log::info('👤 Booking owner found', [
                'user_id' => $bookingOwner->id,
                'user_name' => $bookingOwner->name,
                'user_email' => $bookingOwner->email
            ]);

            \Log::info('📤 Sending notification...');

            // Send notification
            $bookingOwner->notify(new \App\Notifications\BookingStatusChanged(
                $this->booking,
                $oldStatus,
                $newStatus,
                auth()->user()->name
            ));

            \Log::info('✅ Status change notification sent successfully', [
                'booking_id' => $this->booking->id,
                'recipient' => $bookingOwner->email,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Failed to send booking status notification', [
                'booking_id' => $this->booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Generate reason for status change
     */
    private function getStatusChangeReason($oldStatus, $newStatus): string
    {
        return match([$oldStatus, $newStatus]) {
            ['pending', 'approved'] => 'Booking approved by admin',
            ['pending', 'rejected'] => 'Booking rejected by admin',
            ['approved', 'cancelled'] => 'Booking cancelled',
            ['approved', 'done'] => 'Booking completed',
            ['rejected', 'pending'] => 'Booking reopened for review',
            default => "Status changed from {$oldStatus} to {$newStatus}"
        };
    }

    /**
     * Toggle status history display
     */
    public function toggleStatusHistory()
    {
        $this->showStatusHistory = !$this->showStatusHistory;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorProperty(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'approved' => 'bg-green-100 text-green-800 border-green-300',
            'rejected' => 'bg-red-100 text-red-800 border-red-300',
            'cancelled' => 'bg-gray-100 text-gray-800 border-gray-300',
            'done' => 'bg-blue-100 text-blue-800 border-blue-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300'
        };
    }

    /**
     * Get formatted status history
     */
    public function getStatusHistoryProperty(): array
    {
        $history = $this->booking->status_history ?? [];
        
        // Sort by most recent first
        return array_reverse($history);
    }

    /**
     * Get total status changes count
     */
    public function getStatusChangesCountProperty(): int
    {
        return count($this->booking->status_history ?? []);
    }

    /**
     * Check if user can change status
     */
    public function getCanChangeStatusProperty(): bool
    {
        // Add your authorization logic here
        // For example, only admins or booking owner can change status
        return auth()->user()->hasRole(['Super Admin','Admin']) || 
               auth()->id() === $this->booking->booked_by;
    }

    /**
     * Convert stored asset_type back to key
     */
    private function getAssetTypeKey($fullClassName): string
    {
        foreach ($this->assetTypeConfig as $key => $config) {
            if ($config['model'] === $fullClassName) {
                return $key;
            }
        }
        return '';
    }

    public function getAssetTypeOptionsProperty()
    {
        return collect($this->assetTypeConfig)->map(function ($config, $key) {
            return [
                'value' => $key,
                'label' => $config['label']
            ];
        });
    }

    public function getAssetOptionsProperty()
    {
        if (empty($this->asset_type) || !isset($this->assetTypeConfig[$this->asset_type])) {
            return collect();
        }

        $config = $this->assetTypeConfig[$this->asset_type];
        $model = $config['model'];
        $nameField = $config['name_field'];

        return $model::select('id', "{$nameField} as name")->get();
    }

    public function updatedAssetType()
    {
        $this->asset_id = '';
    }

    public function render()
    {
        return view('livewire.bookings.booking-show');
    }
}