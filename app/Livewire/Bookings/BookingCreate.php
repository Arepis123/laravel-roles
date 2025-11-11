<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Vehicle;
use App\Models\VehicleMaintenanceLog;
use App\Models\ItAsset;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\BookingNotification;
use Carbon\Carbon;

class BookingCreate extends Component
{
    public string $asset_type = '';
    public string $asset_id = '';
    public string $booking_date = '';  
    public string $end_date = ''; // Add end date for multi-day bookings
    public string $start_time = '';   
    public string $end_time = '';     
    public string $purpose = '';
    public string $capacity = '';     
    public array $passengers = [];
    public string $destination = ''; 
    public $availablePassengers;

    public $additional_booking = [];
    public string $refreshment_details = '';
    public bool $vehicleUnderMaintenance = false;
    
    // Pro features support
    public bool $saving = false;

    protected $assetTypeConfig = [
        'meeting_room' => [
            'label' => 'Meeting Room',
            'model' => MeetingRoom::class,
            'name_field' => 'name',
            'asset_label' => 'Location', // Dynamic label for asset
            'available_services' => ['refreshment', 'technical'], // Available additional services
            'allows_multi_day' => false, // Meeting rooms are single day only
            'show_capacity' => true
        ],
        'vehicle' => [
            'label' => 'Vehicle',
            'model' => Vehicle::class,
            'name_field' => 'model',
            'asset_label' => 'Model', // Dynamic label for asset
            'available_services' => [], // No additional services for vehicles
            'allows_multi_day' => true, // Vehicles can be booked for multiple days
            'show_capacity' => true
        ],
        'it_asset' => [
            'label' => 'IT Asset',
            'model' => ItAsset::class,
            'name_field' => 'name',
            'asset_label' => 'Asset', // Default label
            'available_services' => ['email'], // Only email setup for IT assets
            'allows_multi_day' => true, // IT assets can be borrowed for multiple days
            'show_capacity' => false // Hide capacity for IT assets
        ],
    ];

    public function mount()
    {
        // Initialize properties as empty arrays
        $this->additional_booking = [];
        $this->passengers = [];
        $this->availablePassengers = collect(); // Initialize as empty collection
    }

