<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\VehicleFuelLog;
use App\Models\VehicleOdometerLog;
use App\Models\VehicleMaintenanceLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Get bookings formatted for FullCalendar
     */
    public function getBookings(Request $request)
    {
        $query = Booking::with(['bookedBy', 'asset']);

        // Enhanced status filtering with multiple statuses
        if ($request->has('status') && $request->status !== 'all') {
            if (is_array($request->status)) {
                $query->whereIn('status', $request->status);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Enhanced asset type filtering with multiple types
        if ($request->has('asset_type') && $request->asset_type !== 'all') {
            if (is_array($request->asset_type)) {
                $query->where(function($q) use ($request) {
                    foreach ($request->asset_type as $type) {
                        $q->orWhere('asset_type', 'like', '%' . $type . '%');
                    }
                });
            } else {
                $query->where('asset_type', 'like', '%' . $request->asset_type . '%');
            }
        }

        // Filter by specific asset IDs
        if ($request->has('asset_ids') && !empty($request->asset_ids)) {
            $assetIds = is_array($request->asset_ids) ? $request->asset_ids : explode(',', $request->asset_ids);
            $query->whereIn('asset_id', $assetIds);
        }

        // Filter by user/department
        if ($request->has('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by capacity range
        if ($request->has('min_capacity')) {
            $query->where('capacity', '>=', $request->min_capacity);
        }
        if ($request->has('max_capacity')) {
            $query->where('capacity', '<=', $request->max_capacity);
        }

        // Filter by purpose (search in purpose field)
        if ($request->has('purpose') && !empty($request->purpose)) {
            $query->where('purpose', 'like', '%' . $request->purpose . '%');
        }

        // Filter by time of day
        if ($request->has('time_filter')) {
            switch ($request->time_filter) {
                case 'morning':
                    $query->whereTime('start_time', '>=', '06:00:00')
                          ->whereTime('start_time', '<', '12:00:00');
                    break;
                case 'afternoon':
                    $query->whereTime('start_time', '>=', '12:00:00')
                          ->whereTime('start_time', '<', '18:00:00');
                    break;
                case 'evening':
                    $query->whereTime('start_time', '>=', '18:00:00')
                          ->whereTime('start_time', '<', '24:00:00');
                    break;
            }
        }

        // Filter by booking duration
        if ($request->has('duration_filter')) {
            switch ($request->duration_filter) {
                case 'short': // Less than 2 hours
                    $query->whereRaw('TIMESTAMPDIFF(HOUR, start_time, end_time) < 2');
                    break;
                case 'medium': // 2-8 hours
                    $query->whereRaw('TIMESTAMPDIFF(HOUR, start_time, end_time) BETWEEN 2 AND 8');
                    break;
                case 'long': // More than 8 hours
                    $query->whereRaw('TIMESTAMPDIFF(HOUR, start_time, end_time) > 8');
                    break;
            }
        }

        // Enhanced date range filtering (FullCalendar sends start/end dates)
        if ($request->has('start') && $request->has('end')) {
            $start = Carbon::parse($request->start);
            $end = Carbon::parse($request->end);
            
            $query->where(function($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhereBetween('end_time', [$start, $end])
                  ->orWhere(function($subQ) use ($start, $end) {
                      $subQ->where('start_time', '<=', $start)
                           ->where('end_time', '>=', $end);
                  });
            });
        }

        // Filter by weekdays only or weekends only
        if ($request->has('day_type')) {
            switch ($request->day_type) {
                case 'weekdays':
                    $query->whereRaw('DAYOFWEEK(start_time) BETWEEN 2 AND 6'); // Mon-Fri
                    break;
                case 'weekends':
                    $query->whereIn(\DB::raw('DAYOFWEEK(start_time)'), [1, 7]); // Sun, Sat
                    break;
            }
        }

        $bookings = $query->orderBy('start_time')->get();

        // Transform bookings to FullCalendar event format
        $events = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => $this->generateEventTitle($booking),
                'start' => $booking->start_time->toISOString(),
                'end' => $booking->end_time->toISOString(),
                'backgroundColor' => $this->getStatusColor($booking->status),
                'borderColor' => $this->getStatusBorderColor($booking->status),
                'textColor' => $this->getTextColor($booking->status),
                'extendedProps' => [
                    'bookingId' => $booking->id,
                    'status' => $booking->status,
                    'assetType' => class_basename($booking->asset_type),
                    'assetTypeLabel' => $booking->asset_type_label,
                    'bookedBy' => $booking->bookedBy?->name ?? 'Unknown User',
                    'bookedByEmail' => $booking->bookedBy?->email ?? '',
                    'purpose' => $booking->purpose,
                    'capacity' => $booking->capacity,
                    'additionalBooking' => $booking->additional_booking,
                    'refreshmentDetails' => $booking->refreshment_details,
                    'statusHistory' => $booking->status_history,
                    'timeRange' => $this->formatTimeRange($booking->start_time, $booking->end_time),
                    'createdAt' => $booking->created_at->format('M j, Y g:i A'),
                    'isUpcoming' => $booking->isUpcoming(),
                    'isActive' => $booking->isActive(),
                    'isPast' => $booking->isPast(),
                    'assetName' => $booking->asset->name,
                    'assetModel' => $booking->asset->model, // for vehicles
                    'assetPlateNumber' => $booking->asset->plate_number, // for vehicles
                    'assetTag' => $booking->asset->asset_tag, // for IT assets                    
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::pending()->count(),
            'approved' => Booking::approved()->count(),
            'rejected' => Booking::rejected()->count(),
            'cancelled' => Booking::cancelled()->count(),
            'done' => Booking::done()->count(),
            'today_total' => Booking::whereDate('start_time', today())->count(),
            'today_approved' => Booking::whereDate('start_time', today())->approved()->count(),
            'upcoming' => Booking::approved()->where('start_time', '>', now())->count(),
            'active' => Booking::approved()
                              ->where('start_time', '<=', now())
                              ->where('end_time', '>=', now())
                              ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get vehicle management events for calendar (Admin/Super Admin only)
     */
    public function getVehicleEvents(Request $request)
    {
        // Check if user has admin or super admin role
        if (!auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            return response()->json(['error' => 'Unauthorized. Admin access required.'], 403);
        }

        $events = collect();
        
        // Get date range from request
        $start = $request->has('start') ? Carbon::parse($request->start) : Carbon::now()->startOfMonth();
        $end = $request->has('end') ? Carbon::parse($request->end) : Carbon::now()->endOfMonth()->addMonth();
        
        // Filter by vehicle if provided
        $vehicleId = $request->get('vehicle_id');
        
        // 1. Maintenance Events
        if (!$request->has('event_types') || in_array('maintenance', $request->get('event_types', []))) {
            $maintenanceEvents = $this->getMaintenanceEvents($start, $end, $vehicleId);
            $events = $events->merge($maintenanceEvents);
        }
        
        // 2. Fuel Events (if show_completed filter is enabled)
        if (!$request->has('event_types') || in_array('fuel', $request->get('event_types', []))) {
            $fuelEvents = $this->getFuelEvents($start, $end, $vehicleId, $request->get('show_completed', false));
            $events = $events->merge($fuelEvents);
        }
        
        // 3. Odometer Events (major milestones)
        if (!$request->has('event_types') || in_array('odometer', $request->get('event_types', []))) {
            $odometerEvents = $this->getOdometerEvents($start, $end, $vehicleId);
            $events = $events->merge($odometerEvents);
        }

        return response()->json($events->values());
    }

    /**
     * Get enhanced booking events with vehicle management integration
     */
    public function getEnhancedBookings(Request $request)
    {
        // Get regular bookings
        $bookingEvents = collect($this->getBookings($request)->getData());
        
        // If user is admin/super admin, add vehicle events
        if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            $vehicleEvents = collect($this->getVehicleEvents($request)->getData());
            $bookingEvents = $bookingEvents->merge($vehicleEvents);
        }
        
        return response()->json($bookingEvents->values());
    }

    /**
     * Get vehicle management statistics (Admin/Super Admin only)
     */
    public function getVehicleStats(Request $request)
    {
        if (!auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            return response()->json(['error' => 'Unauthorized. Admin access required.'], 403);
        }

        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $stats = [
            'vehicles' => [
                'total' => Vehicle::count(),
                'active' => Vehicle::where('status', 'available')->count(),
                'in_maintenance' => Vehicle::where('status', 'maintenance')->count(),
                'in_use' => Vehicle::where('status', 'in_use')->count(),
            ],
            'maintenance' => [
                'overdue' => VehicleMaintenanceLog::whereDate('next_maintenance_due', '<', now())->count(),
                'upcoming_week' => VehicleMaintenanceLog::whereBetween('next_maintenance_due', [now(), now()->addWeek()])->count(),
                'upcoming_month' => VehicleMaintenanceLog::whereBetween('next_maintenance_due', [now(), now()->addMonth()])->count(),
                'completed_period' => VehicleMaintenanceLog::whereBetween('performed_at', [$dateFrom, $dateTo])->count(),
            ],
            'fuel' => [
                'total_logs_period' => VehicleFuelLog::whereBetween('filled_at', [$dateFrom, $dateTo])->count(),
                'total_fuel_period' => VehicleFuelLog::whereBetween('filled_at', [$dateFrom, $dateTo])->sum('fuel_amount'),
                'total_cost_period' => VehicleFuelLog::whereBetween('filled_at', [$dateFrom, $dateTo])->sum('fuel_cost'),
                'avg_cost_per_liter' => VehicleFuelLog::whereBetween('filled_at', [$dateFrom, $dateTo])
                    ->whereNotNull('fuel_cost')
                    ->where('fuel_amount', '>', 0)
                    ->selectRaw('AVG(fuel_cost / fuel_amount) as avg_cost')
                    ->value('avg_cost') ?? 0,
            ],
            'odometer' => [
                'total_readings_period' => VehicleOdometerLog::whereBetween('recorded_at', [$dateFrom, $dateTo])->count(),
                'total_distance_period' => VehicleOdometerLog::whereBetween('recorded_at', [$dateFrom, $dateTo])->sum('distance_traveled'),
                'vehicles_tracked' => VehicleOdometerLog::whereBetween('recorded_at', [$dateFrom, $dateTo])
                    ->distinct('vehicle_id')->count('vehicle_id'),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Get chart data for booking trends by month
     */
    public function getChartData(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        // Create date range for the specified month
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();
        $daysInMonth = $startOfMonth->daysInMonth;
        
        // Generate data for each day of the month
        $chartData = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startOfMonth->copy()->day($day);
            
            // Get bookings by asset type for this date
            $vehicleBookings = Booking::whereDate('created_at', $date)
                ->where('asset_type', 'App\Models\Vehicle')
                ->count();
            
            $meetingRoomBookings = Booking::whereDate('created_at', $date)
                ->where('asset_type', 'App\Models\MeetingRoom')
                ->count();
            
            $itAssetBookings = Booking::whereDate('created_at', $date)
                ->where('asset_type', 'App\Models\ItAsset')
                ->count();
            
            $chartData[] = [
                'date' => $date->format('M j'),
                'vehicles' => $vehicleBookings,
                'meeting_rooms' => $meetingRoomBookings,
                'it_assets' => $itAssetBookings,
            ];
        }
        
        return response()->json($chartData);
    }

    /**
     * Get peak usage patterns data by time periods
     */
    public function getPeakUsageData(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        // Create date range for the specified month
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();
        $daysInMonth = $startOfMonth->daysInMonth;
        
        // Generate data for each day of the month
        $chartData = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startOfMonth->copy()->day($day);
            
            // Morning bookings (6:00 AM - 12:00 PM)
            $morningBookings = Booking::whereDate('start_time', $date)
                ->whereTime('start_time', '>=', '06:00:00')
                ->whereTime('start_time', '<', '12:00:00')
                ->count();
            
            // Afternoon bookings (12:00 PM - 6:00 PM)
            $afternoonBookings = Booking::whereDate('start_time', $date)
                ->whereTime('start_time', '>=', '12:00:00')
                ->whereTime('start_time', '<', '18:00:00')
                ->count();
            
            // Evening bookings (6:00 PM - 11:59 PM)
            $eveningBookings = Booking::whereDate('start_time', $date)
                ->whereTime('start_time', '>=', '18:00:00')
                ->whereTime('start_time', '<=', '23:59:59')
                ->count();
            
            $chartData[] = [
                'date' => $date->format('M j'),
                'morning' => $morningBookings,
                'afternoon' => $afternoonBookings,
                'evening' => $eveningBookings,
            ];
        }
        
        return response()->json($chartData);
    }

    /**
     * Generate event title for calendar display
     */
    private function generateEventTitle($booking)
    {
        $assetName = $booking->asset ? 
            (method_exists($booking->asset, 'name') ? $booking->asset->name : 
            (method_exists($booking->asset, 'title') ? $booking->asset->title : '')) : 
            'Unknown Asset';

        // $userName = $booking->bookedBy?->name ?? 'Unknown';
        $userName = $booking->bookedBy
            ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $booking->bookedBy->name)
            : 'Unknown';
        
        return "{$assetName} - {$userName}";
    }

    /**
     * Format time range for display
     */
    private function formatTimeRange($startTime, $endTime)
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        
        if ($start->isSameDay($end)) {
            return $start->format('M j, Y') . ' (' . $start->format('g:i A') . ' - ' . $end->format('g:i A') . ')';
        } else {
            return $start->format('M j, Y g:i A') . ' - ' . $end->format('M j, Y g:i A');
        }
    }

    /**
     * Get background color based on status
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'pending' => '#fbbf24',     // yellow-400
            'approved' => '#10b981',    // green-500
            'rejected' => '#ef4444',    // red-500
            'cancelled' => '#6b7280',   // gray-500
            'done' => '#3b82f6',        // blue-500
            default => '#9ca3af'        // gray-400
        };
    }

    /**
     * Get border color based on status
     */
    private function getStatusBorderColor($status)
    {
        return match($status) {
            'pending' => '#f59e0b',     // yellow-500
            'approved' => '#059669',    // green-600
            'rejected' => '#dc2626',    // red-600
            'cancelled' => '#4b5563',   // gray-600
            'done' => '#2563eb',        // blue-600
            default => '#6b7280'        // gray-500
        };
    }

    /**
     * Get text color based on status
     */
    private function getTextColor($status)
    {
        return match($status) {
            'pending' => '#000000',     // black text for yellow background
            'approved' => '#ffffff',    // white text
            'rejected' => '#ffffff',    // white text
            'cancelled' => '#ffffff',   // white text
            'done' => '#ffffff',        // white text
            default => '#ffffff'        // white text
        };
    }

    /**
     * Get maintenance events for calendar
     */
    private function getMaintenanceEvents($start, $end, $vehicleId = null)
    {
        $query = VehicleMaintenanceLog::with(['vehicle', 'performedBy'])
            ->where(function($q) use ($start, $end) {
                // Include scheduled maintenance within date range
                $q->whereBetween('next_maintenance_due', [$start, $end])
                  ->orWhereBetween('performed_at', [$start, $end]);
            });

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        return $query->get()->map(function ($maintenance) {
            $isUpcoming = $maintenance->next_maintenance_due && $maintenance->next_maintenance_due->isFuture();
            $isOverdue = $maintenance->next_maintenance_due && $maintenance->next_maintenance_due->isPast();
            
            return [
                'id' => 'maintenance_' . $maintenance->id,
                'title' => $this->generateMaintenanceTitle($maintenance),
                'start' => ($maintenance->next_maintenance_due ?? $maintenance->performed_at)->toISOString(),
                'allDay' => true,
                'backgroundColor' => $this->getMaintenanceColor($isOverdue, $isUpcoming, $maintenance->performed_at),
                'borderColor' => $this->getMaintenanceBorderColor($isOverdue, $isUpcoming, $maintenance->performed_at),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'maintenance',
                    'maintenanceId' => $maintenance->id,
                    'vehicleId' => $maintenance->vehicle_id,
                    'vehicleName' => $maintenance->vehicle->model ?? 'Unknown Vehicle',
                    'vehiclePlate' => $maintenance->vehicle->license_plate ?? 'N/A',
                    'maintenanceType' => $maintenance->maintenance_type,
                    'description' => $maintenance->description,
                    'cost' => $maintenance->cost ? '$' . number_format($maintenance->cost, 2) : 'N/A',
                    'serviceProvider' => $maintenance->service_provider ?? 'N/A',
                    'performedBy' => $maintenance->performedBy->name ?? 'N/A',
                    'status' => $isOverdue ? 'overdue' : ($isUpcoming ? 'upcoming' : 'completed'),
                    'isOverdue' => $isOverdue,
                    'isUpcoming' => $isUpcoming,
                    'isCompleted' => !is_null($maintenance->performed_at),
                    'odometerAtService' => $maintenance->odometer_at_service ? number_format($maintenance->odometer_at_service) . ' km' : 'N/A',
                ]
            ];
        });
    }

    /**
     * Get fuel events for calendar
     */
    private function getFuelEvents($start, $end, $vehicleId = null, $showCompleted = false)
    {
        if (!$showCompleted) {
            return collect(); // Only show fuel events if explicitly requested
        }

        $query = VehicleFuelLog::with(['vehicle', 'filledBy'])
            ->whereBetween('filled_at', [$start, $end]);

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        return $query->get()->map(function ($fuelLog) {
            return [
                'id' => 'fuel_' . $fuelLog->id,
                'title' => 'Fuel: ' . ($fuelLog->vehicle->model ?? 'Unknown') . ' (' . number_format($fuelLog->fuel_amount, 1) . 'L)',
                'start' => $fuelLog->filled_at->toISOString(),
                'allDay' => false,
                'backgroundColor' => '#059669', // green-600
                'borderColor' => '#047857', // green-700
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'fuel',
                    'fuelLogId' => $fuelLog->id,
                    'vehicleId' => $fuelLog->vehicle_id,
                    'vehicleName' => $fuelLog->vehicle->model ?? 'Unknown Vehicle',
                    'vehiclePlate' => $fuelLog->vehicle->license_plate ?? 'N/A',
                    'fuelAmount' => number_format($fuelLog->fuel_amount, 2) . ' L',
                    'fuelType' => ucfirst($fuelLog->fuel_type),
                    'fuelCost' => $fuelLog->fuel_cost ? '$' . number_format($fuelLog->fuel_cost, 2) : 'N/A',
                    'fuelStation' => $fuelLog->fuel_station ?? 'N/A',
                    'filledBy' => $fuelLog->filledBy->name ?? 'N/A',
                    'odometerReading' => $fuelLog->odometer_at_fill ? number_format($fuelLog->odometer_at_fill) . ' km' : 'N/A',
                ]
            ];
        });
    }

    /**
     * Get odometer milestone events
     */
    private function getOdometerEvents($start, $end, $vehicleId = null)
    {
        $query = VehicleOdometerLog::with(['vehicle', 'recordedBy'])
            ->whereBetween('recorded_at', [$start, $end])
            ->where('reading_type', 'service'); // Only show service-related odometer readings

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        return $query->get()->map(function ($odometerLog) {
            return [
                'id' => 'odometer_' . $odometerLog->id,
                'title' => 'Service Reading: ' . ($odometerLog->vehicle->model ?? 'Unknown') . ' (' . number_format($odometerLog->odometer_reading) . ' km)',
                'start' => $odometerLog->recorded_at->toISOString(),
                'allDay' => false,
                'backgroundColor' => '#7c3aed', // purple-600
                'borderColor' => '#6d28d9', // purple-700
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'odometer',
                    'odometerLogId' => $odometerLog->id,
                    'vehicleId' => $odometerLog->vehicle_id,
                    'vehicleName' => $odometerLog->vehicle->model ?? 'Unknown Vehicle',
                    'vehiclePlate' => $odometerLog->vehicle->license_plate ?? 'N/A',
                    'odometerReading' => number_format($odometerLog->odometer_reading) . ' km',
                    'readingType' => ucfirst(str_replace('_', ' ', $odometerLog->reading_type)),
                    'distanceTraveled' => $odometerLog->distance_traveled ? number_format($odometerLog->distance_traveled) . ' km' : 'N/A',
                    'recordedBy' => $odometerLog->recordedBy->name ?? 'N/A',
                ]
            ];
        });
    }

    /**
     * Generate maintenance event title
     */
    private function generateMaintenanceTitle($maintenance)
    {
        $vehicle = $maintenance->vehicle->model ?? 'Unknown Vehicle';
        $type = $maintenance->maintenance_type ? ucfirst($maintenance->maintenance_type) : 'Maintenance';
        
        $isOverdue = $maintenance->next_maintenance_due && $maintenance->next_maintenance_due->isPast();
        $prefix = $isOverdue ? '⚠️ OVERDUE' : ($maintenance->performed_at ? '✅' : '🔧');
        
        return "{$prefix} {$type}: {$vehicle}";
    }

    /**
     * Get maintenance event colors
     */
    private function getMaintenanceColor($isOverdue, $isUpcoming, $performedAt)
    {
        if ($isOverdue) return '#dc2626'; // red-600 - overdue
        if ($performedAt) return '#10b981'; // green-500 - completed
        if ($isUpcoming) return '#f59e0b'; // yellow-500 - upcoming
        return '#6b7280'; // gray-500 - default
    }

    /**
     * Get maintenance border colors
     */
    private function getMaintenanceBorderColor($isOverdue, $isUpcoming, $performedAt)
    {
        if ($isOverdue) return '#b91c1c'; // red-700
        if ($performedAt) return '#059669'; // green-600
        if ($isUpcoming) return '#d97706'; // yellow-600
        return '#4b5563'; // gray-600
    }
}