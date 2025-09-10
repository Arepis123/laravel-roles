<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Vehicle;
use App\Models\ItAsset;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\BookingNotification;
use Carbon\Carbon;

class BookingMyEdit extends Component
{
    public Booking $booking;
    public string $asset_type = '';
    public string $asset_id = '';
    public string $booking_date = '';  
    public string $end_date = '';
    public string $start_time = '';   
    public string $end_time = '';     
    public string $purpose = '';
    public string $capacity = '';
    public array $passengers = []; 
    public string $destination = ''; 
    public $availablePassengers; 

    public array $additional_booking = [];
    public string $refreshment_details = '';

    protected $assetTypeConfig = [
        'meeting_room' => [
            'label' => 'Meeting Room',
            'model' => 'App\Models\MeetingRoom',
            'name_field' => 'name',
            'asset_label' => 'Location', // Dynamic label for asset
            'available_services' => ['refreshment', 'technical'], // Available additional services
            'allows_multi_day' => false, // Meeting rooms are single day only
            'show_capacity' => true
        ],
        'vehicle' => [
            'label' => 'Vehicle',
            'model' => 'App\Models\Vehicle',
            'name_field' => 'model',
            'asset_label' => 'Model', // Dynamic label for asset
            'available_services' => [], // No additional services for vehicles
            'allows_multi_day' => true, // Vehicles can be booked for multiple days
            'show_capacity' => true
        ],
        'it_asset' => [
            'label' => 'IT Asset',
            'model' => 'App\Models\ItAsset',
            'name_field' => 'name',
            'asset_label' => 'Asset', // Default label
            'available_services' => ['email'], // Only email setup for IT assets
            'allows_multi_day' => true, // IT assets can be borrowed for multiple days
            'show_capacity' => false // Hide capacity for IT assets
        ],
    ];

    public function mount(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->booked_by !== auth()->id()) {
            abort(403, 'Unauthorized to edit this booking.');
        }

        // Check if booking can still be edited (not started yet)
        if ($booking->start_time->isPast()) {
            abort(403, 'Cannot edit past bookings.');
        }

        $this->booking = $booking;
        $this->availablePassengers = collect(); // Initialize as empty collection
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
        
        // Load end_date for multi-day bookings
        if ($this->allowsMultiDayBooking && 
            $this->booking->end_time->format('Y-m-d') !== $this->booking->start_time->format('Y-m-d')) {
            $this->end_date = $this->booking->end_time->format('Y-m-d');
        } else {
            $this->end_date = '';
        }
        
        $this->start_time = $this->booking->start_time->format('H:i');
        $this->end_time = $this->booking->end_time->format('H:i');
        $this->purpose = $this->booking->purpose ?? '';
        $this->capacity = (string) ($this->booking->capacity ?? '');
        $this->additional_booking = $this->booking->additional_booking ?? [];
        $this->refreshment_details = $this->booking->refreshment_details ?? '';
        
