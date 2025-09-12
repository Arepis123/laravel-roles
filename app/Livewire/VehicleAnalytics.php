<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Vehicle;
use App\Models\VehicleFuelLog;
use App\Models\VehicleOdometerLog;
use App\Models\VehicleMaintenanceLog;
use Carbon\Carbon;

class VehicleAnalytics extends Component
{
    use WithPagination;

    public $selectedVehicle = null;
    public $dateFrom;
    public $dateTo;
    public $analyticsType = 'overview'; // overview, fuel, odometer, maintenance
    
    // UI States
    public $showFuelDetails = false;
    
    // Filters
    public $fuelType = '';
    public $maintenanceType = '';
    public $readingType = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfYear()->format('Y-m-d'); // January 1st of current year
        $this->dateTo = now()->endOfYear()->format('Y-m-d'); // December 31st of current year
    }

    public function updatedSelectedVehicle()
    {
        $this->resetPage();
    }

    public function updatedAnalyticsType()
    {
        $this->resetPage();
    }

    public function setAnalyticsType($type)
    {
        $this->analyticsType = $type;
        $this->resetPage();
    }

    public function toggleFuelDetails()
    {
        $this->showFuelDetails = !$this->showFuelDetails;
    }

    public function getVehiclesProperty()
    {
        return Vehicle::withLatestLogs()->get();
    }

    public function getSelectedVehicleDataProperty()
    {
        if (!$this->selectedVehicle) {
            return null;
        }

        $vehicle = Vehicle::with(['fuelLogs', 'odometerLogs', 'maintenanceLogs', 'bookings'])
            ->findOrFail($this->selectedVehicle);
        
        return $vehicle->getVehicleStats($this->dateFrom, $this->dateTo);
    }

    public function getFleetOverviewProperty()
    {
        $startDate = $this->dateFrom;
        $endDate = $this->dateTo;

        return [
            'total_vehicles' => Vehicle::count(),
            'active_vehicles' => Vehicle::whereHas('bookings', function($q) use ($startDate, $endDate) {
                $q->where(function($subQ) use ($startDate, $endDate) {
                    $subQ->whereDate('start_time', '>=', $startDate)
                         ->whereDate('start_time', '<=', $endDate)
                         ->orWhereDate('end_time', '>=', $startDate)
                         ->whereDate('end_time', '<=', $endDate)
                         ->orWhere(function($dateQ) use ($startDate, $endDate) {
                             $dateQ->whereDate('start_time', '<=', $startDate)
                                   ->whereDate('end_time', '>=', $endDate);
                         });
                });
            })->count(),
            'total_fuel_consumed' => VehicleFuelLog::inDateRange($startDate, $endDate)
                ->whereNotNull('fuel_amount')
                ->sum('fuel_amount'),
            'total_fuel_cost' => VehicleFuelLog::inDateRange($startDate, $endDate)
                ->whereNotNull('fuel_cost')
                ->sum('fuel_cost'),
            'fuel_by_type' => $this->getFuelStatsByType($startDate, $endDate),
            'total_distance_traveled' => VehicleOdometerLog::inDateRange($startDate, $endDate)->sum('distance_traveled'),
            'total_maintenance_cost' => VehicleMaintenanceLog::inDateRange($startDate, $endDate)->sum('cost'),
            'vehicles_needing_maintenance' => Vehicle::whereHas('maintenanceLogs', function($q) {
                $q->whereNotNull('next_maintenance_due')
                  ->where('next_maintenance_due', '<=', now()->addDays(30));
            })->count(),
            'overdue_maintenance' => Vehicle::whereHas('maintenanceLogs', function($q) {
                $q->whereNotNull('next_maintenance_due')
                  ->where('next_maintenance_due', '<', now());
            })->count()
        ];
    }

    public function getFuelStatsByType($startDate, $endDate)
    {
        return VehicleFuelLog::inDateRange($startDate, $endDate)
            ->whereNotNull('fuel_amount')
            ->whereNotNull('fuel_cost')
            ->whereNotNull('fuel_type')
            ->where('fuel_amount', '>', 0)
            ->selectRaw('
                fuel_type,
                SUM(fuel_amount) as total_amount,
                SUM(fuel_cost) as total_cost,
                COUNT(*) as fill_count,
                CASE 
                    WHEN SUM(fuel_amount) > 0 THEN SUM(fuel_cost) / SUM(fuel_amount)
                    ELSE 0
                END as avg_cost_per_liter
            ')
            ->groupBy('fuel_type')
            ->get()
            ->keyBy('fuel_type');
    }

    public function getTopPerformingVehiclesProperty()
    {
        return Vehicle::withLatestLogs()
            ->get()
            ->map(function ($vehicle) {
                $stats = $vehicle->getVehicleStats($this->dateFrom, $this->dateTo);
                return [
                    'vehicle' => $vehicle,
                    'efficiency' => $stats['fuel_data']['average_efficiency'] ?? 0,
                    'utilization' => $stats['booking_stats']['total_bookings'] ?? 0,
                    'distance' => $stats['odometer_data']['total_distance'] ?? 0,
                    'cost_per_km' => $stats['maintenance_data']['cost_per_km'] ?? 0
                ];
            })
            ->sortByDesc('efficiency')
            ->take(5);
    }

    public function getFuelAnalyticsProperty()
    {
        if (!$this->selectedVehicle) {
            return null;
        }

        $query = VehicleFuelLog::forVehicle($this->selectedVehicle)
            ->inDateRange($this->dateFrom, $this->dateTo);

        if ($this->fuelType) {
            $query->byFuelType($this->fuelType);
        }

        $logs = $query->with(['filledBy', 'booking'])->orderBy('filled_at', 'desc')->get();

        $avgFuelAmount = $logs->avg('fuel_amount');
        $avgFuelCost = $logs->avg('fuel_cost');

        return [
            'logs' => $logs,
            'total_fuel' => $logs->sum('fuel_amount'),
            'total_cost' => $logs->sum('fuel_cost'),
            'average_cost_per_liter' => ($avgFuelAmount && $avgFuelAmount > 0) ? round($avgFuelCost / $avgFuelAmount, 2) : null,
            'fuel_sessions' => $logs->count(),
            'average_efficiency' => VehicleFuelLog::getAverageFuelEfficiency($this->selectedVehicle, $this->dateFrom, $this->dateTo)
        ];
    }

    public function getOdometerAnalyticsProperty()
    {
        if (!$this->selectedVehicle) {
            return null;
        }

        $query = VehicleOdometerLog::forVehicle($this->selectedVehicle)
            ->inDateRange($this->dateFrom, $this->dateTo);

        if ($this->readingType) {
            $query->byReadingType($this->readingType);
        }

        $logs = $query->with(['recordedBy', 'booking'])->orderBy('recorded_at', 'desc')->get();

        return [
            'logs' => $logs,
            'total_distance' => $logs->sum('distance_traveled'),
            'average_distance' => VehicleOdometerLog::getAverageDistancePerTrip($this->selectedVehicle, $this->dateFrom, $this->dateTo),
            'readings_count' => $logs->count(),
            'odometer_range' => VehicleOdometerLog::getOdometerRange($this->selectedVehicle, $this->dateFrom, $this->dateTo)
        ];
    }

    public function getMaintenanceAnalyticsProperty()
    {
        if (!$this->selectedVehicle) {
            return null;
        }

        $query = VehicleMaintenanceLog::forVehicle($this->selectedVehicle)
            ->inDateRange($this->dateFrom, $this->dateTo);

        if ($this->maintenanceType) {
            $query->byMaintenanceType($this->maintenanceType);
        }

        $logs = $query->with(['recordedBy'])->orderBy('performed_at', 'desc')->get();

        return [
            'logs' => $logs,
            'total_cost' => $logs->sum('cost'),
            'maintenance_count' => VehicleMaintenanceLog::getMaintenanceCountByType($this->selectedVehicle, $this->dateFrom, $this->dateTo),
            'upcoming_maintenance' => VehicleMaintenanceLog::getUpcomingMaintenanceForVehicle($this->selectedVehicle),
            'overdue_maintenance' => VehicleMaintenanceLog::getOverdueMaintenanceForVehicle($this->selectedVehicle)
        ];
    }

    public function exportAnalytics($format = 'excel')
    {
        $this->dispatch('analytics-export', [
            'vehicle_id' => $this->selectedVehicle,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'analytics_type' => $this->analyticsType,
            'format' => $format
        ]);
    }

    public function render()
    {
        return view('livewire.vehicle-analytics', [
            'vehicles' => $this->vehicles,
            'selectedVehicleData' => $this->selectedVehicleData,
            'fleetOverview' => $this->fleetOverview,
            'topPerformingVehicles' => $this->topPerformingVehicles,
            'fuelAnalytics' => $this->fuelAnalytics,
            'odometerAnalytics' => $this->odometerAnalytics,
            'maintenanceAnalytics' => $this->maintenanceAnalytics
        ]);
    }
}