    /**
     * Load available users for passenger selection
     */
    public function loadAvailablePassengers()
    {
        $this->availablePassengers = User::notDeleted()
            ->where('id', '!=', Auth::id())
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
     * Check if selected vehicle has maintenance conflict with booking dates
     */
    public function getVehicleMaintenanceConflictProperty()
    {
        if ($this->asset_type !== 'vehicle' || !$this->asset_id || !$this->booking_date) {
            return null;
        }

        $vehicle = Vehicle::find($this->asset_id);
        if (!$vehicle) {
            return null;
        }

        $maintenanceStatus = $vehicle->maintenanceStatus;

        // Check for ongoing maintenance
        if ($maintenanceStatus['status'] === 'ongoing') {
            return [
                'type' => 'error',
                'message' => '🔧 This vehicle is currently under maintenance and cannot be booked.',
                'details' => $maintenanceStatus['message']
            ];
        }

        // Check for scheduled maintenance conflict
        if ($maintenanceStatus['status'] === 'scheduled') {
            $startDateTime = Carbon::parse($this->booking_date . ' 00:00');
            $endDateStr = $this->end_date ?: $this->booking_date;
            $endDateTime = Carbon::parse($endDateStr . ' 23:59');

            if ($vehicle->hasScheduledMaintenanceInPeriod($startDateTime, $endDateTime)) {
                return [
                    'type' => 'warning',
                    'message' => 'Maintenance Scheduled',
                    'details' => 'This vehicle has scheduled maintenance during your selected booking period. Please choose different dates or select another vehicle.'
                ];
            }
        }

        return null;
    }

    /**
     * Get existing bookings for the selected asset and date
     */
    public function getExistingBookingsProperty()
    {
        if (!$this->asset_type || !$this->asset_id || !$this->booking_date) {
            return collect();
        }

        $assetModel = $this->assetTypeConfig[$this->asset_type]['model'];
        $endDateStr = $this->end_date ?: $this->booking_date;

        // For multi-day bookings, get all bookings that overlap with the date range
        if ($this->allowsMultiDayBooking) {
            return Booking::where('asset_type', $assetModel)
                ->where('asset_id', $this->asset_id)
                ->whereIn('status', ['pending', 'approved'])
                ->where(function($query) use ($endDateStr) {
                    $query->where(function($dateOverlap) use ($endDateStr) {
                        // Existing booking overlaps with our date range
                        $dateOverlap->whereDate('start_time', '<=', $endDateStr)
                                   ->whereDate('end_time', '>=', $this->booking_date);
                    });
                })
                ->with('user:id,name')
                ->orderBy('start_time')
                ->get();
        } else {
            // For single-day bookings (meeting rooms), get bookings on the selected date
            return Booking::where('asset_type', $assetModel)
                ->where('asset_id', $this->asset_id)
                ->whereIn('status', ['pending', 'approved'])
                ->whereDate('start_time', $this->booking_date)
                ->with('user:id,name')
                ->orderBy('start_time')
                ->get();
        }
    }

    /**
     * #3: Get suggested alternative time slots when conflicts occur
     */
    public function getSuggestedTimeSlotsProperty()
    {
        if (!$this->asset_type || !$this->asset_id || !$this->booking_date) {
            return collect();
        }

        // Only suggest if user has attempted to select times
        if (!$this->start_time || !$this->end_time) {
            return collect();
        }

        // Check if current selection has conflicts
        if ($this->isTimeRangeAvailable($this->start_time, $this->end_time)) {
            return collect(); // No conflicts, no need for suggestions
        }

        $assetModel = $this->assetTypeConfig[$this->asset_type]['model'];
        $suggestions = collect();

        // Get all bookings for the day
        $existingBookings = $this->existingBookings;

        if ($existingBookings->isEmpty()) {
            return collect();
        }

        try {
            $requestedStart = Carbon::createFromFormat('H:i', $this->start_time);
            $requestedEnd = Carbon::createFromFormat('H:i', $this->end_time);
            $requestedDuration = $requestedStart->diffInMinutes($requestedEnd);

            // Find gaps between bookings
            $workDayStart = Carbon::createFromFormat('H:i', '08:00');
            $workDayEnd = Carbon::createFromFormat('H:i', '22:00');

            $sortedBookings = $existingBookings->sortBy('start_time');

            // Check if there's a slot before the first booking
            $firstBooking = $sortedBookings->first();
            $slotStart = $workDayStart->copy();
            $firstBookingStart = Carbon::parse($firstBooking->start_time);

            if ($slotStart->lt($firstBookingStart)) {
                $gapDuration = $slotStart->diffInMinutes($firstBookingStart);
                if ($gapDuration >= $requestedDuration) {
                    $slotEnd = $slotStart->copy()->addMinutes($requestedDuration);
                    $suggestions->push([
                        'start_time' => $slotStart->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'label' => $slotStart->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                        'duration' => $requestedDuration,
                    ]);
                }
            }

            // Check gaps between consecutive bookings
            for ($i = 0; $i < $sortedBookings->count() - 1; $i++) {
                $currentBooking = $sortedBookings[$i];
                $nextBooking = $sortedBookings[$i + 1];

                $gapStart = Carbon::parse($currentBooking->end_time);
                $gapEnd = Carbon::parse($nextBooking->start_time);

                $gapDuration = $gapStart->diffInMinutes($gapEnd);

                if ($gapDuration >= $requestedDuration) {
                    $slotEnd = $gapStart->copy()->addMinutes($requestedDuration);
                    $suggestions->push([
                        'start_time' => $gapStart->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'label' => $gapStart->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                        'duration' => $requestedDuration,
                    ]);
                }
            }

            // Check if there's a slot after the last booking
            $lastBooking = $sortedBookings->last();
            $lastBookingEnd = Carbon::parse($lastBooking->end_time);

            if ($lastBookingEnd->lt($workDayEnd)) {
                $gapDuration = $lastBookingEnd->diffInMinutes($workDayEnd);
                if ($gapDuration >= $requestedDuration) {
                    $slotEnd = $lastBookingEnd->copy()->addMinutes($requestedDuration);
                    $suggestions->push([
                        'start_time' => $lastBookingEnd->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'label' => $lastBookingEnd->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                        'duration' => $requestedDuration,
                    ]);
                }
            }

            // Limit to 3 suggestions
            return $suggestions->take(3);

        } catch (\Exception $e) {
            \Log::error('Error generating suggested time slots:', [
                'error' => $e->getMessage(),
                'asset_type' => $this->asset_type,
                'asset_id' => $this->asset_id,
                'booking_date' => $this->booking_date,
            ]);
            return collect();
        }
    }

    /**
     * #3: Apply suggested time slot
     */
    public function applySuggestion($startTime, $endTime)
    {
        $this->start_time = $startTime;
        $this->end_time = $endTime;
        $this->dispatch('timeSlotApplied');
    }

    /**
     * Get quick duration options based on asset type
     */
    public function getQuickDurationOptionsProperty()
    {
        if (!$this->asset_type) {
            return [];
        }

        // Define duration options for each asset type
        $durationOptions = [
            'meeting_room' => [
                ['minutes' => 60, 'label' => '1 hour'],
                ['minutes' => 120, 'label' => '2 hours'],
                ['minutes' => 180, 'label' => '3 hours'],
                ['minutes' => 240, 'label' => '4 hours'],
            ],
            'vehicle' => [
                ['minutes' => 60, 'label' => '1 hour'],
                ['minutes' => 120, 'label' => '2 hours'],
                ['minutes' => 180, 'label' => '3 hours'],
                ['minutes' => 240, 'label' => '4 hours'],
            ],
            'it_asset' => [
                ['minutes' => 240, 'label' => '4 hours'],
                ['minutes' => 480, 'label' => '8 hours'],
                ['minutes' => 1440, 'label' => '1 day'], // 24 hours = 1 day
                ['minutes' => 2880, 'label' => '2 days'], // 48 hours = 2 days
            ],
        ];

        return $durationOptions[$this->asset_type] ?? [];
    }

    /**
     * Quick Time Slot Selection - Apply preset duration from start time
     * Supports both same-day and multi-day bookings
     */
    public function applyQuickDuration($minutes)
    {
        if (!$this->start_time || !$this->booking_date) {
            return;
        }

        try {
            $startTime = Carbon::createFromFormat('H:i', $this->start_time);

            // For durations >= 1 day (1440 minutes), handle multi-day bookings
            if ($minutes >= 1440 && $this->allowsMultiDayBooking) {
                $days = floor($minutes / 1440);
                $remainingMinutes = $minutes % 1440;

                // Set end date
                $endDate = Carbon::parse($this->booking_date)->addDays($days);
                $this->end_date = $endDate->format('Y-m-d');

                // Set end time (add remaining minutes to start time)
                $endTime = $startTime->copy()->addMinutes($remainingMinutes);

                // Make sure end time doesn't exceed 22:00 (10 PM)
                $maxEndTime = Carbon::createFromFormat('H:i', '22:00');
                if ($endTime->gt($maxEndTime)) {
                    $endTime = $maxEndTime;
                }

                $this->end_time = $endTime->format('H:i');
            } else {
                // For same-day bookings (< 1 day or non-multi-day asset types)
                $endTime = $startTime->copy()->addMinutes($minutes);

                // Make sure it doesn't exceed 22:00 (10 PM) for same-day bookings
                $maxEndTime = Carbon::createFromFormat('H:i', '22:00');
                if ($endTime->gt($maxEndTime)) {
                    $endTime = $maxEndTime;
                }

                $this->end_time = $endTime->format('H:i');

                // Clear end_date if it was set
                if ($this->allowsMultiDayBooking) {
                    $this->end_date = '';
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error applying quick duration:', [
                'error' => $e->getMessage(),
                'start_time' => $this->start_time,
                'booking_date' => $this->booking_date,
                'duration' => $minutes
            ]);
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
                'label' => $config['label'],
                'description' => match($key) {
                    'meeting_room' => 'Book conference rooms and meeting spaces',
                    'vehicle' => 'Book company vehicles for transportation',
                    'it_asset' => 'Book IT equipment like laptops, projectors',
                    default => ''
                }
            ];
        });
    }

    /**
     * Toggle favorite status for an asset
     */
    public function toggleFavorite($assetId)
    {
        if (!$this->asset_type || !$assetId) {
            return;
        }

        $assetModel = $this->assetTypeConfig[$this->asset_type]['model'];
        auth()->user()->toggleFavorite($assetModel, $assetId);

        // Re-render to show updated favorite status
        $this->dispatch('favoriteToggled');
    }

    public function getAssetOptionsProperty()
    {
        if (empty($this->asset_type) || !isset($this->assetTypeConfig[$this->asset_type])) {
            return collect();
        }

        $config = $this->assetTypeConfig[$this->asset_type];
        $model = $config['model'];
        $nameField = $config['name_field'];
        $currentUser = auth()->user();

        // Special handling for vehicles to include plate number, access filtering, and availability status
        if ($this->asset_type === 'vehicle') {
            return $model::select('id', 'model', 'plate_number', 'allowed_positions', 'allowed_users')
                ->with('maintenanceLogs')
                ->get()
                ->filter(function ($vehicle) use ($currentUser) {
                    return $vehicle->canUserBook($currentUser);
                })
                ->map(function ($vehicle) use ($currentUser, $model) {
                    // Create a stdClass object to maintain consistency with the blade template
                    $item = new \stdClass();
                    $item->id = $vehicle->id;
                    $item->name = $vehicle->model . ' (' . $vehicle->plate_number . ')';
                    $item->is_favorite = $currentUser->hasFavorited($model, $vehicle->id);

                    // #6: Add availability indicator
                    $availabilityInfo = $this->getAssetAvailability($vehicle->id, 'vehicle');

                    // Combine access restriction and availability info
                    $hasPositions = !empty($vehicle->allowed_positions);
                    $hasUsers = !empty($vehicle->allowed_users);

                    $descriptionParts = [];

                    if ($hasPositions && $hasUsers) {
                        $descriptionParts[] = 'Position: ' . implode(', ', $vehicle->allowed_positions) . ' + Specific users';
                    } elseif ($hasUsers) {
                        $descriptionParts[] = 'Specific users only';
                    } elseif ($hasPositions) {
                        $descriptionParts[] = 'Positions: ' . implode(', ', $vehicle->allowed_positions);
                    }

                    if ($availabilityInfo) {
                        $descriptionParts[] = $availabilityInfo;
                    }

                    $item->description = !empty($descriptionParts) ? implode(' • ', $descriptionParts) : 'Available to all users';
                    $item->availability = $availabilityInfo;

                    return $item;
                })
                ->sortByDesc('is_favorite') // Sort favorites first
                ->values(); // Reset array keys after filtering
        }

        // Special handling for IT assets to include asset tag and availability
        if ($this->asset_type === 'it_asset') {
            return $model::select('id', 'name', 'asset_tag', 'location', 'specs')
                ->get()
                ->map(function ($asset) use ($currentUser, $model) {
                    $item = new \stdClass();
                    $item->id = $asset->id;
                    $item->name = $asset->name . ' (' . $asset->asset_tag . ')';
                    $item->is_favorite = $currentUser->hasFavorited($model, $asset->id);

                    // #6: Add availability indicator
                    $availabilityInfo = $this->getAssetAvailability($asset->id, 'it_asset');

                    $descriptionParts = [];
                    if ($asset->location) {
                        $descriptionParts[] = $asset->location;
                    } elseif ($asset->specs) {
                        $descriptionParts[] = $asset->specs;
                    }

                    if ($availabilityInfo) {
                        $descriptionParts[] = $availabilityInfo;
                    }

                    $item->description = !empty($descriptionParts) ? implode(' • ', $descriptionParts) : '';
                    $item->availability = $availabilityInfo;

                    return $item;
                })
                ->sortByDesc('is_favorite') // Sort favorites first
                ->values();
        }

        // For meeting rooms with availability indicator
        if ($this->asset_type === 'meeting_room') {
            return $model::select('id', "{$nameField} as name", 'location')
                ->get()
                ->map(function ($room) use ($currentUser, $model) {
                    $item = new \stdClass();
                    $item->id = $room->id;
                    $item->name = $room->name;
                    $item->is_favorite = $currentUser->hasFavorited($model, $room->id);

                    // #6: Add availability indicator
                    $availabilityInfo = $this->getAssetAvailability($room->id, 'meeting_room');

                    $descriptionParts = [];
                    if ($room->location) {
                        $descriptionParts[] = $room->location;
                    }

                    if ($availabilityInfo) {
                        $descriptionParts[] = $availabilityInfo;
                    }

                    $item->description = !empty($descriptionParts) ? implode(' • ', $descriptionParts) : '';
                    $item->availability = $availabilityInfo;

                    return $item;
                })
                ->sortByDesc('is_favorite') // Sort favorites first
                ->values();
        }

        // Fallback
        return $model::select('id', "{$nameField} as name")->get();
    }

    /**
     * #6: Get real-time availability info for an asset
     */
    protected function getAssetAvailability($assetId, $assetType)
    {
        if (!$this->booking_date) {
            return null; // No date selected, can't determine availability
        }

        $assetModel = $this->assetTypeConfig[$assetType]['model'];
        $now = Carbon::now();
        $bookingDate = Carbon::parse($this->booking_date);
        $isToday = $bookingDate->isToday();

        // Check for ongoing bookings (only relevant for today)
        if ($isToday) {
            $ongoingBooking = Booking::where('asset_type', $assetModel)
                ->where('asset_id', $assetId)
                ->whereIn('status', ['pending', 'approved'])
                ->where('start_time', '<=', $now)
                ->where('end_time', '>', $now)
                ->first();

            if ($ongoingBooking) {
                $availableAt = Carbon::parse($ongoingBooking->end_time);
                return 'In use until ' . $availableAt->format('g:i A');
            }
        }

        // Check for upcoming bookings on the selected date
        $nextBooking = Booking::where('asset_type', $assetModel)
            ->where('asset_id', $assetId)
            ->whereIn('status', ['pending', 'approved'])
            ->whereDate('start_time', $bookingDate)
            ->where('start_time', '>', $now)
            ->orderBy('start_time')
            ->first();

        if ($nextBooking) {
            $bookingStart = Carbon::parse($nextBooking->start_time);
            if ($isToday) {
                return 'Next booking at ' . $bookingStart->format('g:i A');
            } else {
                return 'Has bookings on this date';
            }
        }

        // No bookings found
        if ($isToday) {
            return 'Available now';
        } else {
            return 'Available all day';
        }
    }

    public function updatedAssetType()
    {
        $this->asset_id = '';
        // Reset time selections when asset type changes
        $this->start_time = '';
        $this->end_time = '';
        $this->end_date = '';
        
        // Clear additional bookings that are not available for the new asset type
        $availableServices = $this->availableServices;
        
        // Ensure additional_booking is an array before filtering
        if (!is_array($this->additional_booking)) {
            $this->additional_booking = [];
        }
        
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
        // Reset time selections when date changes
        $this->start_time = '';
        $this->end_time = '';
        
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
        
        // Reset time selections when end date changes for multi-day bookings
        if ($this->allowsMultiDayBooking) {
            $this->start_time = '';
            $this->end_time = '';
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
        // Reset time selections when asset changes
        $this->start_time = '';
        $this->end_time = '';
        
        // Check if selected vehicle is under maintenance or has maintenance conflicts
        if ($this->asset_type === 'vehicle' && $this->asset_id) {
            $vehicle = Vehicle::find($this->asset_id);
            if ($vehicle) {
                $this->vehicleUnderMaintenance = $vehicle->hasOngoingMaintenance();
            } else {
                $this->vehicleUnderMaintenance = false;
            }
        } else {
            $this->vehicleUnderMaintenance = false;
        }
    }

    public function updatedStartTime()
    {
        // Reset end time when start time changes
        $this->end_time = '';
    }

    // Add this method to handle additional_booking updates
    public function updatedAdditionalBooking()
    {
        // Ensure additional_booking is always an array
        if (!is_array($this->additional_booking)) {
            $this->additional_booking = [];
        }
        
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
            $this->passengers = array_filter($this->passengers, fn($id) => $id != $userId);
        } else {
            if (count($this->passengers) < $this->maxPassengers) {
                $this->passengers[] = $userId;
            }
        }
    }

    /**
     * Deselect all passengers
     */
    public function deselectAllPassengers()
    {
        $this->passengers = [];
        $this->resetErrorBag('passengers');
    }

    /**
     * Get available time slots (every 30 minutes from 8 AM to 22 PM)
     */
    public function getAvailableTimeSlots()
    {
        $slots = [];
        $start = 8; // 8 AM
        $end = 22;  // 10 PM
        
        // Return empty if required fields not selected
        if (!$this->asset_type || !$this->asset_id || !$this->booking_date) {
            return $slots;
        }
        
        // Generate time slots and check availability for ALL asset types
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

                // Check availability for ALL asset types
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
        if (!$this->start_time) {
            return [];
        }

        $slots = [];

        try {
            $startTime = Carbon::createFromFormat('H:i', $this->start_time);
            $endOfDay = Carbon::createFromFormat('H:i', '22:00'); // 10 PM

            // Determine if this is ACTUALLY a multi-day booking based on dates
            $endDateStr = $this->end_date ?: $this->booking_date;
            $isActuallyMultiDay = $this->allowsMultiDayBooking && $this->end_date && ($this->end_date !== $this->booking_date);

            // For ACTUAL multi-day bookings, don't check time conflicts (entire days are blocked)
            if ($isActuallyMultiDay) {
                $currentTime = $startTime->copy()->addMinutes(30);
                while ($currentTime->lte($endOfDay)) {
                    $timeString = $currentTime->format('H:i');
                    $displayTime = $currentTime->format('g:i A');
                    $slots[$timeString] = $displayTime;
                    $currentTime->addMinutes(30);
                }
            } else {
                // For single-day bookings (including single-day vehicle/IT asset bookings), check availability
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
     * FIXED: Properly handles multi-day booking overlaps
     */
    protected function isTimeSlotAvailable($timeSlot)
    {
        if (!$this->asset_type || !$this->asset_id || !$this->booking_date) {
            return false;
        }

        try {
            // Get the model class for this asset type
            $assetModel = $this->assetTypeConfig[$this->asset_type]['model'];

            // Check for vehicle maintenance conflicts (vehicles only)
            if ($this->asset_type === 'vehicle') {
                $vehicle = Vehicle::find($this->asset_id);
                if ($vehicle) {
                    // Check for ongoing maintenance
                    if ($vehicle->hasOngoingMaintenance()) {
                        return false; // Vehicle is under maintenance, no time slots available
                    }

                    // For date-specific checks, we'll validate during booking creation
                    // since hasAvailableTimeSlots doesn't have date context
                }
            }

            // Determine if this is ACTUALLY a multi-day booking based on dates
            $endDateStr = $this->end_date ?: $this->booking_date;
            $isActuallyMultiDay = $this->allowsMultiDayBooking && $this->end_date && ($this->end_date !== $this->booking_date);

            // For ACTUAL multi-day bookings, check if the entire date range has conflicts
            if ($isActuallyMultiDay) {
                $conflictingBooking = Booking::where('asset_type', $assetModel)
                    ->where('asset_id', $this->asset_id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->where(function($query) use ($endDateStr) {
                        // Check for date range overlaps
                        $query->where(function($dateOverlap) use ($endDateStr) {
                            // Case 1: Existing booking starts during our requested period
                            $dateOverlap->whereDate('start_time', '>=', $this->booking_date)
                                       ->whereDate('start_time', '<=', $endDateStr);
                        })
                        ->orWhere(function($dateOverlap) use ($endDateStr) {
                            // Case 2: Existing booking ends during our requested period
                            $dateOverlap->whereDate('end_time', '>=', $this->booking_date)
                                       ->whereDate('end_time', '<=', $endDateStr);
                        })
                        ->orWhere(function($dateOverlap) use ($endDateStr) {
                            // Case 3: Existing booking completely encompasses our requested period
                            $dateOverlap->whereDate('start_time', '<=', $this->booking_date)
                                       ->whereDate('end_time', '>=', $endDateStr);
                        })
                        ->orWhere(function($dateOverlap) use ($endDateStr) {
                            // Case 4: Our requested period encompasses existing booking
                            $dateOverlap->whereDate('start_time', '>=', $this->booking_date)
                                       ->whereDate('end_time', '<=', $endDateStr);
                        });
                    })
                    ->exists();

                return !$conflictingBooking;
            } else {
                // For single-day bookings (meeting rooms, or single-day vehicle/IT asset bookings), check specific time slots
                $checkDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $timeSlot);

                $conflictingBooking = Booking::where('asset_type', $assetModel)
                    ->where('asset_id', $this->asset_id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->where('start_time', '<=', $checkDateTime)
                    ->where('end_time', '>', $checkDateTime)
                    ->exists();

                return !$conflictingBooking;
            }

        } catch (\Exception $e) {
            \Log::error('Error checking time slot availability:', [
                'error' => $e->getMessage(),
                'time_slot' => $timeSlot,
                'asset_type' => $this->asset_type,
                'asset_id' => $this->asset_id,
                'booking_date' => $this->booking_date,
                'end_date' => $this->end_date
            ]);
            return false;
        }
    }

    /**
     * Check if a time range is available for the selected asset
     * FIXED: Properly handles multi-day booking overlaps
     */
    protected function isTimeRangeAvailable($startTime, $endTime)
    {
        if (!$this->asset_type || !$this->asset_id || !$this->booking_date) {
            return false;
        }

        try {
            // Get the model class for this asset type
            $assetModel = $this->assetTypeConfig[$this->asset_type]['model'];

            // Determine if this is ACTUALLY a multi-day booking based on dates
            $endDateStr = $this->end_date ?: $this->booking_date;
            $isActuallyMultiDay = $this->allowsMultiDayBooking && $this->end_date && ($this->end_date !== $this->booking_date);

            // For ACTUAL multi-day bookings (vehicles and IT assets)
            if ($isActuallyMultiDay) {
                $conflictingBooking = Booking::where('asset_type', $assetModel)
                    ->where('asset_id', $this->asset_id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->where(function($query) use ($endDateStr) {
                        // Check for date range overlaps
                        $query->where(function($dateOverlap) use ($endDateStr) {
                            // Case 1: Existing booking starts during our requested period
                            $dateOverlap->whereDate('start_time', '>=', $this->booking_date)
                                       ->whereDate('start_time', '<=', $endDateStr);
                        })
                        ->orWhere(function($dateOverlap) use ($endDateStr) {
                            // Case 2: Existing booking ends during our requested period
                            $dateOverlap->whereDate('end_time', '>=', $this->booking_date)
                                       ->whereDate('end_time', '<=', $endDateStr);
                        })
                        ->orWhere(function($dateOverlap) use ($endDateStr) {
                            // Case 3: Existing booking completely encompasses our requested period
                            $dateOverlap->whereDate('start_time', '<=', $this->booking_date)
                                       ->whereDate('end_time', '>=', $endDateStr);
                        })
                        ->orWhere(function($dateOverlap) use ($endDateStr) {
                            // Case 4: Our requested period encompasses existing booking
                            $dateOverlap->whereDate('start_time', '>=', $this->booking_date)
                                       ->whereDate('end_time', '<=', $endDateStr);
                        });
                    })
                    ->exists();

                return !$conflictingBooking;
            } else {
                // For single-day bookings (meeting rooms, or single-day vehicle/IT asset bookings), use time-based overlap checking
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $startTime);
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $this->booking_date . ' ' . $endTime);

                $conflictingBooking = Booking::where('asset_type', $assetModel)
                    ->where('asset_id', $this->asset_id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->where(function ($query) use ($startDateTime, $endDateTime) {
                        $query->where(function ($overlap) use ($startDateTime, $endDateTime) {
                            // Existing booking starts during our requested time
                            $overlap->where('start_time', '>=', $startDateTime)
                                    ->where('start_time', '<', $endDateTime);
                        })
                        ->orWhere(function ($overlap) use ($startDateTime, $endDateTime) {
                            // Existing booking ends during our requested time
                            $overlap->where('end_time', '>', $startDateTime)
                                    ->where('end_time', '<=', $endDateTime);
                        })
                        ->orWhere(function ($overlap) use ($startDateTime, $endDateTime) {
                            // Existing booking completely encompasses our requested time
                            $overlap->where('start_time', '<=', $startDateTime)
                                    ->where('end_time', '>=', $endDateTime);
                        });
                    })
                    ->exists();

                return !$conflictingBooking;
            }

        } catch (\Exception $e) {
            \Log::error('Error checking time range availability:', [
                'error' => $e->getMessage(),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'asset_type' => $this->asset_type,
                'asset_id' => $this->asset_id,
                'booking_date' => $this->booking_date,
                'end_date' => $this->end_date
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
        // Property updated - handle specific cases

        // Handle array properties differently
        if ($propertyName === 'additional_booking') {
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
        $additionalServices = is_array($this->additional_booking) ? $this->additional_booking : [];
        if (in_array('refreshment', $additionalServices)) {
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

    public function save()
    {
        // Set saving state for loading indicator
        $this->saving = true;
        
        // Validate all fields
        $this->validate();

        // Validate position-based access for vehicles and maintenance status
        if ($this->asset_type === 'vehicle') {
            $vehicle = Vehicle::find($this->asset_id);
            if ($vehicle && !$vehicle->canUserBook(auth()->user())) {
                $this->saving = false;
                $this->addError('asset_id', 'You are not authorized to book this vehicle based on your position.');
                return;
            }

            // Check comprehensive availability including maintenance
            if ($vehicle) {
                $startDateTime = Carbon::parse($this->booking_date . ' ' . $this->start_time);
                $endDateTime = Carbon::parse(($this->end_date ?: $this->booking_date) . ' ' . $this->end_time);

                if (!$vehicle->isAvailableForBooking($startDateTime, $endDateTime)) {
                    $this->saving = false;

                    // Get specific reason for unavailability
                    if ($vehicle->hasOngoingMaintenance()) {
                        $this->addError('asset_id', 'This vehicle is currently under maintenance and cannot be booked.');
                    } elseif ($vehicle->hasScheduledMaintenanceInPeriod($startDateTime, $endDateTime)) {
                        $this->addError('booking_date', 'This vehicle has scheduled maintenance during your requested booking period.');
                    } else {
                        $this->addError('booking_date', 'This vehicle is not available for the selected time period.');
                    }
                    return;
                }
            }
        }

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

            // ENHANCED: Check for conflicting bookings for ALL asset types with proper multi-day support
            $assetModel = $this->assetTypeConfig[$this->asset_type]['model'];
            
            if ($this->allowsMultiDayBooking) {
                // For multi-day bookings (vehicles and IT assets) - check date range overlaps
                $conflictingBooking = Booking::where('asset_type', $assetModel)
                    ->where('asset_id', $this->asset_id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->where(function($query) use ($endDateStr) {
                        // Check for date range overlaps using all possible overlap scenarios
                        $query->where(function($dateOverlap) use ($endDateStr) {
                            // Case 1: Existing booking starts during our requested period
                            $dateOverlap->whereDate('start_time', '>=', $this->booking_date)
                                       ->whereDate('start_time', '<=', $endDateStr);
                        })
                        ->orWhere(function($dateOverlap) use ($endDateStr) {
                            // Case 2: Existing booking ends during our requested period
                            $dateOverlap->whereDate('end_time', '>=', $this->booking_date)
                                       ->whereDate('end_time', '<=', $endDateStr);
                        })
                        ->orWhere(function($dateOverlap) use ($endDateStr) {
                            // Case 3: Existing booking completely encompasses our requested period
                            $dateOverlap->whereDate('start_time', '<=', $this->booking_date)
                                       ->whereDate('end_time', '>=', $endDateStr);
                        })
                        ->orWhere(function($dateOverlap) use ($endDateStr) {
                            // Case 4: Our requested period encompasses existing booking
                            $dateOverlap->whereDate('start_time', '>=', $this->booking_date)
                                       ->whereDate('end_time', '<=', $endDateStr);
                        });
                    })
                    ->exists();
            } else {
                // For single-day bookings (meeting rooms) - check time-based overlaps
                $conflictingBooking = Booking::where('asset_type', $assetModel)
                    ->where('asset_id', $this->asset_id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->where(function ($query) use ($startDateTime, $endDateTime) {
                        $query->where(function ($overlap) use ($startDateTime, $endDateTime) {
                            // Existing booking starts during our requested time
                            $overlap->where('start_time', '>=', $startDateTime)
                                    ->where('start_time', '<', $endDateTime);
                        })
                        ->orWhere(function ($overlap) use ($startDateTime, $endDateTime) {
                            // Existing booking ends during our requested time  
                            $overlap->where('end_time', '>', $startDateTime)
                                    ->where('end_time', '<=', $endDateTime);
                        })
                        ->orWhere(function ($overlap) use ($startDateTime, $endDateTime) {
                            // Existing booking completely encompasses our requested time
                            $overlap->where('start_time', '<=', $startDateTime)
                                    ->where('end_time', '>=', $endDateTime);
                        });
                    })
                    ->exists();
            }

            if ($conflictingBooking) {
                $errorMessage = 'This ' . strtolower($this->assetFieldLabel) . ' is already booked for the selected ';
                if ($this->allowsMultiDayBooking && $this->bookingDays > 1) {
                    $errorMessage .= 'date range.';
                } else {
                    $errorMessage .= 'time slot.';
                }
                $this->addError('booking_date', $errorMessage);
                return;
            }

            // Prepare booking data
            $bookingData = [
                'asset_type' => $assetModel,
                'asset_id' => $this->asset_id,
                'purpose' => $this->purpose,
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'status' => 'pending',
                'capacity' => $this->shouldShowCapacity && $this->capacity ? $this->capacity : null,
                'additional_booking' => is_array($this->additional_booking) ? $this->additional_booking : [],
                'refreshment_details' => (is_array($this->additional_booking) && in_array('refreshment', $this->additional_booking)) ? $this->refreshment_details : null,
            ];

            // Add vehicle-specific data
            if ($this->asset_type === 'vehicle') {
                $bookingData['destination'] = $this->destination;
                
                // Add passengers data if there are passengers
                if (!empty($this->passengers)) {
                    $bookingData['passengers'] = $this->passengers;
                }
            }

            // Create the booking
            $newBooking = auth()->user()->bookings()->create($bookingData);

            // Send email notification to admin
            try {
                Mail::to('e-booking@clab.com.my')
                    ->send(new BookingNotification($newBooking, auth()->user()));
                
                \Log::info('Booking notification email sent to admin', [
                    'booking_id' => $newBooking->id,
                    'user_id' => auth()->id(),
                    'admin_email' => 'e-booking@clab.com.my'
                ]);
            } catch (\Exception $mailException) {
                // Log the error but don't fail the booking creation
                \Log::error('Failed to send booking notification email', [
                    'booking_id' => $newBooking->id,
                    'error' => $mailException->getMessage()
                ]);
                
                // Optionally, you can add a warning message to the user
                // session()->flash('warning', 'Booking created successfully, but notification email could not be sent to admin.');
            }

            // Create success message based on booking type
            $successMessage = 'Booking submitted successfully for ';
            if ($this->bookingDays > 1) {
                $successMessage .= $this->bookingDays . ' days from ' . $startDateTime->format('F j, Y') . ' to ' . $endDateTime->format('F j, Y');
            } else {
                $successMessage .= $startDateTime->format('F j, Y') . ' from ' . $startDateTime->format('g:i A') . ' to ' . $endDateTime->format('g:i A');
            }
            $successMessage .= '. Admin has been notified via email.';

            // Reset saving state
            $this->saving = false;
            
            // Show enhanced success message with emoji
            session()->flash('success', '🎉 ' . $successMessage);
            
            return redirect()->route('bookings.index');

        } catch (\Exception $e) {
            // Reset saving state on exception
            $this->saving = false;
            
            \Log::error('Booking creation failed: ' . $e->getMessage(), [
                'asset_type' => $this->asset_type,
                'asset_id' => $this->asset_id,
                'booking_date' => $this->booking_date,
                'end_date' => $this->end_date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time
            ]);
            session()->flash('error', '⚠️ Failed to create booking. Please try again.');
        }
    }

    /**
     * Prepare for booking confirmation
     */
    public function confirmBooking()
    {
        // Basic validation first - modal will show via trigger
        $this->validate();
    }
    
    /**
     * Reset the entire form to initial state
     */
    public function resetForm()
    {
        // Reset all form properties
        $this->asset_type = '';
        $this->asset_id = '';
        $this->booking_date = '';
        $this->end_date = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->purpose = '';
        $this->capacity = '';
        $this->passengers = [];
        $this->destination = '';
        $this->additional_booking = [];
        $this->refreshment_details = '';
        $this->vehicleUnderMaintenance = false;
        
        // Reset available passengers
        $this->availablePassengers = collect();
        
        // Clear all validation errors
        $this->resetErrorBag();
        
        // Show success message
        session()->flash('success', '✨ Form has been reset successfully!');
    }
    
    /**
     * Check if form is valid for submission
     */
    public function isFormValid()
    {
        return !empty($this->asset_type) &&
               !empty($this->asset_id) &&
               !empty($this->booking_date) &&
               !empty($this->start_time) &&
               !empty($this->end_time) &&
               !empty($this->purpose);
    }

    /**
     * Get the last parking level used for the selected vehicle
     */
    public function getLastParkingInfoProperty()
    {
        if ($this->asset_type !== 'vehicle' || empty($this->asset_id)) {
            return null;
        }

        $lastBooking = Booking::where('asset_type', Vehicle::class)
            ->where('asset_id', $this->asset_id)
            ->where('status', 'done')
            ->whereNotNull('parking_level')
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$lastBooking) {
            return null;
        }

        return [
            'level' => $lastBooking->parking_level,
            'is_reserved' => $lastBooking->is_reserved_slot,
            'date' => $lastBooking->updated_at,
        ];
    }

    public function render()
    {
        return view('livewire.bookings.booking-create');
    }
}