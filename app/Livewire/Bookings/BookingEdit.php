<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Vehicle;
use App\Models\ItAsset;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingEdit extends Component
{
    public Booking $booking;
    public string $asset_type = '';
    public string $asset_id = '';
    public string $booking_date = '';  
    public string $start_time = '';   
    public string $end_time = '';     
    public string $purpose = '';
    public string $capacity = '';     

    public array $additional_booking = [];
    public string $refreshment_details = '';

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

    public function mount(Booking $booking)
    {
        // Check if user owns this booking or is admin
        if ($booking->user_id !== auth()->id() && !auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            abort(403, 'Unauthorized to edit this booking.');
        }

        // Check if booking can still be edited (not started yet)
        if ($booking->start_time->isPast()) {
            abort(403, 'Cannot edit past bookings.');
        }

        $this->booking = $booking;
        $this->loadBookingData();
    }

    protected function loadBookingData()
    {
        // Find the asset type key from the model class
        $this->asset_type = '';
        foreach ($this->assetTypeConfig as $key => $config) {
            if ($config['model'] === $this->booking->asset_type) {
                $this->asset_type = $key;
                break;
            }
        }

        $this->asset_id = (string) $this->booking->asset_id;
        $this->booking_date = $this->booking->start_time->format('Y-m-d');
        $this->start_time = $this->booking->start_time->format('H:i');
        $this->end_time = $this->booking->end_time->format('H:i');
        $this->purpose = $this->booking->purpose ?? '';
        $this->capacity = (string) ($this->booking->capacity ?? '');
        $this->additional_booking = $this->booking->additional_booking ?? [];
        $this->refreshment_details = $this->booking->refreshment_details ?? '';
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
        // Only reset asset_id if asset_type actually changed and it's different from the original
        $originalAssetType = '';
        foreach ($this->assetTypeConfig as $key => $config) {
            if ($config['model'] === $this->booking->asset_type) {
                $originalAssetType = $key;
                break;
            }
        }

        if ($this->asset_type !== $originalAssetType) {
            $this->asset_id = '';
            // Reset time selections when asset type changes
            $this->start_time = '';
            $this->end_time = '';
        }
    }

    public function updatedAssetId()
    {
        // Reset time selections when asset changes (only if different from original)
        if ($this->asset_id != $this->booking->asset_id) {
            $this->start_time = '';
            $this->end_time = '';
        }
    }

    public function updatedBookingDate()
    {
        // Reset time selections when date changes (only if different from original)
        if ($this->booking_date !== $this->booking->start_time->format('Y-m-d')) {
            $this->start_time = '';
            $this->end_time = '';
        }
    }

    public function updatedStartTime()
    {
        // Reset end time when start time changes (only if different from original)
        if ($this->start_time !== $this->booking->start_time->format('H:i')) {
            $this->end_time = '';
        }
    }

    public function updatedAdditionalBooking()
    {
        // Clear refreshment_details if refreshment is unchecked
        if (!in_array('refreshment', $this->additional_booking)) {
            $this->refreshment_details = '';
            $this->resetErrorBag('refreshment_details');
        }
    }

    /**
     * Get available time slots (every 30 minutes from 8 AM to 6 PM)
     */
    public function getAvailableTimeSlots()
    {
        $slots = [];
        $start = 8; // 8 AM
        $end = 18;  // 6 PM
        
        // Return empty if asset not selected
        if (!$this->asset_type || !$this->asset_id || !$this->booking_date) {
            return $slots;
        }

        for ($hour = $start; $hour < $end; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $timeString = sprintf('%02d:%02d', $hour, $minute);
                $displayTime = Carbon::createFromFormat('H:i', $timeString)->format('g:i A');
                
                // Skip past times for today
                if ($this->booking_date === date('Y-m-d')) {
                    $timeToCheck = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $timeString);
                    if ($timeToCheck->isPast()) {
                        continue;
                    }
                }

                // Check if this time slot is available (excluding current booking)
                if ($this->isTimeSlotAvailable($timeString)) {
                    $slots[$timeString] = $displayTime;
                }
            }
        }
        
        return $slots;
    }

    /**
     * Get available end times based on selected start time
     */
    public function getAvailableEndTimes()
    {
        if (!$this->start_time || !$this->asset_type || !$this->asset_id || !$this->booking_date) {
            return [];
        }
        
        $slots = [];
        
        try {
            $startTime = Carbon::createFromFormat('H:i', $this->start_time);
            $endOfDay = Carbon::createFromFormat('H:i', '18:00'); // 6 PM
            
            // Start from 30 minutes after start time
            $currentTime = $startTime->copy()->addMinutes(30);            
            
            while ($currentTime->lte($endOfDay)) {
                $timeString = $currentTime->format('H:i');
                $displayTime = $currentTime->format('g:i A');
                
                // Check if the time range from start_time to this end_time is available
                if ($this->isTimeRangeAvailable($this->start_time, $timeString)) {
                    $slots[$timeString] = $displayTime;
                } else {
                    // If this slot is not available, stop checking further slots
                    break;
                }
                
                $currentTime->addMinutes(30);
            }
            
        } catch (\Exception $e) {
            \Log::error('Error generating end times:', [
                'error' => $e->getMessage(),
                'start_time' => $this->start_time
            ]);
        }
        
        return $slots;
    }

    /**
     * Check if a specific time slot is available for the selected asset
     */
    protected function isTimeSlotAvailable($timeSlot)
    {
        if (!$this->asset_type || !$this->asset_id || !$this->booking_date) {
            return false;
        }

        try {
            $checkDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $timeSlot);
            
            // Check if there's any booking that conflicts with this time slot (exclude current booking)
            $conflictingBooking = Booking::where('asset_type', $this->assetTypeConfig[$this->asset_type]['model'])
                ->where('asset_id', $this->asset_id)
                ->where('status', '!=', 'cancelled')
                ->where('id', '!=', $this->booking->id) // Exclude current booking
                ->where('start_time', '<=', $checkDateTime)
                ->where('end_time', '>', $checkDateTime)
                ->exists();

            return !$conflictingBooking;

        } catch (\Exception $e) {
            \Log::error('Error checking time slot availability:', [
                'error' => $e->getMessage(),
                'time_slot' => $timeSlot,
                'asset_type' => $this->asset_type,
                'asset_id' => $this->asset_id,
                'booking_date' => $this->booking_date
            ]);
            return false;
        }
    }

    /**
     * Check if a time range is available for the selected asset
     */
    protected function isTimeRangeAvailable($startTime, $endTime)
    {
        if (!$this->asset_type || !$this->asset_id || !$this->booking_date) {
            return false;
        }

        try {
            $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $startTime);
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $endTime);
            
            // Check if there's any booking that conflicts with this time range (exclude current booking)
            $conflictingBooking = Booking::where('asset_type', $this->assetTypeConfig[$this->asset_type]['model'])
                ->where('asset_id', $this->asset_id)
                ->where('status', '!=', 'cancelled')
                ->where('id', '!=', $this->booking->id) // Exclude current booking
                ->where(function ($query) use ($startDateTime, $endDateTime) {
                    $query->where(function ($q) use ($startDateTime, $endDateTime) {
                        // Existing booking starts during our requested time
                        $q->whereBetween('start_time', [$startDateTime, $endDateTime->copy()->subSecond()]);
                    })
                    ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                        // Existing booking ends during our requested time  
                        $q->whereBetween('end_time', [$startDateTime->copy()->addSecond(), $endDateTime]);
                    })
                    ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                        // Existing booking completely encompasses our requested time
                        $q->where('start_time', '<=', $startDateTime)
                          ->where('end_time', '>=', $endDateTime);
                    })
                    ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                        // Our requested time completely encompasses existing booking
                        $q->where('start_time', '>=', $startDateTime)
                          ->where('end_time', '<=', $endDateTime);
                    });
                })
                ->exists();

            return !$conflictingBooking;

        } catch (\Exception $e) {
            \Log::error('Error checking time range availability:', [
                'error' => $e->getMessage(),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'asset_type' => $this->asset_type,
                'asset_id' => $this->asset_id,
                'booking_date' => $this->booking_date
            ]);
            return false;
        }
    }
    public function getBookingDurationProperty()
    {
        if (!$this->start_time || !$this->end_time) return '';
        
        try {
            $start = Carbon::createFromFormat('H:i', $this->start_time);
            $end = Carbon::createFromFormat('H:i', $this->end_time);
            
            $minutes = $start->diffInMinutes($end);
            $hours = intval($minutes / 60);
            $remainingMinutes = $minutes % 60;
            
            $duration = '';
            if ($hours > 0) {
                $duration .= $hours . ' hour' . ($hours > 1 ? 's' : '');
            }
            if ($remainingMinutes > 0) {
                if ($hours > 0) $duration .= ' and ';
                $duration .= $remainingMinutes . ' minute' . ($remainingMinutes > 1 ? 's' : '');
            }
            
            return $duration;
            
        } catch (\Exception $e) {
            \Log::error('Duration calculation error: ' . $e->getMessage());
            return '';
        }
    }    

    /**
     * Get booking duration in human readable format
     */
    public function getBookingDuration()
    {
        return $this->getBookingDurationProperty();
    }

    /**
     * Real-time validation as user types
     */
    public function updated($propertyName)
    {
        // Debug: Log what's being updated
        \Log::info('Property updated:', [
            'property' => $propertyName,
            'value' => $this->{$propertyName} ?? 'N/A',
            'booking_date' => $this->booking_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time
        ]);

        // Handle array properties differently
        if (strpos($propertyName, 'additional_booking') === 0) {
            $this->updatedAdditionalBooking();
            return;
        }

        // Clear previous errors for the field being updated
        $this->resetErrorBag($propertyName);

        // Validate individual fields
        $this->validateOnly($propertyName);
        
        // If all time-related fields are filled, validate the combination
        if (in_array($propertyName, ['booking_date', 'start_time', 'end_time']) && 
            $this->booking_date && $this->start_time && $this->end_time) {
            $this->validateTimes();
        }
    }

    /**
     * Custom validation for date and time combination
     */
    public function validateTimes()
    {
        if (!$this->booking_date || !$this->start_time || !$this->end_time) {
            return false;
        }

        try {
            $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $this->start_time);
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $this->end_time);
            
            // Check if booking is not in the past (for today's bookings)
            if ($startDateTime->isToday() && $startDateTime->isPast()) {
                $this->addError('start_time', 'Start time cannot be in the past for today\'s bookings.');
                return false;
            }

            // Check if end time is after start time
            if ($endDateTime->lte($startDateTime)) {
                $this->addError('end_time', 'End time must be after start time.');
                return false;
            }

            // Check minimum booking duration (at least 30 minutes)
            $durationMinutes = $startDateTime->diffInMinutes($endDateTime);

            if ($durationMinutes < 30) {
                $this->addError('end_time', "Booking must be at least 30 minutes long. Current duration: {$durationMinutes} minutes.");
                return false;
            }

            // Check maximum booking duration (max 8 hours)
            if ($endDateTime->diffInHours($startDateTime) > 8) {
                $this->addError('end_time', 'Booking cannot exceed 8 hours.');
                return false;
            }

            return true;

        } catch (\Exception $e) {
            \Log::error('Time validation error:', [
                'error' => $e->getMessage(),
                'booking_date' => $this->booking_date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time
            ]);
            $this->addError('booking_date', 'Invalid date or time format: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get validation rules
     */
    public function rules()
    {
        $rules = [
            'asset_type' => 'required|string',
            'asset_id' => 'required|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'purpose' => 'required|string|min:3',
            'capacity' => 'nullable|numeric|min:1',
            'additional_booking' => 'array',
            'additional_booking.*' => 'string|in:refreshment,smart_monitor,laptop',
        ];

        // Add conditional rule for refreshment_details
        if (in_array('refreshment', $this->additional_booking)) {
            $rules['refreshment_details'] = 'required|string|min:3';
        }

        return $rules;
    }

    /**
     * Get custom validation messages
     */
    public function messages()
    {
        return [
            'asset_type.required' => 'Please select an asset type.',
            'asset_id.required' => 'Please select an asset.',
            'booking_date.required' => 'Please select a booking date.',
            'booking_date.after_or_equal' => 'Booking date must be today or in the future.',
            'start_time.required' => 'Please select a start time.',
            'start_time.date_format' => 'Please enter a valid start time.',
            'end_time.required' => 'Please select an end time.',
            'end_time.date_format' => 'Please enter a valid end time.',
            'purpose.required' => 'Please provide a purpose for the booking.',
            'purpose.min' => 'Purpose must be at least 3 characters long.',
            'capacity.numeric' => 'Capacity must be a number.',
            'capacity.min' => 'Capacity must be at least 1.',
            'refreshment_details.required' => 'Please provide refreshment details.',
            'refreshment_details.min' => 'Refreshment details must be at least 3 characters long.',
            'additional_booking.*.in' => 'Invalid additional booking option selected.',
        ];
    }

    public function update()
    {
        // Validate all fields
        $this->validate();

        // Validate time combination
        if (!$this->validateTimes()) {
            return;
        }

        try {
            // Combine date and time for database storage
            $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $this->start_time);
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $this->end_time);

            // Check for conflicting bookings (exclude current booking)
            $conflictingBooking = Booking::where('asset_type', $this->assetTypeConfig[$this->asset_type]['model'])
                ->where('asset_id', $this->asset_id)
                ->where('status', '!=', 'cancelled')
                ->where('id', '!=', $this->booking->id) // Exclude current booking
                ->where(function ($query) use ($startDateTime, $endDateTime) {
                    $query->whereBetween('start_time', [$startDateTime, $endDateTime])
                          ->orWhereBetween('end_time', [$startDateTime, $endDateTime])
                          ->orWhere(function ($q) use ($startDateTime, $endDateTime) {
                              $q->where('start_time', '<=', $startDateTime)
                                ->where('end_time', '>=', $endDateTime);
                          });
                })
                ->exists();

            if ($conflictingBooking) {
                $this->addError('booking_date', 'This asset is already booked for the selected time slot.');
                return;
            }

            // Update the booking
            $this->booking->update([
                'asset_type' => $this->assetTypeConfig[$this->asset_type]['model'],
                'asset_id' => $this->asset_id,
                'purpose' => $this->purpose,
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'capacity' => $this->capacity ?: null,
                'additional_booking' => $this->additional_booking,
                'refreshment_details' => in_array('refreshment', $this->additional_booking) ? $this->refreshment_details : null,
            ]);

            session()->flash('success', 'Booking updated successfully for ' . $startDateTime->format('F j, Y') . ' from ' . $startDateTime->format('g:i A') . ' to ' . $endDateTime->format('g:i A') . '.');
            
            return redirect()->route('bookings.index');

        } catch (\Exception $e) {
            \Log::error('Booking update failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to update booking. Please try again.');
        }
    }

    public function cancel()
    {
        try {
            $this->booking->update(['status' => 'cancelled']);
            session()->flash('success', 'Booking has been cancelled successfully.');
            return redirect()->route('bookings.index');
        } catch (\Exception $e) {
            \Log::error('Booking cancellation failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to cancel booking. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.bookings.booking-edit');
    }
}