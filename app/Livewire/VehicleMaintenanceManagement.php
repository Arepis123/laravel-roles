<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Vehicle;
use App\Models\VehicleMaintenanceLog;
use App\Models\Booking;
use Carbon\Carbon;

class VehicleMaintenanceManagement extends Component
{
    use WithPagination;

    public $editingLog = null;
    public $isScheduleMode = false; // Toggle between log completed vs schedule future
    
    // Form fields
    public $vehicle_id = '';
    public $booking_id = '';
    public $maintenance_type = 'service';
    public $title = '';
    public $description = '';
    public $cost = '';
    public $service_provider = '';
    public $performed_by = '';
    public $odometer_at_service = '';
    public $performed_at = '';
    public $next_maintenance_due = '';
    public $notes = '';
    public $status = 'ongoing';
    
    // Filters
    public $filterVehicle = '';
    public $filterMaintenanceType = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterUpcoming = false;
    public $filterOverdue = false;
    
    // Sorting
    public $sortField = 'performed_at';
    public $sortDirection = 'desc';

    protected function rules()
    {
        $baseRules = [
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type' => 'required|in:service,repair,inspection,oil_change,tire_change,brake_service,other',
            'title' => 'required|string|max:255',
            'cost' => 'nullable|numeric|min:0|max:999999.99',
            'service_provider' => 'nullable|string|max:255',
            'odometer_at_service' => 'nullable|integer|min:0|max:9999999',
            'notes' => 'nullable|string|max:1000',
            'booking_id' => 'nullable|exists:bookings,id',
            'status' => 'required|in:ongoing,completed,scheduled'
        ];

        if ($this->isScheduleMode) {
            // Schedule mode: future maintenance
            $baseRules['description'] = 'nullable|string|max:500'; // Optional for scheduling
            $baseRules['performed_by'] = 'nullable|string|max:255'; // Optional for scheduling
            $baseRules['performed_at'] = 'required|date|after:now'; // Must be future date
            $baseRules['next_maintenance_due'] = 'nullable|date|after:performed_at';
        } else {
            // Log mode: completed/ongoing maintenance
            $baseRules['description'] = 'required|string|max:500';
            $baseRules['performed_by'] = 'required|string|max:255';
            $baseRules['performed_at'] = 'required|date|before_or_equal:now';
            $baseRules['next_maintenance_due'] = 'nullable|date|after:performed_at';
        }

        return $baseRules;
    }

    public function mount()
    {
        $this->performed_at = now()->format('Y-m-d\TH:i');
        $this->filterDateFrom = now()->startOfYear()->format('Y-m-d'); // January 1st of current year
        $this->filterDateTo = now()->endOfYear()->format('Y-m-d'); // December 31st of current year
    }

    public function updatedFilterVehicle()
    {
        $this->resetPage();
    }

    public function updatedFilterMaintenanceType()
    {
        $this->resetPage();
    }

    public function updatedFilterUpcoming()
    {
        $this->resetPage();
    }

    public function updatedFilterOverdue()
    {
        $this->resetPage();
    }

    public function updatedVehicleId()
    {
        // Auto-populate latest odometer reading if available
        if ($this->vehicle_id) {
            $latestOdometer = \App\Models\VehicleOdometerLog::getLatestReadingForVehicle($this->vehicle_id);
            if ($latestOdometer) {
                $this->odometer_at_service = $latestOdometer->odometer_reading;
            }
        }
    }

    public function updatedIsScheduleMode()
    {
        // When switching modes, adjust form fields accordingly
        if ($this->isScheduleMode) {
            // Schedule mode: set future date and scheduled status
            $this->status = 'scheduled';
            $this->performed_at = now()->addDays(7)->format('Y-m-d\TH:i'); // Default to next week
            $this->performed_by = ''; // Not required for scheduling
        } else {
            // Log mode: set current/past date and ongoing status
            $this->status = 'ongoing';
            $this->performed_at = now()->format('Y-m-d\TH:i');
        }
    }

