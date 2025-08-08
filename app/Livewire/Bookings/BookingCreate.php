<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Vehicle;
use App\Models\ItAsset;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingCreate extends Component
{
    public string $asset_type = '';
    public string $asset_id = '';
    public string $booking_date = '';  // New: separate date field
    public string $start_time = '';    // Modified: now just time (H:i format)
    public string $end_time = '';      // Modified: now just time (H:i format)
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
     * Get available time slots (every 30 minutes from 8 AM to 6 PM)
     */
    public function getAvailableTimeSlots()
    {
        $slots = [];
        $start = 8; // 8 AM
        $end = 18;  // 6 PM
        
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
                
                $slots[$timeString] = $displayTime;
            }
        }
        
        return $slots;
    }

    /**
     * Get available end times based on selected start time
     */
    public function getAvailableEndTimes()
    {
        if (!$this->start_time) return [];
        
        $slots = [];
        
        try {
            $startTime = Carbon::createFromFormat('H:i', $this->start_time);
            $endOfDay = Carbon::createFromFormat('H:i', '18:00'); // 6 PM
            
            // Start from 15 minutes after start time
            $currentTime = $startTime->copy()->addMinutes(30);            
            
            while ($currentTime->lte($endOfDay)) {
                $timeString = $currentTime->format('H:i');
                $displayTime = $currentTime->format('g:i A');
                $slots[$timeString] = $displayTime;
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
     * Get booking duration as computed property
     */
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
        if (!$this->start_time || !$this->end_time) return '';
        
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
            'value' => $this->{$propertyName},
            'booking_date' => $this->booking_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time
        ]);

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
        ];
    }

    public function save()
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

            // Check for conflicting bookings (optional)
            $conflictingBooking = Booking::where('asset_type', $this->assetTypeConfig[$this->asset_type]['model'])
                ->where('asset_id', $this->asset_id)
                ->where('status', '!=', 'cancelled')
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

            // Create the booking
            auth()->user()->bookings()->create([
                'asset_type' => $this->assetTypeConfig[$this->asset_type]['model'],
                'asset_id' => $this->asset_id,
                'purpose' => $this->purpose,
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'status' => 'pending',
                'capacity' => $this->capacity ?: null,
                'additional_booking' => $this->additional_booking,
                'refreshment_details' => in_array('refreshment', $this->additional_booking) ? $this->refreshment_details : null,
            ]);

            session()->flash('success', 'Booking submitted successfully for ' . $startDateTime->format('F j, Y') . ' from ' . $startDateTime->format('g:i A') . ' to ' . $endDateTime->format('g:i A') . '.');
            
            return redirect()->route('bookings.index');

        } catch (\Exception $e) {
            \Log::error('Booking creation failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to create booking. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.bookings.booking-create');
    }
}