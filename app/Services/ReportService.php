<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\ItAsset;
use App\Models\MeetingRoom;
use App\Models\Booking;
use App\Models\User;
use App\Models\ReportLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    public function generateAssetsReport($filters = [], $format = 'excel')
    {
        try {
            // Get data from all asset types
            $vehicles = $this->getVehicleData($filters);
            $itAssets = $this->getItAssetData($filters);  
            $meetingRooms = $this->getMeetingRoomData($filters);

            // Combine all assets into a collection
            $allAssets = collect()
                ->concat($vehicles->map(function ($item) {
                    return (object) [
                        'id' => 'V-' . $item->id,
                        'name' => $item->name ?? $item->brand ?? $item->model ?? 'Unnamed Vehicle',
                        'type' => 'Vehicle',
                        'category' => $item->vehicle_type ?? $item->type ?? 'N/A',
                        'status' => $item->status ?? 'N/A',
                        'description' => $item->description ?? $item->model ?? 'N/A',
                        'created_by' => $this->getCreatedByName($item),
                        'created_at' => $item->created_at,
                    ];
                }))
                ->concat($itAssets->map(function ($item) {
                    return (object) [
                        'id' => 'IT-' . $item->id,
                        'name' => $item->name ?? $item->asset_name ?? 'Unnamed IT Asset',
                        'type' => 'IT Asset',
                        'category' => $item->category ?? $item->type ?? 'N/A',
                        'status' => $item->status ?? 'N/A',
                        'description' => $item->description ?? $item->specifications ?? 'N/A',
                        'created_by' => $this->getCreatedByName($item),
                        'created_at' => $item->created_at,
                    ];
                }))
                ->concat($meetingRooms->map(function ($item) {
                    return (object) [
                        'id' => 'MR-' . $item->id,
                        'name' => $item->name ?? $item->room_name ?? 'Unnamed Room',
                        'type' => 'Meeting Room',
                        'category' => 'Room',
                        'status' => $item->status ?? 'N/A',
                        'description' => $item->description ?? "Capacity: " . ($item->capacity ?? 'N/A'),
                        'created_by' => $this->getCreatedByName($item),
                        'created_at' => $item->created_at,
                    ];
                }));

            return $this->generateFile($allAssets, 'assets', $format, $filters);
            
        } catch (\Exception $e) {
            \Log::error('Assets Report Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function generateVehiclesReport($filters = [], $format = 'excel')
    {
        try {
            \Log::info('Starting enhanced vehicles report generation', ['format' => $format, 'filters' => $filters]);
            
            // Get all vehicles with enhanced analytics using new normalized models
            $vehicles = $this->getVehiclesWithEnhancedDetails($filters);
            
            \Log::info('Enhanced vehicles query completed', ['count' => $vehicles->count()]);

            return $this->generateFile($vehicles, 'vehicles', $format, $filters);
            
        } catch (\Exception $e) {
            \Log::error('Vehicles Report Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    private function getVehiclesWithDetails($filters)
    {
        $query = Vehicle::query();
        
        // Apply basic vehicle filters
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        $vehicles = $query->get();

        // Enhance vehicles with fuel and odometer data from bookings
        $vehiclesWithDetails = $vehicles->map(function ($vehicle) use ($filters) {
            // Get fuel data for this vehicle
            $fuelData = $this->getVehicleFuelData($vehicle->id, $filters);
            
            // Get odometer data for this vehicle
            $odometerData = $this->getVehicleOdometerData($vehicle->id, $filters);
            
            // Get booking statistics
            $bookingStats = $this->getVehicleBookingStats($vehicle->id, $filters);

            return (object) [
                'id' => $vehicle->id,
                'model' => $vehicle->model ?? 'N/A',
                'plate_number' => $vehicle->plate_number ?? 'N/A',
                'capacity' => $vehicle->capacity ?? 'N/A',
                'driver_name' => $vehicle->driver_name ?? 'N/A',
                'notes' => $vehicle->notes ?? 'N/A',
                'status' => $vehicle->status ?? 'available',
                'created_at' => $vehicle->created_at,
                'created_by' => $this->getCreatedByName($vehicle),
                
                // Fuel Data
                'total_fuel_filled' => $fuelData['total_fuel'],
                'fuel_sessions_count' => $fuelData['fuel_sessions'],
                'avg_fuel_per_session' => $fuelData['avg_fuel'],
                'last_fuel_date' => $fuelData['last_fuel_date'],
                'last_fuel_amount' => $fuelData['last_fuel_amount'],
                
                // Odometer Data
                'latest_odometer' => $odometerData['latest_odometer'],
                'initial_odometer' => $odometerData['initial_odometer'],
                'total_distance' => $odometerData['total_distance'],
                'last_odometer_date' => $odometerData['last_odometer_date'],
                
                // Booking Statistics
                'total_bookings' => $bookingStats['total_bookings'],
                'completed_bookings' => $bookingStats['completed_bookings'],
                'pending_bookings' => $bookingStats['pending_bookings'],
                'approved_bookings' => $bookingStats['approved_bookings'],
                'utilization_rate' => $bookingStats['utilization_rate'],
            ];
        });

        return $vehiclesWithDetails;
    }

    private function getVehicleFuelData($vehicleId, $filters)
    {
        $query = Booking::where('asset_type', Vehicle::class)
            ->where('asset_id', $vehicleId)
            ->where('status', 'done');
            
        // Only add done_details filter if the column exists
        if ($this->columnExists('bookings', 'done_details')) {
            $query->whereNotNull('done_details');
        }

        // Apply booking date filters
        if (isset($filters['booking_date_from']) && $filters['booking_date_from']) {
            $query->where('start_time', '>=', $filters['booking_date_from']);
        }
        
        if (isset($filters['booking_date_to']) && $filters['booking_date_to']) {
            $query->where('end_time', '<=', $filters['booking_date_to']);
        }

        $bookings = $query->get();
        
        $totalFuel = 0;
        $fuelSessions = 0;
        $lastFuelDate = null;
        $lastFuelAmount = 0;
        
        foreach ($bookings as $booking) {
            $doneDetails = $booking->done_details ?? [];
            
            if (isset($doneDetails['gas_filled']) && $doneDetails['gas_filled'] && 
                isset($doneDetails['gas_amount']) && $doneDetails['gas_amount'] > 0) {
                
                $totalFuel += (float) $doneDetails['gas_amount'];
                $fuelSessions++;
                
                if (!$lastFuelDate || $booking->end_time > $lastFuelDate) {
                    $lastFuelDate = $booking->end_time;
                    $lastFuelAmount = (float) $doneDetails['gas_amount'];
                }
            }
        }
        
        return [
            'total_fuel' => $totalFuel,
            'fuel_sessions' => $fuelSessions,
            'avg_fuel' => $fuelSessions > 0 ? round($totalFuel / $fuelSessions, 2) : 0,
            'last_fuel_date' => $lastFuelDate ? $lastFuelDate->format('Y-m-d') : null,
            'last_fuel_amount' => $lastFuelAmount,
        ];
    }

    private function getVehicleOdometerData($vehicleId, $filters)
    {
        $query = Booking::where('asset_type', Vehicle::class)
            ->where('asset_id', $vehicleId)
            ->where('status', 'done')
            ->orderBy('end_time', 'asc');
            
        // Only add done_details filter if the column exists
        if ($this->columnExists('bookings', 'done_details')) {
            $query->whereNotNull('done_details');
        }

        // Apply booking date filters
        if (isset($filters['booking_date_from']) && $filters['booking_date_from']) {
            $query->where('start_time', '>=', $filters['booking_date_from']);
        }
        
        if (isset($filters['booking_date_to']) && $filters['booking_date_to']) {
            $query->where('end_time', '<=', $filters['booking_date_to']);
        }

        $bookings = $query->get();
        
        $latestOdometer = null;
        $initialOdometer = null;
        $lastOdometerDate = null;
        
        foreach ($bookings as $booking) {
            $doneDetails = $booking->done_details ?? [];
            
            if (isset($doneDetails['odometer']) && $doneDetails['odometer'] > 0) {
                if ($initialOdometer === null) {
                    $initialOdometer = (float) $doneDetails['odometer'];
                }
                
                $latestOdometer = (float) $doneDetails['odometer'];
                $lastOdometerDate = $booking->end_time;
            }
        }
        
        $totalDistance = ($latestOdometer && $initialOdometer) ? 
            $latestOdometer - $initialOdometer : 0;
        
        return [
            'latest_odometer' => $latestOdometer,
            'initial_odometer' => $initialOdometer,
            'total_distance' => $totalDistance,
            'last_odometer_date' => $lastOdometerDate ? $lastOdometerDate->format('Y-m-d') : null,
        ];
    }

    private function getVehicleBookingStats($vehicleId, $filters)
    {
        $query = Booking::where('asset_type', Vehicle::class)
            ->where('asset_id', $vehicleId);

        // Apply booking date filters
        if (isset($filters['booking_date_from']) && $filters['booking_date_from']) {
            $query->where('start_time', '>=', $filters['booking_date_from']);
        }
        
        if (isset($filters['booking_date_to']) && $filters['booking_date_to']) {
            $query->where('end_time', '<=', $filters['booking_date_to']);
        }

        $totalBookings = $query->count();
        $completedBookings = (clone $query)->where('status', 'done')->count();
        $pendingBookings = (clone $query)->where('status', 'pending')->count();
        $approvedBookings = (clone $query)->where('status', 'approved')->count();
        
        // Calculate utilization rate (completed bookings / total bookings)
        $utilizationRate = $totalBookings > 0 ? 
            round(($completedBookings / $totalBookings) * 100, 1) : 0;
        
        return [
            'total_bookings' => $totalBookings,
            'completed_bookings' => $completedBookings,
            'pending_bookings' => $pendingBookings,
            'approved_bookings' => $approvedBookings,
            'utilization_rate' => $utilizationRate,
        ];
    }

    private function getVehicleData($filters)
    {
        // Check if Vehicle model has createdBy relationship
        $query = Vehicle::query();
        
        // Only load createdBy if the relationship exists
        if (method_exists(new Vehicle, 'createdBy')) {
            $query->with(['createdBy']);
        }
        
        return $this->applyAssetFilters($query, $filters)->get();
    }

    private function getItAssetData($filters)
    {
        // Check if ItAsset model has createdBy relationship
        $query = ItAsset::query();
        
        // Only load createdBy if the relationship exists
        if (method_exists(new ItAsset, 'createdBy')) {
            $query->with(['createdBy']);
        }
        
        return $this->applyAssetFilters($query, $filters)->get();
    }

    private function getMeetingRoomData($filters)
    {
        // Check if MeetingRoom model has createdBy relationship
        $query = MeetingRoom::query();
        
        // Only load createdBy if the relationship exists
        if (method_exists(new MeetingRoom, 'createdBy')) {
            $query->with(['createdBy']);
        }
        
        return $this->applyAssetFilters($query, $filters)->get();
    }

    private function getCreatedByName($item)
    {
        // Try different ways to get the creator name
        if (isset($item->createdBy) && $item->createdBy) {
            return $item->createdBy->name;
        }
        
        if (isset($item->created_by_name)) {
            return $item->created_by_name;
        }
        
        if (isset($item->created_by) && is_numeric($item->created_by)) {
            // Try to get user by ID
            try {
                $user = User::find($item->created_by);
                return $user ? $user->name : 'N/A';
            } catch (\Exception $e) {
                return 'N/A';
            }
        }
        
        return 'N/A';
    }

    private function applyAssetFilters($query, $filters)
    {
        // Apply common filters for all asset types
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    public function generateBookingsReport($filters = [], $format = 'excel')
    {
        try {
            \Log::info('Starting bookings report generation', ['format' => $format, 'filters' => $filters]);
            
            $query = Booking::with(['user']);

            // Try to load different asset relationships if they exist
            $booking = new Booking;
            if (method_exists($booking, 'vehicle')) {
                $query->with(['vehicle']);
            }
            if (method_exists($booking, 'itAsset')) {
                $query->with(['itAsset']);
            }
            if (method_exists($booking, 'meetingRoom')) {
                $query->with(['meetingRoom']);
            }

            // Apply filters
            if (isset($filters['date_from']) && $filters['date_from']) {
                $query->where('created_at', '>=', $filters['date_from']);
            }
            
            if (isset($filters['date_to']) && $filters['date_to']) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['booking_date_from']) && $filters['booking_date_from']) {
                $query->where('start_date', '>=', $filters['booking_date_from']);
            }

            if (isset($filters['booking_date_to']) && $filters['booking_date_to']) {
                $query->where('end_date', '<=', $filters['booking_date_to']);
            }

            $bookings = $query->get();
            
            \Log::info('Bookings query completed', ['count' => $bookings->count()]);

            return $this->generateFile($bookings, 'bookings', $format, $filters);
            
        } catch (\Exception $e) {
            \Log::error('Bookings Report Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function generateUsersReport($filters = [], $format = 'excel')
    {
        try {
            \Log::info('Starting users report generation', ['format' => $format, 'filters' => $filters]);
            
            $query = User::withCount(['bookings']);

            // Apply filters
            if (isset($filters['date_from']) && $filters['date_from']) {
                $query->where('created_at', '>=', $filters['date_from']);
            }
            
            if (isset($filters['date_to']) && $filters['date_to']) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            if (isset($filters['role']) && $filters['role']) {
                $query->where('role', $filters['role']);
            }

            if (isset($filters['status']) && $filters['status']) {
                if ($filters['status'] === 'active') {
                    $query->whereNotNull('email_verified_at');
                } elseif ($filters['status'] === 'inactive') {
                    $query->whereNull('email_verified_at');
                }
            }

            $users = $query->get();
            
            \Log::info('Users query completed', ['count' => $users->count()]);

            return $this->generateFile($users, 'users', $format, $filters);
            
        } catch (\Exception $e) {
            \Log::error('Users Report Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    private function generateFile($data, $type, $format, $filters)
    {
        $fileName = $type . '_report_' . date('Y-m-d_H-i-s') . '.' . $this->getFileExtension($format);
        $filePath = 'reports/' . $fileName;

        // Ensure the reports directory exists
        $reportsDir = storage_path('app/reports');
        if (!file_exists($reportsDir)) {
            mkdir($reportsDir, 0755, true);
            \Log::info('Created reports directory: ' . $reportsDir);
        }

        // Normalize path for Windows
        $normalizedPath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
        \Log::info('File paths', [
            'original_path' => $filePath,
            'normalized_path' => $normalizedPath,
            'full_path' => storage_path('app/' . $filePath)
        ]);

        switch ($format) {
            case 'pdf':
                return $this->generatePDF($data, $type, $filePath, $filters);
            case 'csv':
                return $this->generateCSV($data, $type, $filePath);
            case 'json':
                return $this->generateJSON($data, $type, $filePath, $filters);
            case 'xml':
                return $this->generateXML($data, $type, $filePath, $filters);
            case 'html':
                return $this->generateHTML($data, $type, $filePath, $filters);
            case 'txt':
                return $this->generateTXT($data, $type, $filePath);
            default:
                return $this->generateExcel($data, $type, $filePath);
        }
    }

    private function generateExcel($data, $type, $filePath)
    {
        try {
            \Log::info('Starting Excel generation', ['type' => $type, 'count' => $data->count()]);
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers based on type
            $headers = $this->getHeaders($type);
            \Log::info('Headers set', ['headers' => $headers]);
            
            $sheet->fromArray($headers, null, 'A1');

            // Style headers
            $sheet->getStyle('A1:' . chr(64 + count($headers)) . '1')->getFont()->setBold(true);

            // Add data
            $row = 2;
            foreach ($data as $item) {
                try {
                    $rowData = $this->formatRowData($item, $type);
                    \Log::info('Row data formatted', ['row' => $row, 'data' => $rowData]);
                    $sheet->fromArray($rowData, null, 'A' . $row);
                    $row++;
                } catch (\Exception $e) {
                    \Log::error('Error formatting row data', ['row' => $row, 'error' => $e->getMessage(), 'item' => $item]);
                    throw $e;
                }
            }

            // Auto-size columns
            foreach (range('A', chr(64 + count($headers))) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            \Log::info('About to save Excel file', ['path' => storage_path('app/' . $filePath)]);
            
            $writer = new Xlsx($spreadsheet);
            $writer->save(storage_path('app/' . $filePath));
            
            \Log::info('Excel file saved successfully');

            return $this->logReport($type, 'excel', $filePath, basename($filePath), $data->count(), $filters ?? []);
            
        } catch (\Exception $e) {
            \Log::error('Excel generation error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    private function generateCSV($data, $type, $filePath)
    {
        try {
            \Log::info('Starting CSV generation', ['type' => $type, 'count' => $data->count()]);
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $headers = $this->getHeaders($type);
            $sheet->fromArray($headers, null, 'A1');

            // Add data
            $row = 2;
            foreach ($data as $item) {
                try {
                    $rowData = $this->formatRowData($item, $type);
                    $sheet->fromArray($rowData, null, 'A' . $row);
                    $row++;
                } catch (\Exception $e) {
                    \Log::error('Error formatting CSV row data', ['row' => $row, 'error' => $e->getMessage()]);
                    throw $e;
                }
            }

            $writer = new Csv($spreadsheet);
            $writer->save(storage_path('app/' . $filePath));
            
            \Log::info('CSV file saved successfully');

            return $this->logReport($type, 'csv', $filePath, basename($filePath), $data->count(), $filters ?? []);
            
        } catch (\Exception $e) {
            \Log::error('CSV generation error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    private function generatePDF($data, $type, $filePath, $filters)
    {
        try {
            \Log::info('Starting PDF generation', [
                'type' => $type,
                'count' => $data->count(),
                'file_path' => $filePath,
                'full_path' => storage_path('app/' . $filePath)
            ]);
            
            // Convert data to array to avoid any model-related issues
            $dataArray = collect($data)->map(function ($item) use ($type) {
                if ($type === 'vehicles') {
                    return (object) [
                        'id' => $item->id ?? 'N/A',
                        'model' => $item->model ?? 'N/A',
                        'plate_number' => $item->plate_number ?? 'N/A',
                        'capacity' => $item->capacity ?? 'N/A',
                        'driver_name' => $item->driver_name ?? 'N/A',
                        'status' => $item->status ?? 'N/A',
                        'total_fuel_filled' => $item->total_fuel_filled ?? 0,
                        'fuel_sessions_count' => $item->fuel_sessions_count ?? 0,
                        'latest_odometer' => $item->latest_odometer ?? 'N/A',
                        'total_distance' => $item->total_distance ?? 0,
                        'total_bookings' => $item->total_bookings ?? 0,
                        'utilization_rate' => $item->utilization_rate ?? 0,
                        'created_at' => $item->created_at ?? now(),
                    ];
                }
                
                return (object) [
                    'id' => $item->id ?? 'N/A',
                    'name' => $item->name ?? 'N/A', 
                    'type' => $item->type ?? 'N/A',
                    'category' => $item->category ?? 'N/A',
                    'status' => $item->status ?? 'N/A',
                    'description' => $item->description ?? 'N/A',
                    'created_by' => $item->created_by ?? 'N/A',
                    'created_at' => $item->created_at ?? now(),
                    // For bookings
                    'user' => isset($item->user) ? $item->user : null,
                    'vehicle' => isset($item->vehicle) ? $item->vehicle : null,
                    'itAsset' => isset($item->itAsset) ? $item->itAsset : null,
                    'meetingRoom' => isset($item->meetingRoom) ? $item->meetingRoom : null,
                    'start_date' => $item->start_date ?? null,
                    'end_date' => $item->end_date ?? null,
                    'notes' => $item->notes ?? 'N/A',
                    // For users
                    'email' => $item->email ?? 'N/A',
                    'role' => $item->role ?? 'N/A',
                    'bookings_count' => $item->bookings_count ?? 0,
                    'email_verified_at' => $item->email_verified_at ?? null,
                ];
            });

            \Log::info('Data converted for PDF', ['processed_count' => $dataArray->count()]);

            // Check if PDF template exists
            $templatePath = resource_path('views/reports/pdf-template.blade.php');
            \Log::info('Template check', [
                'template_path' => $templatePath,
                'template_exists' => file_exists($templatePath)
            ]);

            if (!file_exists($templatePath)) {
                throw new \Exception('PDF template not found at: ' . $templatePath);
            }

            \Log::info('Loading PDF view');
            
            $pdf = Pdf::loadView('reports.pdf-template', [
                'data' => $dataArray,
                'type' => $type,
                'filters' => $filters,
                'generated_at' => now()->format('Y-m-d H:i:s')
            ]);

            \Log::info('PDF view loaded, generating PDF output');

            $pdfOutput = $pdf->output();
            \Log::info('PDF output generated', ['output_size' => strlen($pdfOutput)]);

            // Use direct file operations instead of Storage::put()
            $fullPath = storage_path('app/' . $filePath);
            \Log::info('Saving PDF directly to filesystem', ['full_path' => $fullPath]);
            
            $bytesWritten = file_put_contents($fullPath, $pdfOutput);
            
            // Verify file was created
            $fileCreated = file_exists($fullPath);
            $fileSize = $fileCreated ? filesize($fullPath) : 0;
            
            \Log::info('PDF file save result', [
                'bytes_written' => $bytesWritten,
                'file_created' => $fileCreated,
                'file_size' => $fileSize,
                'full_path' => $fullPath
            ]);

            if (!$fileCreated || $bytesWritten === false) {
                throw new \Exception('PDF file was not created successfully. Bytes written: ' . ($bytesWritten ?: 'false'));
            }

            return $this->logReport($type, 'pdf', $filePath, basename($filePath), $dataArray->count(), $filters);
            
        } catch (\Exception $e) {
            \Log::error('PDF generation error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    private function getHeaders($type)
    {
        switch ($type) {
            case 'assets':
                return ['ID', 'Name', 'Type', 'Category', 'Status', 'Description', 'Created By', 'Created At'];
            case 'vehicles':
                return [
                    'ID', 'Model', 'Plate Number', 'Capacity', 'Driver Name', 'Status',
                    'Total Fuel (L)', 'Fuel Sessions', 'Avg Fuel/Session', 'Last Fuel Date', 'Last Fuel Amount',
                    'Latest Odometer', 'Total Distance', 'Total Bookings', 'Completed Bookings', 'Utilization Rate (%)',
                    'Created By', 'Created At'
                ];
            case 'bookings':
                return ['ID', 'Asset', 'Asset Type', 'User', 'Start Date', 'End Date', 'Status', 'Notes', 'Created At'];
            case 'users':
                return ['ID', 'Name', 'Email', 'Role', 'Total Bookings', 'Email Verified', 'Created At'];
            default:
                return [];
        }
    }

    private function formatRowData($item, $type)
    {
        try {
            switch ($type) {
                case 'assets':
                    return [
                        $item->id,
                        $item->name,
                        $item->type ?? 'N/A',
                        $item->category ?? 'N/A',
                        ucfirst($item->status),
                        $item->description ?? 'N/A',
                        $item->created_by ?? 'N/A',
                        $item->created_at->format('Y-m-d H:i:s')
                    ];
                case 'vehicles':
                    return [
                        $item->id,
                        $item->model ?? 'N/A',
                        $item->plate_number ?? 'N/A',
                        $item->capacity ?? 'N/A',
                        $item->driver_name ?? 'N/A',
                        ucfirst($item->status ?? 'available'),
                        $item->total_fuel_filled ?? 0,
                        $item->fuel_sessions_count ?? 0,
                        $item->avg_fuel_per_session ?? 0,
                        $item->last_fuel_date ?? 'N/A',
                        $item->last_fuel_amount ?? 0,
                        $item->latest_odometer ?? 'N/A',
                        $item->total_distance ?? 0,
                        $item->total_bookings ?? 0,
                        $item->completed_bookings ?? 0,
                        $item->utilization_rate ?? 0,
                        $item->created_by ?? 'N/A',
                        $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ];
                case 'bookings':
                    // Get asset name and type from different possible relationships
                    $assetName = 'N/A';
                    $assetType = 'N/A';
                    
                    if ($item->vehicle) {
                        $assetName = $item->vehicle->name;
                        $assetType = 'Vehicle';
                    } elseif ($item->itAsset) {
                        $assetName = $item->itAsset->name;
                        $assetType = 'IT Asset';
                    } elseif ($item->meetingRoom) {
                        $assetName = $item->meetingRoom->name;
                        $assetType = 'Meeting Room';
                    } elseif (isset($item->asset)) {
                        $assetName = $item->asset->name ?? 'N/A';
                        $assetType = 'Asset';
                    }

                    return [
                        $item->id,
                        $assetName,
                        $assetType,
                        $item->user->name ?? 'N/A',
                        $item->start_date ? $item->start_date->format('Y-m-d') : 'N/A',
                        $item->end_date ? $item->end_date->format('Y-m-d') : 'N/A',
                        ucfirst($item->status),
                        $item->notes ?? 'N/A',
                        $item->created_at->format('Y-m-d H:i:s')
                    ];
                case 'users':
                    \Log::info('Formatting user row data', [
                        'id' => $item->id ?? 'missing',
                        'name' => $item->name ?? 'missing',
                        'email' => $item->email ?? 'missing',
                        'role' => $item->role ?? 'missing',
                        'bookings_count' => $item->bookings_count ?? 'missing',
                        'email_verified_at' => $item->email_verified_at ?? 'missing',
                        'created_at' => $item->created_at ?? 'missing'
                    ]);
                    
                    return [
                        $item->id ?? 'N/A',
                        $item->name ?? 'N/A',
                        $item->email ?? 'N/A',
                        ucfirst($item->role ?? 'user'),
                        $item->bookings_count ?? 0,
                        $item->email_verified_at ? 'Yes' : 'No',
                        $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ];
                default:
                    return [];
            }
        } catch (\Exception $e) {
            \Log::error('Error in formatRowData', [
                'type' => $type,
                'error' => $e->getMessage(),
                'item_id' => $item->id ?? 'unknown'
            ]);
            throw $e;
        }
    }

    private function generateJSON($data, $type, $filePath, $filters)
    {
        try {
            \Log::info('Starting JSON generation', ['type' => $type, 'count' => $data->count()]);
            
            // Format data for JSON export
            $jsonData = [
                'report_info' => [
                    'type' => $type,
                    'generated_at' => now()->toISOString(),
                    'generated_by' => auth()->user()->name ?? 'System',
                    'filters' => $filters,
                    'total_records' => $data->count()
                ],
                'data' => collect($data)->map(function ($item) use ($type) {
                    return $this->formatItemForStructuredExport($item, $type);
                })->toArray()
            ];

            $jsonContent = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            file_put_contents(storage_path('app/' . $filePath), $jsonContent);
            
            \Log::info('JSON file saved successfully');

            return $this->logReport($type, 'json', $filePath, basename($filePath), $data->count(), $filters);
            
        } catch (\Exception $e) {
            \Log::error('JSON generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateXML($data, $type, $filePath, $filters)
    {
        try {
            \Log::info('Starting XML generation', ['type' => $type, 'count' => $data->count()]);
            
            $xml = new \SimpleXMLElement('<report/>');
            
            // Add metadata
            $info = $xml->addChild('report_info');
            $info->addChild('type', $type);
            $info->addChild('generated_at', now()->toISOString());
            $info->addChild('generated_by', htmlspecialchars(auth()->user()->name ?? 'System'));
            $info->addChild('total_records', $data->count());
            
            // Add filters
            $filtersNode = $info->addChild('filters');
            foreach ($filters as $key => $value) {
                if ($value !== null && $value !== '') {
                    $filtersNode->addChild($key, htmlspecialchars($value));
                }
            }
            
            // Add data
            $dataNode = $xml->addChild('data');
            foreach ($data as $item) {
                $itemData = $this->formatItemForStructuredExport($item, $type);
                $itemNode = $dataNode->addChild('record');
                
                foreach ($itemData as $key => $value) {
                    $itemNode->addChild($key, htmlspecialchars($value ?? ''));
                }
            }

            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->formatOutput = true;
            $dom->loadXML($xml->asXML());
            
            file_put_contents(storage_path('app/' . $filePath), $dom->saveXML());
            
            \Log::info('XML file saved successfully');

            return $this->logReport($type, 'xml', $filePath, basename($filePath), $data->count(), $filters);
            
        } catch (\Exception $e) {
            \Log::error('XML generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateHTML($data, $type, $filePath, $filters)
    {
        try {
            \Log::info('Starting HTML generation', ['type' => $type, 'count' => $data->count()]);
            
            $headers = $this->getHeaders($type);
            
            $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . ucfirst($type) . ' Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        tr:hover { background-color: #f5f5f5; }
        .footer { margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="header">
        <h1>' . ucfirst($type) . ' Report</h1>
        <p><strong>Generated:</strong> ' . now()->format('Y-m-d H:i:s') . '</p>
        <p><strong>Generated By:</strong> ' . (auth()->user()->name ?? 'System') . '</p>
        <p><strong>Total Records:</strong> ' . $data->count() . '</p>';
        
            if (!empty($filters)) {
                $html .= '<p><strong>Applied Filters:</strong> ' . collect($filters)->map(function($value, $key) {
                    return "$key: $value";
                })->implode(', ') . '</p>';
            }
            
            $html .= '
    </div>
    <table>
        <thead>
            <tr>';
            
            foreach ($headers as $header) {
                $html .= '<th>' . htmlspecialchars($header) . '</th>';
            }
            
            $html .= '
            </tr>
        </thead>
        <tbody>';
        
            foreach ($data as $item) {
                $rowData = $this->formatRowData($item, $type);
                $html .= '<tr>';
                foreach ($rowData as $cell) {
                    $html .= '<td>' . htmlspecialchars($cell ?? '') . '</td>';
                }
                $html .= '</tr>';
            }
            
            $html .= '
        </tbody>
    </table>
    <div class="footer">
        <p>Report generated by Laravel Booking System</p>
    </div>
</body>
</html>';

            file_put_contents(storage_path('app/' . $filePath), $html);
            
            \Log::info('HTML file saved successfully');

            return $this->logReport($type, 'html', $filePath, basename($filePath), $data->count(), $filters);
            
        } catch (\Exception $e) {
            \Log::error('HTML generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateTXT($data, $type, $filePath)
    {
        try {
            \Log::info('Starting TXT generation', ['type' => $type, 'count' => $data->count()]);
            
            $headers = $this->getHeaders($type);
            $content = '';
            
            // Add header
            $content .= str_repeat('=', 80) . "\n";
            $content .= strtoupper($type) . " REPORT\n";
            $content .= str_repeat('=', 80) . "\n";
            $content .= "Generated: " . now()->format('Y-m-d H:i:s') . "\n";
            $content .= "Generated By: " . (auth()->user()->name ?? 'System') . "\n";
            $content .= "Total Records: " . $data->count() . "\n";
            $content .= str_repeat('=', 80) . "\n\n";
            
            // Calculate column widths
            $widths = array_fill(0, count($headers), 15);
            foreach ($headers as $index => $header) {
                $widths[$index] = max($widths[$index], strlen($header));
            }
            
            foreach ($data as $item) {
                $rowData = $this->formatRowData($item, $type);
                foreach ($rowData as $index => $cell) {
                    if (isset($widths[$index])) {
                        $widths[$index] = max($widths[$index], strlen($cell ?? ''));
                    }
                }
            }
            
            // Add headers
            foreach ($headers as $index => $header) {
                $content .= str_pad($header, $widths[$index] + 2);
            }
            $content .= "\n";
            $content .= str_repeat('-', array_sum($widths) + count($widths) * 2) . "\n";
            
            // Add data rows
            foreach ($data as $item) {
                $rowData = $this->formatRowData($item, $type);
                foreach ($rowData as $index => $cell) {
                    if (isset($widths[$index])) {
                        $content .= str_pad($cell ?? '', $widths[$index] + 2);
                    }
                }
                $content .= "\n";
            }
            
            file_put_contents(storage_path('app/' . $filePath), $content);
            
            \Log::info('TXT file saved successfully');

            return $this->logReport($type, 'txt', $filePath, basename($filePath), $data->count(), []);
            
        } catch (\Exception $e) {
            \Log::error('TXT generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function formatItemForStructuredExport($item, $type)
    {
        switch ($type) {
            case 'assets':
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'type' => $item->type ?? 'N/A',
                    'category' => $item->category ?? 'N/A',
                    'status' => $item->status,
                    'description' => $item->description ?? 'N/A',
                    'created_by' => $item->created_by ?? 'N/A',
                    'created_at' => $item->created_at ? $item->created_at->toISOString() : null
                ];
            case 'vehicles':
                return [
                    'id' => $item->id,
                    'model' => $item->model ?? 'N/A',
                    'plate_number' => $item->plate_number ?? 'N/A',
                    'capacity' => $item->capacity ?? 'N/A',
                    'driver_name' => $item->driver_name ?? 'N/A',
                    'status' => $item->status ?? 'available',
                    'total_fuel_filled' => $item->total_fuel_filled ?? 0,
                    'fuel_sessions_count' => $item->fuel_sessions_count ?? 0,
                    'avg_fuel_per_session' => $item->avg_fuel_per_session ?? 0,
                    'last_fuel_date' => $item->last_fuel_date,
                    'last_fuel_amount' => $item->last_fuel_amount ?? 0,
                    'latest_odometer' => $item->latest_odometer ?? 'N/A',
                    'total_distance' => $item->total_distance ?? 0,
                    'total_bookings' => $item->total_bookings ?? 0,
                    'completed_bookings' => $item->completed_bookings ?? 0,
                    'utilization_rate' => $item->utilization_rate ?? 0,
                    'created_by' => $item->created_by ?? 'N/A',
                    'created_at' => $item->created_at ? $item->created_at->toISOString() : null
                ];
            case 'bookings':
                // Get asset name and type
                $assetName = 'N/A';
                $assetType = 'N/A';
                
                if (isset($item->vehicle)) {
                    $assetName = $item->vehicle->name ?? 'N/A';
                    $assetType = 'Vehicle';
                } elseif (isset($item->itAsset)) {
                    $assetName = $item->itAsset->name ?? 'N/A';
                    $assetType = 'IT Asset';
                } elseif (isset($item->meetingRoom)) {
                    $assetName = $item->meetingRoom->name ?? 'N/A';
                    $assetType = 'Meeting Room';
                }
                
                return [
                    'id' => $item->id,
                    'asset_name' => $assetName,
                    'asset_type' => $assetType,
                    'user_name' => isset($item->user) ? $item->user->name : 'N/A',
                    'user_email' => isset($item->user) ? $item->user->email : 'N/A',
                    'start_date' => $item->start_date ? $item->start_date->toDateString() : null,
                    'end_date' => $item->end_date ? $item->end_date->toDateString() : null,
                    'status' => $item->status,
                    'notes' => $item->notes ?? 'N/A',
                    'created_at' => $item->created_at ? $item->created_at->toISOString() : null
                ];
            case 'users':
                return [
                    'id' => $item->id ?? 'N/A',
                    'name' => $item->name ?? 'N/A',
                    'email' => $item->email ?? 'N/A',
                    'role' => $item->role ?? 'user',
                    'total_bookings' => $item->bookings_count ?? 0,
                    'email_verified' => $item->email_verified_at ? true : false,
                    'email_verified_at' => $item->email_verified_at ? $item->email_verified_at->toISOString() : null,
                    'created_at' => $item->created_at ? $item->created_at->toISOString() : null
                ];
            default:
                return (array) $item;
        }
    }

    private function getFileExtension($format)
    {
        switch ($format) {
            case 'pdf':
                return 'pdf';
            case 'csv':
                return 'csv';
            case 'json':
                return 'json';
            case 'xml':
                return 'xml';
            case 'html':
                return 'html';
            case 'txt':
                return 'txt';
            default:
                return 'xlsx';
        }
    }

    private function logReport($type, $format, $filePath, $fileName, $recordCount, $filters)
    {
        return ReportLog::create([
            'report_type' => $type,
            'report_format' => $format,
            'filters' => $filters,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'record_count' => $recordCount,
            'generated_by' => auth()->id(),
            'generated_at' => now(),
        ]);
    }

    /**
     * Enhanced vehicle details method using new normalized models
     */
    private function getVehiclesWithEnhancedDetails($filters)
    {
        $query = Vehicle::query()
            ->withLatestLogs()  // Use new relationship to get latest logs
            ->with(['fuelLogs', 'odometerLogs', 'maintenanceLogs']); // Eager load all logs
        
        // Apply filters
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->where('created_at', '<=', $filters['date_to']);
        }
        
        $vehicles = $query->get();
        
        // Get enhanced data for each vehicle using new normalized structure
        $vehiclesWithDetails = $vehicles->map(function ($vehicle) use ($filters) {
            $startDate = $filters['booking_date_from'] ?? null;
            $endDate = $filters['booking_date_to'] ?? null;
            
            // Use the new Vehicle model methods for comprehensive stats
            $stats = $vehicle->getVehicleStats($startDate, $endDate);
            
            return (object) [
                'id' => $vehicle->id,
                'model' => $vehicle->model ?? 'N/A',
                'plate_number' => $vehicle->plate_number ?? 'N/A',
                'capacity' => $vehicle->capacity ?? 'N/A',
                'driver_name' => $vehicle->driver_name ?? 'N/A',
                'notes' => $vehicle->notes ?? 'N/A',
                'status' => $vehicle->status ?? 'available',
                'created_at' => $vehicle->created_at,
                'created_by' => 'System',
                
                // Enhanced fuel analytics using new models
                'total_fuel_filled' => $stats['fuel_data']['total_fuel'] ?? 0,
                'total_fuel_cost' => $stats['fuel_data']['total_cost'] ?? 0,
                'fuel_sessions_count' => $stats['fuel_data']['fuel_sessions'] ?? 0,
                'avg_fuel_per_session' => ($stats['fuel_data']['fuel_sessions'] ?? 0) > 0 
                    ? round(($stats['fuel_data']['total_fuel'] ?? 0) / $stats['fuel_data']['fuel_sessions'], 2) 
                    : 0,
                'average_fuel_efficiency' => $stats['fuel_data']['average_efficiency'] ?? 0,
                
                // Latest fuel log data
                'last_fuel_date' => $vehicle->latestFuelLog?->filled_at?->format('Y-m-d') ?? 'N/A',
                'last_fuel_amount' => $vehicle->latestFuelLog?->fuel_amount ?? 0,
                'last_fuel_cost' => $vehicle->latestFuelLog?->fuel_cost ?? 0,
                'last_fuel_station' => $vehicle->latestFuelLog?->fuel_station ?? 'N/A',
                
                // Enhanced odometer analytics
                'latest_odometer' => $vehicle->current_odometer_reading ?? 'N/A',
                'total_distance' => $stats['odometer_data']['total_distance'] ?? 0,
                'avg_distance_per_trip' => $stats['odometer_data']['average_distance_per_trip'] ?? 0,
                'initial_odometer' => $stats['odometer_data']['odometer_range']['min'] ?? 'N/A',
                'last_odometer_date' => $vehicle->latestOdometerLog?->recorded_at?->format('Y-m-d') ?? 'N/A',
                
                // Maintenance analytics
                'total_maintenance_cost' => $stats['maintenance_data']['total_cost'] ?? 0,
                'maintenance_sessions_count' => array_sum($stats['maintenance_data']['maintenance_count'] ?? []),
                'cost_per_km' => $stats['maintenance_data']['cost_per_km'] ?? 0,
                'last_maintenance_date' => $vehicle->latestMaintenanceLog?->performed_at?->format('Y-m-d') ?? 'N/A',
                'last_maintenance_type' => $vehicle->latestMaintenanceLog?->maintenance_type ?? 'N/A',
                
                // Enhanced booking statistics
                'total_bookings' => $stats['booking_stats']['total_bookings'] ?? 0,
                'completed_bookings' => $stats['booking_stats']['completed_bookings'] ?? 0,
                'pending_bookings' => $vehicle->bookings()->where('status', 'pending')->count(),
                'approved_bookings' => $vehicle->bookings()->where('status', 'approved')->count(),
                'utilization_rate' => ($stats['booking_stats']['total_bookings'] ?? 0) > 0 
                    ? round((($stats['booking_stats']['completed_bookings'] ?? 0) / $stats['booking_stats']['total_bookings']) * 100, 2) 
                    : 0,
                
                // Maintenance status
                'upcoming_maintenance_count' => $vehicle->upcoming_maintenance ? $vehicle->upcoming_maintenance->count() : 0,
                'overdue_maintenance_count' => $vehicle->overdue_maintenance ? $vehicle->overdue_maintenance->count() : 0,
                'maintenance_status' => $vehicle->overdue_maintenance && $vehicle->overdue_maintenance->count() > 0 ? 'Overdue' : 
                                       ($vehicle->upcoming_maintenance && $vehicle->upcoming_maintenance->count() > 0 ? 'Due Soon' : 'Good'),
                
                // Calculate total cost of ownership
                'total_cost_of_ownership' => ($stats['fuel_data']['total_cost'] ?? 0) + ($stats['maintenance_data']['total_cost'] ?? 0),
                
                // Cost per kilometer (comprehensive)
                'total_cost_per_km' => ($stats['odometer_data']['total_distance'] ?? 0) > 0 
                    ? round((($stats['fuel_data']['total_cost'] ?? 0) + ($stats['maintenance_data']['total_cost'] ?? 0)) / $stats['odometer_data']['total_distance'], 4) 
                    : 0,
            ];
        });

        return $vehiclesWithDetails;
    }

    /**
     * Check if a column exists in the database table
     */
    private function columnExists($table, $column)
    {
        try {
            return \Schema::hasColumn($table, $column);
        } catch (\Exception $e) {
            \Log::warning("Could not check if column {$column} exists in table {$table}: " . $e->getMessage());
            return false;
        }
    }
}