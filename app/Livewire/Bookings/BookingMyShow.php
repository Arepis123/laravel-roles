<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Vehicle;
use App\Models\ItAsset;
use App\Models\VehicleOdometerLog;
use App\Models\VehicleFuelLog;

class BookingMyShow extends Component
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
    
    // Modal properties for marking as done
    public bool $showDoneModal = false;
    public string $doneRemarks = '';
    public string $currentOdometer = '';
    public bool $gasFilledUp = false;
    public string $gasAmount = '';
    public string $gasLiters = '';
    public string $fuelLevel = '4';
    
    // Parking location properties  
    public $parkingLevel = null;
    public bool $isReservedSlot = false;

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

        // Check if we should auto-open completion modal (from QR code scan)
        if (session('auto_open_completion_modal') || request('auto_open_completion_modal')) {
            $this->showDoneModal = true;
            session()->forget('auto_open_completion_modal'); // Clear it after using
        }

        // Load existing done details if available
        if ($this->booking->done_details) {
            $doneDetails = $this->booking->done_details;
            $this->doneRemarks = $doneDetails['remarks'] ?? '';
            $this->currentOdometer = $doneDetails['odometer'] ?? '';
            $this->gasFilledUp = $doneDetails['gas_filled'] ?? false;
            $this->gasAmount = $doneDetails['gas_amount'] ?? '';
            $this->gasLiters = $doneDetails['gas_liters'] ?? '';
            $this->fuelLevel = $doneDetails['fuel_level'] ?? '4';
        }
        
        // Load existing parking data only if booking is already completed
        if ($this->booking->status === 'done' && $this->booking->parking_level) {
            $this->parkingLevel = $this->booking->parking_level;
            $this->isReservedSlot = $this->booking->is_reserved_slot ?? false;
        }
    }

    /**
     * Open the done modal
     */
    public function openDoneModal()
    {
        $this->resetDoneFields();
        $this->showDoneModal = true;
    }

    /**
     * Reset done modal fields
     */
    public function resetDoneFields()
    {
        $this->doneRemarks = '';
        $this->currentOdometer = '';
        $this->gasFilledUp = false;
        $this->gasAmount = '';
        $this->gasLiters = '';
        $this->fuelLevel = '4';
        $this->parkingLevel = null;
        $this->isReservedSlot = false;
    }

    /**
     * Close the done modal
     */
    public function closeDoneModal()
    {
        $this->showDoneModal = false;
        $this->resetDoneFields();
    }

    /**
     * Validate and save done details based on asset type
     */
    public function confirmMarkAsDone()
    {
        // Validation based on asset type
        if ($this->asset_type === 'vehicle') {
            $validationRules = [
                'currentOdometer' => 'required|numeric|min:0',
                'fuelLevel' => 'required|integer|min:1|max:8',
                'gasAmount' => $this->gasFilledUp ? 'required|numeric|min:0' : 'nullable',
                'gasLiters' => $this->gasFilledUp ? 'required|numeric|min:0' : 'nullable',
            ];
            
            $validationMessages = [
                'currentOdometer.required' => 'Current odometer reading is required.',
                'currentOdometer.numeric' => 'Odometer must be a valid number.',
                'fuelLevel.required' => 'Fuel level is required.',
                'fuelLevel.integer' => 'Fuel level must be a valid number.',
                'fuelLevel.min' => 'Fuel level must be at least 1.',
                'fuelLevel.max' => 'Fuel level must be at most 8.',
                'gasAmount.required' => 'Fuel cost is required when fuel was filled up.',
                'gasAmount.numeric' => 'Fuel cost must be a valid number.',
                'gasLiters.required' => 'Fuel amount in liters is required when fuel was filled up.',
                'gasLiters.numeric' => 'Fuel amount must be a valid number.',
            ];
            
            // Add parking validation if required
            if ($this->isParkingRequired()) {
                $validationRules['parkingLevel'] = 'required|integer|min:1|max:5';
                $validationMessages['parkingLevel.required'] = 'Parking level is required.';
                $validationMessages['parkingLevel.integer'] = 'Parking level must be a valid number.';
                $validationMessages['parkingLevel.min'] = 'Parking level must be at least 1.';
                $validationMessages['parkingLevel.max'] = 'Parking level must be at most 5.';
            }
            
            $this->validate($validationRules, $validationMessages);
        } else {
            // For meeting room and IT assets
            $this->validate([
                // 'doneRemarks' => 'required|string|min:10',
                'doneRemarks' => 'string|min:5',
            ], [
                'doneRemarks.required' => 'Remarks are required when marking as done.',
                'doneRemarks.min' => 'Remarks must be at least 10 characters.',
            ]);
        }

        if ($this->asset_type === 'vehicle') {
            // For vehicles, save to dedicated tables instead of done_details
            
            // Save odometer log
            VehicleOdometerLog::create([
                'booking_id' => $this->booking->id,
                'vehicle_id' => $this->booking->asset_id,
                'odometer_reading' => $this->currentOdometer,
                'reading_type' => 'end',
                'recorded_by' => auth()->id(),
                'recorded_at' => now(),
                'notes' => 'Booking completion - odometer reading'
            ]);

            // Save fuel log if gas was filled
            if ($this->gasFilledUp && $this->gasAmount && $this->gasLiters) {
                VehicleFuelLog::create([
                    'booking_id' => $this->booking->id,
                    'vehicle_id' => $this->booking->asset_id,
                    'fuel_cost' => $this->gasAmount,
                    'fuel_amount' => $this->gasLiters,
                    'fuel_type' => 'petrol', // Default to petrol, can be made dynamic later
                    'odometer_at_fill' => $this->currentOdometer,
                    'filled_by' => auth()->id(),
                    'filled_at' => now(),
                    'notes' => 'Fuel filled during booking completion'
                ]);
            }

            // For vehicles, save minimal completion info in done_details
            $doneDetails = [
                'fuel_level' => $this->fuelLevel,
                'completed_at' => now()->toDateTimeString(),
                'completed_by' => auth()->id(),
                'completed_by_name' => auth()->user()->name,
            ];
        } else {
            // For non-vehicle assets, keep existing behavior
            $doneDetails = [
                'remarks' => $this->doneRemarks,
                'completed_at' => now()->toDateTimeString(),
                'completed_by' => auth()->id(),
                'completed_by_name' => auth()->user()->name,
            ];
        }

        // Prepare update data
        $updateData = [
            'done_details' => $doneDetails
        ];

        // Add parking data if required and provided
        if ($this->asset_type === 'vehicle' && $this->isParkingRequired() && $this->parkingLevel) {
            $updateData['parking_level'] = $this->parkingLevel;
            $updateData['is_reserved_slot'] = $this->isReservedSlot;
        }

        // Update booking with done details and parking data
        $this->booking->update($updateData);

        // Close modal
        $this->closeDoneModal();

        // Change status to done
        $this->changeStatus('done');
        
        // Trigger confetti animation via Alpine.js event for all booking types
        $this->dispatch('booking-completed');
    }

    /**
     * Change booking status with history tracking
     */
    public function changeStatus($newStatus)
    {
        try {
            // If trying to mark as done and modal not shown yet, show modal first
            if ($newStatus === 'done' && !$this->booking->done_details && !$this->showDoneModal) {
                $this->openDoneModal();
                return;
            }

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
                \Log::warning('⚠ Invalid status provided: ' . $newStatus);
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
            \Log::error('⚠ changeStatus failed', [
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
                \Log::error('⚠ Booking owner not found', [
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
            \Log::error('⚠ Failed to send booking status notification', [
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

    /**
     * Get vehicle completion data from the new tables
     */
    public function getVehicleCompletionDataProperty(): array
    {
        if ($this->asset_type !== 'vehicle' || $this->status !== 'done') {
            return [];
        }

        // Get latest odometer reading for this booking
        $odometerLog = VehicleOdometerLog::where('booking_id', $this->booking->id)
            ->where('reading_type', 'end')
            ->latest('recorded_at')
            ->first();

        // Get fuel log for this booking
        $fuelLog = VehicleFuelLog::where('booking_id', $this->booking->id)
            ->latest('filled_at')
            ->first();

        return [
            'odometer_reading' => $odometerLog ? $odometerLog->odometer_reading : null,
            'fuel_filled' => $fuelLog ? true : false,
            'fuel_cost' => $fuelLog ? $fuelLog->fuel_cost : null,
            'fuel_amount' => $fuelLog ? $fuelLog->fuel_amount : null,
            'fuel_level' => $this->booking->done_details['fuel_level'] ?? null,
        ];
    }

    /**
     * Check if parking location is required for this booking
     */
    public function isParkingRequired(): bool
    {
        if ($this->asset_type !== 'vehicle') {
            return false;
        }

        $vehicle = Vehicle::find($this->booking->asset_id);
        return $vehicle && $vehicle->parking_required;
    }

    /**
     * Get available parking levels
     */
    public function getParkingLevels(): array
    {
        return [1, 2, 3, 4, 5];
    }

    public function render()
    {
        return view('livewire.bookings.booking-my-show');
    }
}