    public function updatedMaintenanceType()
    {
        // Auto-suggest next maintenance based on type
        if ($this->maintenance_type && $this->performed_at) {
            $performedDate = Carbon::parse($this->performed_at);
            
            $nextDue = match($this->maintenance_type) {
                'oil_change' => $performedDate->addMonths(6),
                'service' => $performedDate->addMonths(12),
                'inspection' => $performedDate->addMonths(12),
                'tire_change' => $performedDate->addMonths(24),
                'brake_service' => $performedDate->addMonths(18),
                default => null
            };
            
            if ($nextDue) {
                $this->next_maintenance_due = $nextDue->format('Y-m-d');
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
        $this->isScheduleMode = false; // Default to log mode
        // Modal is triggered by flux:modal.trigger, no need to dispatch
    }

    public function editLog($logId)
    {
        $log = VehicleMaintenanceLog::with(['vehicle', 'booking'])->findOrFail($logId);
        
        $this->editingLog = $log->id;
        $this->vehicle_id = $log->vehicle_id;
        $this->booking_id = $log->booking_id;
        $this->maintenance_type = $log->maintenance_type;
        $this->title = $log->title;
        $this->description = $log->description;
        $this->cost = $log->cost;
        $this->service_provider = $log->service_provider;
        $this->performed_by = $log->performed_by;
        $this->odometer_at_service = $log->odometer_at_maintenance;
        $this->performed_at = $log->performed_at->format('Y-m-d\TH:i');
        $this->next_maintenance_due = $log->next_maintenance_due?->format('Y-m-d');
        $this->notes = $log->notes;
        $this->status = $log->status ?? 'ongoing';
        
        $this->dispatch('open-modal');
    }

    public function saveLog()
    {
        $this->validate();

        $data = [
            'vehicle_id' => $this->vehicle_id,
            'booking_id' => $this->booking_id ?: null,
            'maintenance_type' => $this->maintenance_type,
            'title' => $this->title,
            'description' => $this->description,
            'cost' => $this->cost ?: null,
            'service_provider' => $this->service_provider ?: null,
            'performed_by' => $this->performed_by,
            'odometer_at_maintenance' => $this->odometer_at_service ?: null,
            'recorded_by' => auth()->id(),
            'performed_at' => Carbon::parse($this->performed_at),
            'next_maintenance_due' => $this->next_maintenance_due ? Carbon::parse($this->next_maintenance_due) : null,
            'notes' => $this->notes ?: null,
            'status' => $this->status,
        ];

        if ($this->editingLog) {
            $log = VehicleMaintenanceLog::findOrFail($this->editingLog);
            $log->update($data);
            $this->dispatch('maintenance-log-updated', [
                'message' => 'Maintenance record updated successfully!'
            ]);
        } else {
            VehicleMaintenanceLog::create($data);
            $this->dispatch('maintenance-log-created', [
                'message' => 'Maintenance record created successfully!'
            ]);
        }

        $this->resetForm();
        $this->dispatch('close-modal');
    }

    public function deleteLog($logId)
    {
        $log = VehicleMaintenanceLog::findOrFail($logId);
        $vehicleName = $log->vehicle->model ?? 'Unknown';
        
        $log->delete();
        
        $this->dispatch('maintenance-log-deleted', [
            'message' => "Maintenance record for {$vehicleName} deleted successfully!"
        ]);
    }

    public function markCompleted($logId)
    {
        $log = VehicleMaintenanceLog::findOrFail($logId);
        $log->update(['status' => 'completed']);
        
        $this->dispatch('maintenance-completed', [
            'message' => 'Maintenance marked as completed!'
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
        $this->maintenance_type = 'service';
        $this->title = '';
        $this->description = '';
        $this->cost = '';
        $this->service_provider = '';
        $this->odometer_at_service = '';
        $this->performed_at = now()->format('Y-m-d\TH:i');
        $this->next_maintenance_due = '';
        $this->notes = '';
        $this->status = 'ongoing';
        $this->isScheduleMode = false;
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

    public function getMaintenanceLogsProperty()
    {
        $query = VehicleMaintenanceLog::with(['vehicle', 'recordedBy', 'booking.user']);

        // Apply filters
        if ($this->filterVehicle) {
            $query->where('vehicle_id', $this->filterVehicle);
        }

        if ($this->filterMaintenanceType) {
            $query->where('maintenance_type', $this->filterMaintenanceType);
        }

        if ($this->filterDateFrom) {
            $query->where('performed_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->where('performed_at', '<=', $this->filterDateTo . ' 23:59:59');
        }

        if ($this->filterUpcoming) {
            $query->whereNotNull('next_maintenance_due')
                  ->where('next_maintenance_due', '>', now())
                  ->where('next_maintenance_due', '<=', now()->addDays(30));
        }

        if ($this->filterOverdue) {
            $query->whereNotNull('next_maintenance_due')
                  ->where('next_maintenance_due', '<', now());
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(15);
    }

    public function getStatsProperty()
    {
        $query = VehicleMaintenanceLog::query();

        // Apply same filters as main query (excluding upcoming/overdue for stats)
        if ($this->filterVehicle) {
            $query->where('vehicle_id', $this->filterVehicle);
        }

        if ($this->filterMaintenanceType) {
            $query->where('maintenance_type', $this->filterMaintenanceType);
        }

        if ($this->filterDateFrom) {
            $query->where('performed_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->where('performed_at', '<=', $this->filterDateTo . ' 23:59:59');
        }

        $logs = $query->get();

        return [
            'total_cost' => $logs->sum('cost') ?: 0,
            'total_records' => $logs->count(),
            'avg_cost_per_service' => $logs->where('cost', '>', 0)->avg('cost') ?: 0,
            'vehicles_serviced' => $logs->pluck('vehicle_id')->unique()->count(),
            'upcoming_maintenance' => VehicleMaintenanceLog::whereNotNull('next_maintenance_due')
                ->where('next_maintenance_due', '>', now())
                ->where('next_maintenance_due', '<=', now()->addDays(30))
                ->count(),
            'overdue_maintenance' => VehicleMaintenanceLog::whereNotNull('next_maintenance_due')
                ->where('next_maintenance_due', '<', now())
                ->count(),
            'maintenance_by_type' => $logs->groupBy('maintenance_type')->map->count()
        ];
    }

    public function exportMaintenanceData($format = 'excel')
    {
        $this->dispatch('maintenance-export', [
            'vehicle_id' => $this->filterVehicle,
            'date_from' => $this->filterDateFrom,
            'date_to' => $this->filterDateTo,
            'format' => $format
        ]);
    }

    public function render()
    {
        return view('livewire.vehicle-maintenance-management', [
            'maintenanceLogs' => $this->maintenanceLogs,
            'vehicles' => $this->vehicles,
            'bookings' => $this->bookings,
            'stats' => $this->stats
        ]);
    }
}