<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Vehicle;
use App\Models\VehicleFuelLog;
use App\Models\Booking;
use Carbon\Carbon;

class VehicleFuelManagement extends Component
{
    use WithPagination;

    public $editingLog = null;
    
    // Form fields
    public $vehicle_id = '';
    public $booking_id = '';
    public $fuel_amount = '';
    public $fuel_type = 'petrol';
    public $fuel_cost = '';
    public $fuel_station = '';
    public $odometer_at_fill = '';
    public $filled_at = '';
    public $notes = '';
    
    // Filters
    public $filterVehicle = '';
    public $filterFuelType = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    
    // Sorting
    public $sortField = 'filled_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'vehicle_id' => 'required|exists:vehicles,id',
        'fuel_amount' => 'required|numeric|min:0.1|max:1000',
        'fuel_type' => 'required|in:petrol,diesel,hybrid,electric',
        'fuel_cost' => 'nullable|numeric|min:0|max:10000',
        'fuel_station' => 'nullable|string|max:255',
        'odometer_at_fill' => 'nullable|integer|min:0|max:999999',
        'filled_at' => 'required|date|before_or_equal:now',
        'notes' => 'nullable|string|max:1000',
        'booking_id' => 'nullable|exists:bookings,id'
    ];

    public function mount()
    {
        $this->filled_at = now()->format('Y-m-d\TH:i');
        $this->filterDateFrom = now()->subMonth()->format('Y-m-d');
        $this->filterDateTo = now()->format('Y-m-d');
    }

    public function updatedFilterVehicle()
    {
        $this->resetPage();
    }

    public function updatedFilterFuelType()
    {
        $this->resetPage();
    }

    public function updatedVehicleId()
    {
        // Auto-populate latest odometer reading if available
        if ($this->vehicle_id) {
            $latestOdometer = \App\Models\VehicleOdometerLog::getLatestReadingForVehicle($this->vehicle_id);
            if ($latestOdometer) {
                $this->odometer_at_fill = $latestOdometer->odometer_reading;
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
        $log = VehicleFuelLog::with(['vehicle', 'booking'])->findOrFail($logId);
        
        $this->editingLog = $log->id;
        $this->vehicle_id = $log->vehicle_id;
        $this->booking_id = $log->booking_id;
        $this->fuel_amount = $log->fuel_amount;
        $this->fuel_type = $log->fuel_type;
        $this->fuel_cost = $log->fuel_cost;
        $this->fuel_station = $log->fuel_station;
        $this->odometer_at_fill = $log->odometer_at_fill;
        $this->filled_at = $log->filled_at->format('Y-m-d\TH:i');
        $this->notes = $log->notes;
        
        $this->dispatch('open-modal');
    }

    public function saveLog()
    {
        $this->validate();

        $data = [
            'vehicle_id' => $this->vehicle_id,
            'booking_id' => $this->booking_id ?: null,
            'fuel_amount' => $this->fuel_amount,
            'fuel_type' => $this->fuel_type,
            'fuel_cost' => $this->fuel_cost ?: null,
            'fuel_station' => $this->fuel_station ?: null,
            'odometer_at_fill' => $this->odometer_at_fill ?: null,
            'filled_by' => auth()->id(),
            'filled_at' => Carbon::parse($this->filled_at),
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingLog) {
            $log = VehicleFuelLog::findOrFail($this->editingLog);
            $log->update($data);
            $this->dispatch('fuel-log-updated', [
                'message' => 'Fuel log updated successfully!'
            ]);
        } else {
            VehicleFuelLog::create($data);
            $this->dispatch('fuel-log-created', [
                'message' => 'Fuel log created successfully!'
            ]);
        }

        $this->resetForm();
        $this->dispatch('close-modal');
    }

    public function deleteLog($logId)
    {
        $log = VehicleFuelLog::findOrFail($logId);
        $vehicleName = $log->vehicle->model ?? 'Unknown';
        
        $log->delete();
        
        $this->dispatch('fuel-log-deleted', [
            'message' => "Fuel log for {$vehicleName} deleted successfully!"
        ]);
    }

    public function cancelForm()
    {
        $this->resetForm();
        $this->dispatch('close-modal');
    }

    private function resetForm()
    {
        $this->editingLog = null;
        $this->vehicle_id = '';
        $this->booking_id = '';
        $this->fuel_amount = '';
        $this->fuel_type = 'petrol';
        $this->fuel_cost = '';
        $this->fuel_station = '';
        $this->odometer_at_fill = '';
        $this->filled_at = now()->format('Y-m-d\TH:i');
        $this->notes = '';
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
            ->where('status', 'done')
            ->with(['user'])
            ->orderBy('end_time', 'desc')
            ->take(20)
            ->get();
    }

    public function getFuelLogsProperty()
    {
        $query = VehicleFuelLog::with(['vehicle', 'filledBy', 'booking.user']);

        // Apply filters
        if ($this->filterVehicle) {
            $query->where('vehicle_id', $this->filterVehicle);
        }

        if ($this->filterFuelType) {
            $query->where('fuel_type', $this->filterFuelType);
        }

        if ($this->filterDateFrom) {
            $query->where('filled_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->where('filled_at', '<=', $this->filterDateTo . ' 23:59:59');
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(15);
    }

    public function getStatsProperty()
    {
        $query = VehicleFuelLog::query();

        // Apply same filters as main query
        if ($this->filterVehicle) {
            $query->where('vehicle_id', $this->filterVehicle);
        }

        if ($this->filterFuelType) {
            $query->where('fuel_type', $this->filterFuelType);
        }

        if ($this->filterDateFrom) {
            $query->where('filled_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->where('filled_at', '<=', $this->filterDateTo . ' 23:59:59');
        }

        return [
            'total_fuel' => $query->sum('fuel_amount') ?: 0,
            'total_cost' => $query->whereNotNull('fuel_cost')->sum('fuel_cost') ?: 0,
            'total_sessions' => $query->count(),
            'avg_cost_per_liter' => $query->whereNotNull('fuel_cost')->where('fuel_amount', '>', 0)->get()->avg(function($log) {
                return $log->fuel_cost / $log->fuel_amount;
            }) ?: 0
        ];
    }

    public function render()
    {
        return view('livewire.vehicle-fuel-management', [
            'fuelLogs' => $this->fuelLogs,
            'vehicles' => $this->vehicles,
            'bookings' => $this->bookings,
            'stats' => $this->stats
        ]);
    }
}