        // Load vehicle-specific data
        if ($this->asset_type === 'vehicle') {
            $this->destination = $this->booking->destination ?? '';
            $this->passengers = $this->booking->passengers ?? [];
            
            // Load available passengers if there's capacity for more than 1
            if (is_numeric($this->capacity) && $this->capacity > 1) {
                $this->loadAvailablePassengers();
            }
        }
    }

    /**
     * Load available users for passenger selection
     */
    public function loadAvailablePassengers()
    {
        $this->availablePassengers = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();
    }

    /**
     * Get dynamic label for asset field based on asset type
     */
    public function getAssetFieldLabelProperty()
    {
        if (empty($this->asset_type) || !isset($this->assetTypeConfig[$this->asset_type])) {
            return 'Asset'; // Default label
        }
        
        return $this->assetTypeConfig[$this->asset_type]['asset_label'];
    }

    /**
     * Get available additional services based on asset type
     */
    public function getAvailableServicesProperty()
    {
        if (empty($this->asset_type) || !isset($this->assetTypeConfig[$this->asset_type])) {
            return [];
        }
        
        return $this->assetTypeConfig[$this->asset_type]['available_services'];
    }

    /**
     * Check if a specific service is available for the selected asset type
     */
    public function isServiceAvailable($service)
    {
        return in_array($service, $this->availableServices);
    }

    /**
     * Check if capacity field should be shown
     */
    public function getShouldShowCapacityProperty()
    {
        if (empty($this->asset_type) || !isset($this->assetTypeConfig[$this->asset_type])) {
            return false;
        }
        
        return $this->assetTypeConfig[$this->asset_type]['show_capacity'];
    }

    /**
     * Check if multi-day booking is allowed
     */
    public function getAllowsMultiDayBookingProperty()
    {
        if (empty($this->asset_type) || !isset($this->assetTypeConfig[$this->asset_type])) {
            return false;
        }
        
        return $this->assetTypeConfig[$this->asset_type]['allows_multi_day'];
    }

    /**
     * Get booking duration in days
     */
    public function getBookingDaysProperty()
    {
        if (!$this->booking_date) return 0;
        
        if (!$this->allowsMultiDayBooking || !$this->end_date) {
            return 1;
        }
        
        try {
            $start = Carbon::parse($this->booking_date);
            $end = Carbon::parse($this->end_date);
            return $start->diffInDays($end) + 1; // +1 to include both start and end dates
        } catch (\Exception $e) {
            return 1;
        }
    }

    /**
     * Get placeholder text for capacity field based on asset type
     */
    public function getCapacityPlaceholderProperty()
    {
        if ($this->asset_type === 'vehicle') {
            return 'No of driver and passenger';
        }
        
        return 'How many people';
    }

    /**
     * Check if passenger selection should be shown
     */
    public function getShouldShowPassengersProperty()
    {
        return $this->asset_type === 'vehicle' && 
               is_numeric($this->capacity) && 
               $this->capacity > 1;
    }

    /**
     * Get maximum passengers allowed based on capacity
     */
    public function getMaxPassengersProperty()
    {
        if (!is_numeric($this->capacity)) {
            return 0;
        }
        
        // Capacity minus 1 (the driver/current user)
        return max(0, intval($this->capacity) - 1);
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

        // Special handling for vehicles to include plate number
        if ($this->asset_type === 'vehicle') {
            return $model::select('id', 'model', 'plate_number')
                ->get()
                ->map(function ($vehicle) {
                    // Create a stdClass object to maintain consistency with the blade template
                    $item = new \stdClass();
                    $item->id = $vehicle->id;
                    $item->name = $vehicle->model . ' (' . $vehicle->plate_number . ')';
                    return $item;
                });
        }

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
            $this->end_date = '';
        }
        
        // Clear additional bookings that are not available for the new asset type
        $availableServices = $this->availableServices;
        $this->additional_booking = array_filter($this->additional_booking, function($service) use ($availableServices) {
            return in_array($service, $availableServices);
        });
        
        // Clear vehicle-specific fields if not vehicle
        if ($this->asset_type !== 'vehicle') {
            $this->passengers = [];
            $this->capacity = '';
            $this->destination = '';
            $this->availablePassengers = collect();
        }
        
        // Clear refreshment details if refreshment is not available
        if (!in_array('refreshment', $availableServices)) {
            $this->refreshment_details = '';
        }
    }

    public function updatedBookingDate()
    {
        // Reset time selections when date changes (only if different from original)
        if ($this->booking_date !== $this->booking->start_time->format('Y-m-d')) {
            $this->start_time = '';
            $this->end_time = '';
        }
        
        // If end date is before start date, clear it
        if ($this->end_date && $this->booking_date) {
            if (Carbon::parse($this->end_date)->lt(Carbon::parse($this->booking_date))) {
                $this->end_date = '';
            }
        }
    }

    public function updatedEndDate()
    {
        // Validate end date is not before start date
        if ($this->booking_date && $this->end_date) {
            if (Carbon::parse($this->end_date)->lt(Carbon::parse($this->booking_date))) {
                $this->addError('end_date', 'End date cannot be before start date.');
            }
        }
    }

    public function updatedCapacity()
    {
        // Reset passengers if capacity changes
        if ($this->asset_type === 'vehicle') {
            // Load available passengers when capacity changes
            if (is_numeric($this->capacity) && $this->capacity > 1) {
                $this->loadAvailablePassengers();
            } else {
                $this->availablePassengers = collect();
            }
            
            // If capacity is reduced, trim excess passengers
            if (is_numeric($this->capacity) && $this->capacity > 0) {
                $maxPassengers = $this->maxPassengers;
                if (count($this->passengers) > $maxPassengers) {
                    $this->passengers = array_slice($this->passengers, 0, $maxPassengers);
                }
            } else {
                // Clear passengers if capacity is invalid or 1
                $this->passengers = [];
            }
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
     * Handle when passengers array is updated (from wire:model)
     */
    public function updatedPassengers()
    {
        // Enforce capacity limit when passengers are selected via wire:model
        if ($this->asset_type === 'vehicle' && is_numeric($this->capacity) && $this->capacity > 0) {
            $maxPassengers = $this->maxPassengers;
            if (count($this->passengers) > $maxPassengers) {
                // Keep only the first allowed passengers and show error
                $this->passengers = array_slice($this->passengers, 0, $maxPassengers);
                $this->addError('passengers', "You can only select up to {$maxPassengers} passengers for this vehicle capacity of {$this->capacity}.");
            } else {
                // Clear the error if selection is valid
                $this->resetErrorBag('passengers');
            }
        }
    }

    /**
     * Toggle passenger selection
     */
    public function togglePassenger($userId)
    {
        if (in_array($userId, $this->passengers)) {
            $this->passengers = array_filter($this->passengers, function($id) use ($userId) {
                return $id != $userId;
            });
        } else {
            if (count($this->passengers) < $this->maxPassengers) {
                $this->passengers[] = $userId;
            }
        }
        
        // Re-index array
        $this->passengers = array_values($this->passengers);
    }

    /**
     * Get available time slots (every 30 minutes from 8 AM to 6 PM)
     */
    public function getAvailableTimeSlots()
    {
        $slots = [];
        $start = 8; // 8 AM
        $end = 18;  // 6 PM
        
        // For meeting rooms, check availability
        if ($this->asset_type === 'meeting_room') {
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
        } else {
            // For vehicles and IT assets (multi-day bookings)
            for ($hour = $start; $hour < $end; $hour++) {
                for ($minute = 0; $minute < 60; $minute += 30) {
                    $timeString = sprintf('%02d:%02d', $hour, $minute);
                    $displayTime = Carbon::createFromFormat('H:i', $timeString)->format('g:i A');
                    
                    // Skip past times for today's bookings
                    if ($this->booking_date === date('Y-m-d')) {
                        $timeToCheck = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $timeString);
                        if ($timeToCheck->isPast()) {
                            continue;
                        }
                    }
                    
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
        if (!$this->start_time) {
            return [];
        }
        
        $slots = [];
        
        try {
            $startTime = Carbon::createFromFormat('H:i', $this->start_time);
            $endOfDay = Carbon::createFromFormat('H:i', '18:00'); // 6 PM
            
            // For multi-day bookings or when no specific date checking is needed
            if ($this->allowsMultiDayBooking) {
                $currentTime = $startTime->copy()->addMinutes(30);
                while ($currentTime->lte($endOfDay)) {
                    $timeString = $currentTime->format('H:i');
                    $displayTime = $currentTime->format('g:i A');
                    $slots[$timeString] = $displayTime;
                    $currentTime->addMinutes(30);
                }
            } else {
                // For meeting rooms, check availability
                if (!$this->asset_type || !$this->asset_id || !$this->booking_date) {
                    return [];
                }
                
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
                ->whereNotIn('status', ['cancelled', 'done'])
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
        // For multi-day bookings (vehicles and IT assets)
        if ($this->allowsMultiDayBooking && $this->bookingDays > 1) {
            return $this->bookingDays . ' ' . Str::plural('day', $this->bookingDays) . ' total';
        }
        
        // For single day bookings (meeting rooms)
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
        // Handle array properties differently
        if (strpos($propertyName, 'additional_booking') === 0) {
            // Handle additional_booking array updates
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
            
            // For multi-day bookings, use end_date if provided
            $endDateStr = ($this->allowsMultiDayBooking && $this->end_date) ? $this->end_date : $this->booking_date;
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $endDateStr . ' ' . $this->end_time);
            
            // Check if booking is not in the past (for today's bookings)
            if ($startDateTime->isToday() && $startDateTime->isPast()) {
                $this->addError('start_time', 'Start time cannot be in the past for today\'s bookings.');
                return false;
            }

            // Check if end time is after start time (for same-day bookings)
            if (!$this->allowsMultiDayBooking || !$this->end_date || $this->booking_date === $this->end_date) {
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

                // Check maximum booking duration (max 8 hours for single day)
                if ($endDateTime->diffInHours($startDateTime) > 8) {
                    $this->addError('end_time', 'Single day booking cannot exceed 8 hours.');
                    return false;
                }
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
            'additional_booking' => 'array',
            'passengers' => 'array',
            'passengers.*' => 'exists:users,id',
        ];

        // Add capacity validation only if it should be shown (Meeting Room and Vehicle)
        if ($this->shouldShowCapacity) {
            $rules['capacity'] = 'required|numeric|min:1';
        }

        // Add destination validation for vehicles
        if ($this->asset_type === 'vehicle') {
            $rules['destination'] = 'required|string|min:3';
        }

        // Add end_date validation for multi-day bookings
        if ($this->allowsMultiDayBooking && $this->end_date) {
            $rules['end_date'] = 'required|date|after_or_equal:booking_date';
        }

        // Dynamic validation for additional booking options based on asset type
        if (!empty($this->asset_type) && isset($this->assetTypeConfig[$this->asset_type])) {
            $availableServices = $this->assetTypeConfig[$this->asset_type]['available_services'];
            if (!empty($availableServices)) {
                $rules['additional_booking.*'] = 'string|in:' . implode(',', $availableServices);
            }
        }

        // Add conditional rule for refreshment_details
        if (in_array('refreshment', $this->additional_booking)) {
            $rules['refreshment_details'] = 'required|string|min:3';
        }

        // Add validation for passengers if vehicle with capacity > 1
        if ($this->asset_type === 'vehicle' && is_numeric($this->capacity) && $this->capacity > 1) {
            $maxPassengers = $this->maxPassengers;
            $rules['passengers'] = "array|max:{$maxPassengers}";
        }

        return $rules;
    }

    /**
     * Get custom validation messages
     */
    public function messages()
    {
        return [
            'asset_type.required' => 'Please select a booking type.',
            'asset_id.required' => 'Please select an ' . strtolower($this->assetFieldLabel) . '.',
            'booking_date.required' => 'Please select a start date.',
            'booking_date.after_or_equal' => 'Start date must be today or in the future.',
            'end_date.required' => 'Please select an end date.',
            'end_date.after_or_equal' => 'End date must be on or after the start date.',
            'start_time.required' => 'Please select a start time.',
            'start_time.date_format' => 'Please enter a valid start time.',
            'end_time.required' => 'Please select an end time.',
            'end_time.date_format' => 'Please enter a valid end time.',
            'purpose.required' => 'Please provide a purpose for the booking.',
            'purpose.min' => 'Purpose must be at least 3 characters long.',
            'capacity.numeric' => 'Capacity must be a number.',
            'capacity.min' => 'Capacity must be at least 1.',
            'destination.required' => 'Please provide the destination.',
            'destination.min' => 'Destination must be at least 3 characters long.',
            'refreshment_details.required' => 'Please provide refreshment details.',
            'refreshment_details.min' => 'Refreshment details must be at least 3 characters long.',
            'additional_booking.*.in' => 'Invalid additional service option selected.',
            'passengers.max' => 'You can select a maximum of ' . $this->maxPassengers . ' passengers.',
            'passengers.*.exists' => 'Invalid passenger selected.',
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
            
            // For multi-day bookings, use end_date; otherwise use booking_date
            $endDateStr = ($this->allowsMultiDayBooking && $this->end_date) ? $this->end_date : $this->booking_date;
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $endDateStr . ' ' . $this->end_time);

            // Check for conflicting bookings (for meeting rooms only)
            if ($this->asset_type === 'meeting_room') {
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
                    $this->addError('booking_date', 'This ' . strtolower($this->assetFieldLabel) . ' is already booked for the selected time slot.');
                    return;
                }
            }

            // Prepare booking data
            $bookingData = [
                'asset_type' => $this->assetTypeConfig[$this->asset_type]['model'],
                'asset_id' => $this->asset_id,
                'purpose' => $this->purpose,
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'capacity' => $this->shouldShowCapacity && $this->capacity ? $this->capacity : null,
                'additional_booking' => $this->additional_booking,
                'refreshment_details' => in_array('refreshment', $this->additional_booking) ? $this->refreshment_details : null,
            ];

            // Add vehicle-specific data
            if ($this->asset_type === 'vehicle') {
                $bookingData['destination'] = $this->destination;
                
                // Add passengers data if there are passengers
                if (!empty($this->passengers)) {
                    $bookingData['passengers'] = $this->passengers;
                } else {
                    $bookingData['passengers'] = [];
                }
            }

            // Update the booking
            $this->booking->update($bookingData);

            // Send email notification to admin about the update
            try {
                Mail::to('e-booking@clab.com.my')
                    ->send(new BookingNotification($this->booking, auth()->user(), 'updated'));
                
                \Log::info('Booking update notification email sent to admin', [
                    'booking_id' => $this->booking->id,
                    'user_id' => auth()->id(),
                    'admin_email' => 'e-booking@clab.com.my'
                ]);
            } catch (\Exception $mailException) {
                // Log the error but don't fail the booking update
                \Log::error('Failed to send booking update notification email', [
                    'booking_id' => $this->booking->id,
                    'error' => $mailException->getMessage()
                ]);
            }

            // Create success message based on booking type
            $successMessage = 'Booking updated successfully for ';
            if ($this->bookingDays > 1) {
                $successMessage .= $this->bookingDays . ' days from ' . $startDateTime->format('F j, Y') . ' to ' . $endDateTime->format('F j, Y');
            } else {
                $successMessage .= $startDateTime->format('F j, Y') . ' from ' . $startDateTime->format('g:i A') . ' to ' . $endDateTime->format('g:i A');
            }
            $successMessage .= '. Admin has been notified via email.';

            session()->flash('success', $successMessage);
            
            return redirect()->route('bookings.index.user');

        } catch (\Exception $e) {
            \Log::error('Booking update failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to update booking. Please try again.');
        }
    }

    /**
     * Cancel the booking (user can only cancel if status is pending)
     */
    public function cancel()
    {
        // Check if user can cancel (only pending bookings)
        if ($this->booking->status !== 'pending') {
            session()->flash('error', 'You can only cancel pending bookings.');
            return;
        }

        try {
            $this->booking->update([
                'status' => 'cancelled',
                'status_history' => array_merge($this->booking->status_history ?? [], [
                    [
                        'status' => 'cancelled',
                        'changed_at' => now()->toISOString(),
                        'changed_by' => auth()->id(),
                        'reason' => 'Cancelled by user'
                    ]
                ])
            ]);
            
            // Send cancellation notification
            try {
                Mail::to('e-booking@clab.com.my')
                    ->send(new BookingNotification($this->booking, auth()->user(), 'cancelled'));
            } catch (\Exception $mailException) {
                \Log::error('Failed to send cancellation notification', [
                    'booking_id' => $this->booking->id,
                    'error' => $mailException->getMessage()
                ]);
            }
            
            session()->flash('success', 'Booking has been cancelled successfully.');
            return redirect()->route('bookings.index.user');
        } catch (\Exception $e) {
            \Log::error('Booking cancellation failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to cancel booking. Please try again.');
        }
    }

    /**
     * Mark booking as done (user can only mark as done if status is approved and booking has ended)
     */
    public function markDone()
    {
        // Check if user can mark as done (only approved bookings that have ended)
        if ($this->booking->status !== 'approved') {
            session()->flash('error', 'You can only mark approved bookings as done.');
            return;
        }

        if ($this->booking->end_time->isFuture()) {
            session()->flash('error', 'You can only mark bookings as done after they have ended.');
            return;
        }

        try {
            $this->booking->update([
                'status' => 'done',
                'status_history' => array_merge($this->booking->status_history ?? [], [
                    [
                        'status' => 'done',
                        'changed_at' => now()->toISOString(),
                        'changed_by' => auth()->id(),
                        'reason' => 'Marked as done by user'
                    ]
                ])
            ]);
            
            // Send notification to admin
            try {
                Mail::to('e-booking@clab.com.my')
                    ->send(new BookingNotification($this->booking, auth()->user(), 'completed'));
            } catch (\Exception $mailException) {
                \Log::error('Failed to send completion notification', [
                    'booking_id' => $this->booking->id,
                    'error' => $mailException->getMessage()
                ]);
            }
            
            session()->flash('success', 'Booking has been marked as completed.');
            return redirect()->route('bookings.index.user');
        } catch (\Exception $e) {
            \Log::error('Booking completion failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to mark booking as done. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.bookings.booking-my-edit');
    }
}