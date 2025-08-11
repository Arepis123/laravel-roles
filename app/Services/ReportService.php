<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\ItAsset;
use App\Models\MeetingRoom;
use App\Models\Booking;
use App\Models\User;
use App\Models\ReportLog;
use Illuminate\Support\Facades\Storage;
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
            $dataArray = collect($data)->map(function ($item) {
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
                        // Fallback if you have a generic asset relationship
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

    private function getFileExtension($format)
    {
        switch ($format) {
            case 'pdf':
                return 'pdf';
            case 'csv':
                return 'csv';
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
}