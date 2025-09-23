<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Vehicle;
use App\Models\VehicleOdometerLog;
use App\Models\Booking;
use Carbon\Carbon;

class VehicleOdometerManagement extends Component
{
    use WithPagination;

    public $editingLog = null;
    
    // Form fields
    public $vehicle_id = '';
    public $booking_id = '';
    public $odometer_reading = '';
    public $reading_type = 'manual';
    public $distance_traveled = '';
    public $calculated_distance = '';
    public $is_manual_distance = false;
    public $recorded_at = '';
    public $notes = '';
    public $performed_by = '';
    
    // Filters
    public $filterVehicle = '';
    public $filterReadingType = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    
    // Sorting
    public $sortField = 'recorded_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'vehicle_id' => 'required|exists:vehicles,id',
        'odometer_reading' => 'required|integer|min:0|max:9999999',
        'reading_type' => 'required|in:start,end,manual,service',
        'distance_traveled' => 'nullable|numeric|min:0|max:99999',
        'recorded_at' => 'required|date|before_or_equal:now',
        'notes' => 'nullable|string|max:1000',
        'booking_id' => 'nullable|exists:bookings,id',
        'performed_by' => 'required|string|max:255'
    ];

    public function mount()
    {
        $this->recorded_at = now()->format('Y-m-d\TH:i');
        $this->filterDateFrom = now()->startOfYear()->format('Y-m-d'); // January 1st of current year
        $this->filterDateTo = now()->endOfYear()->format('Y-m-d'); // December 31st of current year
        $this->performed_by = auth()->user()->name ?? '';
    }

    public function updatedFilterVehicle()
    {
        $this->resetPage();
    }

    public function updatedFilterReadingType()
    {
        $this->resetPage();
    }

    public function updatedVehicleId()
    {
        // Reset distance calculation flags when vehicle changes
        $this->calculated_distance = '';
        $this->is_manual_distance = false;
        $this->distance_traveled = '';

        // Auto-populate latest odometer reading if available
        if ($this->vehicle_id) {
            $latestReading = VehicleOdometerLog::getLatestReadingForVehicle($this->vehicle_id);
            if ($latestReading) {
                // Don't auto-fill odometer, let user enter it
                // But show the latest reading for reference
            }
        }
    }

    public function updatedOdometerReading()
    {
        // Auto-calculate distance if we have a previous reading
        if ($this->vehicle_id && $this->odometer_reading) {
            $latestReading = VehicleOdometerLog::getLatestReadingForVehicle($this->vehicle_id);
            if ($latestReading && $this->odometer_reading > $latestReading->odometer_reading) {
                $this->calculated_distance = $this->odometer_reading - $latestReading->odometer_reading;
                // Only auto-fill if user hasn't manually entered distance
                if (!$this->is_manual_distance) {
                    $this->distance_traveled = $this->calculated_distance;
                }
            } else {
                $this->calculated_distance = '';
                if (!$this->is_manual_distance) {
                    $this->distance_traveled = '';
                }
            }
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    public function showAddForm()
    {
        $this->resetForm();
        $this->editingLog = null;
        // Modal is triggered by flux:modal.trigger, no need to dispatch
    }

    public function editLog($logId)
    {
        $log = VehicleOdometerLog::with(['vehicle', 'booking'])->findOrFail($logId);
        
        $this->editingLog = $log->id;
        $this->vehicle_id = $log->vehicle_id;
        $this->booking_id = $log->booking_id;
        $this->odometer_reading = $log->odometer_reading;
        $this->reading_type = $log->reading_type;
        $this->distance_traveled = $log->distance_traveled;
        $this->calculated_distance = '';
        $this->is_manual_distance = true; // Assume manual when editing existing log
        $this->recorded_at = $log->recorded_at->format('Y-m-d\TH:i');
        $this->notes = $log->notes;
        $this->performed_by = $log->performed_by ?? auth()->user()->name ?? '';
        
        $this->dispatch('open-modal');
    }

    public function saveLog()
    {
        $this->validate();

        $data = [
            'vehicle_id' => $this->vehicle_id,
            'booking_id' => $this->booking_id ?: null,
            'odometer_reading' => $this->odometer_reading,
            'reading_type' => $this->reading_type,
            'distance_traveled' => $this->distance_traveled ?: null,
            'recorded_by' => auth()->id(),
            'recorded_at' => Carbon::parse($this->recorded_at),
            'notes' => $this->notes ?: null,
            'performed_by' => $this->performed_by,
        ];

        // Add note about manual vs calculated distance
        if ($this->is_manual_distance && $this->calculated_distance && $this->distance_traveled != $this->calculated_distance) {
            $calculationNote = "[Manual Distance Entry] User entered {$this->distance_traveled} km (system calculated {$this->calculated_distance} km)";
            $data['notes'] = $data['notes'] ? $data['notes'] . "\n\n" . $calculationNote : $calculationNote;
        } elseif ($this->calculated_distance && $this->distance_traveled == $this->calculated_distance) {
            $calculationNote = "[Auto-calculated Distance] Based on previous odometer reading";
            $data['notes'] = $data['notes'] ? $data['notes'] . "\n\n" . $calculationNote : $calculationNote;
        }

        if ($this->editingLog) {
            $log = VehicleOdometerLog::findOrFail($this->editingLog);
            $log->update($data);
            $this->dispatch('odometer-log-updated', [
                'message' => 'Odometer reading updated successfully!'
            ]);
        } else {
            VehicleOdometerLog::create($data);
            $this->dispatch('odometer-log-created', [
                'message' => 'Odometer reading recorded successfully!'
            ]);
        }

        $this->resetForm();
        $this->dispatch('close-modal');
    }

    public function deleteLog($logId)
    {
        $log = VehicleOdometerLog::findOrFail($logId);
        $vehicleName = $log->vehicle->model ?? 'Unknown';
        
        $log->delete();
        
        $this->dispatch('odometer-log-deleted', [
            'message' => "Odometer reading for {$vehicleName} deleted successfully!"
        ]);
    }

    public function cancelForm()
    {
        $this->resetForm();
        $this->dispatch('close-modal');
    }

    public function updatedDistanceTraveled()
    {
        // Mark as manual entry when user modifies distance
        if ($this->distance_traveled !== $this->calculated_distance) {
            $this->is_manual_distance = true;
        } else {
            $this->is_manual_distance = false;
        }
    }

    public function useCalculatedDistance()
    {
        $this->distance_traveled = $this->calculated_distance;
        $this->is_manual_distance = false;
    }

    public function getLatestOdometerForSelectedVehicle()
    {
        if ($this->vehicle_id) {
            $latestReading = VehicleOdometerLog::getLatestReadingForVehicle($this->vehicle_id);
            return $latestReading ? $latestReading->odometer_reading : null;
        }
        return null;
    }

    private function resetForm()
    {
        $this->editingLog = null;
        $this->vehicle_id = '';
        $this->booking_id = '';
        $this->odometer_reading = '';
        $this->reading_type = 'manual';
        $this->distance_traveled = '';
        $this->calculated_distance = '';
        $this->is_manual_distance = false;
        $this->recorded_at = now()->format('Y-m-d\TH:i');
        $this->notes = '';
        $this->performed_by = auth()->user()->name ?? '';
        $this->resetErrorBag();
    }

    public function getVehiclesProperty()
    {
        return Vehicle::orderBy('model')->get();
    }

    public function getBookingsProperty()
    {
        if (!$this->vehicle_id) {
            return collect();
        }

        return Booking::where('asset_type', Vehicle::class)
            ->where('asset_id', $this->vehicle_id)
            ->whereIn('status', ['ongoing', 'done'])
            ->with(['user'])
            ->orderBy('start_time', 'desc')
            ->take(20)
            ->get();
    }

    public function getOdometerLogsProperty()
    {
        $query = VehicleOdometerLog::with(['vehicle', 'recordedBy', 'booking.user']);

        // Apply filters
        if ($this->filterVehicle) {
            $query->where('vehicle_id', $this->filterVehicle);
        }

        if ($this->filterReadingType) {
            $query->where('reading_type', $this->filterReadingType);
        }

        if ($this->filterDateFrom) {
            $query->where('recorded_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->where('recorded_at', '<=', $this->filterDateTo . ' 23:59:59');
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(15);
    }

    public function getStatsProperty()
    {
        $query = VehicleOdometerLog::query();

        // Apply same filters as main query
        if ($this->filterVehicle) {
            $query->where('vehicle_id', $this->filterVehicle);
        }

        if ($this->filterReadingType) {
            $query->where('reading_type', $this->filterReadingType);
        }

        if ($this->filterDateFrom) {
            $query->where('recorded_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->where('recorded_at', '<=', $this->filterDateTo . ' 23:59:59');
        }

        $logs = $query->get();

        return [
            'total_distance' => $logs->sum('distance_traveled') ?: 0,
            'total_readings' => $logs->count(),
            'avg_distance_per_trip' => $logs->where('distance_traveled', '>', 0)->avg('distance_traveled') ?: 0,
            'vehicles_tracked' => $logs->pluck('vehicle_id')->unique()->count(),
            'latest_reading' => $query->orderBy('recorded_at', 'desc')->first(),
            'odometer_range' => [
                'min' => $logs->min('odometer_reading') ?: 0,
                'max' => $logs->max('odometer_reading') ?: 0
            ]
        ];
    }

    public function exportOdometerData($format = 'excel')
    {
        $this->dispatch('odometer-export', [
            'vehicle_id' => $this->filterVehicle,
            'date_from' => $this->filterDateFrom,
            'date_to' => $this->filterDateTo,
            'format' => $format
        ]);
    }

    public function render()
    {
        return view('livewire.vehicle-odometer-management', [
            'odometerLogs' => $this->odometerLogs,
            'vehicles' => $this->vehicles,
            'bookings' => $this->bookings,
            'stats' => $this->stats
        ]);
    }
}