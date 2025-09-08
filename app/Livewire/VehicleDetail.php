<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Vehicle;
use App\Models\VehicleFuelLog;
use App\Models\VehicleOdometerLog;
use App\Models\VehicleMaintenanceLog;
use App\Models\Booking;

class VehicleDetail extends Component
{
    use WithPagination;

    public $vehicleId;
    public $vehicle;
    public $activeTab = 'overview';
    public $dateFrom;
    public $dateTo;
    
    // Quick actions
    public $showQuickFuel = false;
    public $showQuickOdometer = false;
    public $showQuickMaintenance = false;

    public function mount($vehicleId)
    {
        $this->vehicleId = $vehicleId;
        $this->vehicle = Vehicle::with(['fuelLogs', 'odometerLogs', 'maintenanceLogs', 'bookings'])
                                ->findOrFail($vehicleId);
        
        $this->dateFrom = now()->subMonths(3)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function toggleQuickAction($action)
    {
        $this->showQuickFuel = false;
        $this->showQuickOdometer = false;
        $this->showQuickMaintenance = false;
        
        match($action) {
            'fuel' => $this->showQuickFuel = true,
            'odometer' => $this->showQuickOdometer = true,
            'maintenance' => $this->showQuickMaintenance = true,
            default => null
        };
    }

    public function getVehicleStatsProperty()
    {
        return $this->vehicle->getVehicleStats($this->dateFrom, $this->dateTo);
    }

    public function getRecentFuelLogsProperty()
    {
        return VehicleFuelLog::where('vehicle_id', $this->vehicleId)
            ->inDateRange($this->dateFrom, $this->dateTo)
            ->with(['filledBy', 'booking'])
            ->orderBy('filled_at', 'desc')
            ->paginate(10, ['*'], 'fuel-page');
    }

    public function getRecentOdometerLogsProperty()
    {
        return VehicleOdometerLog::where('vehicle_id', $this->vehicleId)
            ->inDateRange($this->dateFrom, $this->dateTo)
            ->with(['recordedBy', 'booking'])
            ->orderBy('recorded_at', 'desc')
            ->paginate(10, ['*'], 'odometer-page');
    }

    public function getRecentMaintenanceLogsProperty()
    {
        return VehicleMaintenanceLog::where('vehicle_id', $this->vehicleId)
            ->inDateRange($this->dateFrom, $this->dateTo)
            ->with(['recordedBy'])
            ->orderBy('performed_at', 'desc')
            ->paginate(10, ['*'], 'maintenance-page');
    }

    public function getRecentBookingsProperty()
    {
        return Booking::where('asset_type', Vehicle::class)
            ->where('asset_id', $this->vehicleId)
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo])
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'booking-page');
    }

    public function getUpcomingMaintenanceProperty()
    {
        return VehicleMaintenanceLog::where('vehicle_id', $this->vehicleId)
            ->whereNotNull('next_maintenance_due')
            ->where('next_maintenance_due', '>', now())
            ->orderBy('next_maintenance_due', 'asc')
            ->limit(5)
            ->get();
    }

    public function getOverdueMaintenanceProperty()
    {
        return VehicleMaintenanceLog::where('vehicle_id', $this->vehicleId)
            ->whereNotNull('next_maintenance_due')
            ->where('next_maintenance_due', '<', now())
            ->orderBy('next_maintenance_due', 'asc')
            ->limit(5)
            ->get();
    }

    public function getLatestReadingsProperty()
    {
        return [
            'fuel' => VehicleFuelLog::where('vehicle_id', $this->vehicleId)
                ->orderBy('filled_at', 'desc')
                ->first(),
            'odometer' => VehicleOdometerLog::where('vehicle_id', $this->vehicleId)
                ->orderBy('recorded_at', 'desc')
                ->first(),
            'maintenance' => VehicleMaintenanceLog::where('vehicle_id', $this->vehicleId)
                ->orderBy('performed_at', 'desc')
                ->first()
        ];
    }

    public function exportVehicleData($format = 'excel')
    {
        $this->dispatch('vehicle-export', [
            'vehicle_id' => $this->vehicleId,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'format' => $format
        ]);
    }

    public function render()
    {
        return view('livewire.vehicle-detail', [
            'vehicleStats' => $this->vehicleStats,
            'recentFuelLogs' => $this->recentFuelLogs,
            'recentOdometerLogs' => $this->recentOdometerLogs,
            'recentMaintenanceLogs' => $this->recentMaintenanceLogs,
            'recentBookings' => $this->recentBookings,
            'upcomingMaintenance' => $this->upcomingMaintenance,
            'overdueMaintenance' => $this->overdueMaintenance,
            'latestReadings' => $this->latestReadings
        ]);
    }
}