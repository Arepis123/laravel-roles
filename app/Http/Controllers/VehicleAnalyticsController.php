<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleFuelLog;
use App\Models\VehicleOdometerLog;
use App\Models\VehicleMaintenanceLog;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleAnalyticsController extends Controller
{
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $vehicleId = $request->get('vehicle_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $analyticsType = $request->get('analytics_type', 'overview');

        // Debug logging to track what's being received
        \Log::info('Export request received', [
            'format' => $format,
            'analytics_type' => $analyticsType,
            'all_params' => $request->all(),
            'user_agent' => $request->header('User-Agent')
        ]);

        // Set default dates if not provided
        if (!$dateFrom) {
            $dateFrom = now()->startOfYear()->format('Y-m-d');
        }
        if (!$dateTo) {
            $dateTo = now()->endOfYear()->format('Y-m-d');
        }

        $data = $this->getAnalyticsData($vehicleId, $dateFrom, $dateTo, $analyticsType);

        if ($format === 'pdf') {
            \Log::info('Routing to PDF export');
            return $this->exportToPdf($data, $analyticsType);
        } else {
            \Log::info('Routing to CSV export (format: ' . $format . ')');
            return $this->exportToExcel($data, $analyticsType);
        }
    }

    private function getAnalyticsData($vehicleId, $dateFrom, $dateTo, $analyticsType)
    {
        $dateFrom = Carbon::parse($dateFrom);
        $dateTo = Carbon::parse($dateTo);

        $data = [
            'vehicle_id' => $vehicleId,
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
            'analytics_type' => $analyticsType,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];

        if ($vehicleId) {
            $data['vehicle'] = Vehicle::find($vehicleId);
        }

        switch ($analyticsType) {
            case 'fuel':
                $data['fuel_data'] = $this->getFuelData($vehicleId, $dateFrom, $dateTo);
                break;
            case 'odometer':
                $data['odometer_data'] = $this->getOdometerData($vehicleId, $dateFrom, $dateTo);
                break;
            case 'maintenance':
                $data['maintenance_data'] = $this->getMaintenanceData($vehicleId, $dateFrom, $dateTo);
                break;
            case 'overview':
            default:
                $data['overview_data'] = $this->getOverviewData($vehicleId, $dateFrom, $dateTo);
                break;
        }

        return $data;
    }

    private function getFuelData($vehicleId, $dateFrom, $dateTo)
    {
        $query = VehicleFuelLog::with(['vehicle', 'booking.user', 'filledBy'])
            ->whereBetween('filled_at', [$dateFrom, $dateTo]);

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        return $query->orderBy('filled_at', 'desc')->get();
    }

    private function getOdometerData($vehicleId, $dateFrom, $dateTo)
    {
        $query = VehicleOdometerLog::with(['vehicle', 'booking.user', 'recordedBy'])
            ->whereBetween('recorded_at', [$dateFrom, $dateTo]);

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        return $query->orderBy('recorded_at', 'desc')->get();
    }

    private function getMaintenanceData($vehicleId, $dateFrom, $dateTo)
    {
        $query = VehicleMaintenanceLog::with(['vehicle', 'booking.user', 'recordedBy'])
            ->whereBetween('performed_at', [$dateFrom, $dateTo]);

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        return $query->orderBy('performed_at', 'desc')->get();
    }

    private function getOverviewData($vehicleId, $dateFrom, $dateTo)
    {
        $data = [];

        // Get all vehicles or specific vehicle
        if ($vehicleId) {
            $vehicles = Vehicle::where('id', $vehicleId)->with(['fuelLogs', 'odometerLogs', 'maintenanceLogs'])->get();
        } else {
            $vehicles = Vehicle::with(['fuelLogs', 'odometerLogs', 'maintenanceLogs'])->get();
        }

        $data['vehicles'] = $vehicles;
        $data['fuel_logs'] = $this->getFuelData($vehicleId, $dateFrom, $dateTo);
        $data['odometer_logs'] = $this->getOdometerData($vehicleId, $dateFrom, $dateTo);
        $data['maintenance_logs'] = $this->getMaintenanceData($vehicleId, $dateFrom, $dateTo);

        return $data;
    }

    private function exportToExcel($data, $analyticsType)
    {
        $filename = "vehicle_analytics_{$analyticsType}_" . date('Y-m-d_H-i-s') . '.csv';
        
        // Get vehicle name for report header
        $vehicleName = 'All Vehicles';
        if (isset($data['vehicle'])) {
            $vehicleName = $data['vehicle']->model . ' (' . $data['vehicle']->plate_number . ')';
        }

        return $this->generateCsvResponse($data, $analyticsType, $filename, $vehicleName);
    }

    private function generateCsvResponse($data, $analyticsType, $filename, $vehicleName)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data, $analyticsType, $vehicleName) {
            $file = fopen('php://output', 'w');
            
            // Add report header
            fputcsv($file, ["Vehicle Analytics Report - " . ucfirst($analyticsType)]);
            fputcsv($file, ["Vehicle: " . $vehicleName]);
            fputcsv($file, ["Date Range: " . $data['date_from'] . " to " . $data['date_to']]);
            fputcsv($file, ["Generated At: " . $data['generated_at']]);
            fputcsv($file, []); // Empty row

            switch($analyticsType) {
                case 'fuel':
                    $this->addFuelDataToCsv($file, $data);
                    break;
                case 'odometer':
                    $this->addOdometerDataToCsv($file, $data);
                    break;
                case 'maintenance':
                    $this->addMaintenanceDataToCsv($file, $data);
                    break;
                case 'overview':
                    $this->addOverviewDataToCsv($file, $data);
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function addFuelDataToCsv($file, $data)
    {
        if (!isset($data['fuel_data']) || $data['fuel_data']->count() == 0) {
            fputcsv($file, ["No fuel records found for the selected period."]);
            return;
        }

        // Add summary
        $totalCost = $data['fuel_data']->sum('fuel_cost');
        $recordCount = $data['fuel_data']->count();
        fputcsv($file, ["Summary"]);
        fputcsv($file, ["Total Records: " . $recordCount]);
        fputcsv($file, ["Total Cost: RM " . number_format($totalCost, 2)]);
        fputcsv($file, []); // Empty row

        // Add table headers
        fputcsv($file, [
            'Date & Time',
            'Vehicle Model',
            'Plate Number',
            'Fuel Type',
            'Amount (L)',
            'Cost (RM)',
            'Location',
            'Odometer Reading (km)',
            'Filled By',
            'Notes'
        ]);

        // Add data rows
        foreach($data['fuel_data'] as $log) {
            fputcsv($file, [
                $log->filled_at->format('Y-m-d H:i'),
                $log->vehicle->model ?? 'N/A',
                $log->vehicle->plate_number ?? 'N/A',
                ucfirst($log->fuel_type),
                number_format($log->fuel_amount, 2),
                number_format($log->fuel_cost, 2),
                $log->fuel_station ?? '-',
                $log->odometer_at_fill ? number_format($log->odometer_at_fill) : '-',
                $log->filledBy->name ?? '-',
                $log->notes ?? '-'
            ]);
        }
    }

    private function addOdometerDataToCsv($file, $data)
    {
        if (!isset($data['odometer_data']) || $data['odometer_data']->count() == 0) {
            fputcsv($file, ["No odometer records found for the selected period."]);
            return;
        }

        // Add summary
        $recordCount = $data['odometer_data']->count();
        fputcsv($file, ["Summary"]);
        fputcsv($file, ["Total Records: " . $recordCount]);
        fputcsv($file, []); // Empty row

        // Add table headers
        fputcsv($file, [
            'Date & Time',
            'Vehicle Model',
            'Plate Number',
            'Reading (km)',
            'Type',
            'Performed By',
            'Recorded By',
            'Notes'
        ]);

        // Add data rows
        foreach($data['odometer_data'] as $log) {
            fputcsv($file, [
                $log->recorded_at->format('Y-m-d H:i'),
                $log->vehicle->model ?? 'N/A',
                $log->vehicle->plate_number ?? 'N/A',
                number_format($log->odometer_reading),
                ucfirst($log->reading_type),
                $log->performed_by ?? '-',
                $log->recordedBy->name ?? '-',
                $log->notes ?? '-'
            ]);
        }
    }

    private function addMaintenanceDataToCsv($file, $data)
    {
        if (!isset($data['maintenance_data']) || $data['maintenance_data']->count() == 0) {
            fputcsv($file, ["No maintenance records found for the selected period."]);
            return;
        }

        // Add summary
        $totalCost = $data['maintenance_data']->sum('cost');
        $recordCount = $data['maintenance_data']->count();
        fputcsv($file, ["Summary"]);
        fputcsv($file, ["Total Records: " . $recordCount]);
        fputcsv($file, ["Total Cost: RM " . number_format($totalCost, 2)]);
        fputcsv($file, []); // Empty row

        // Add table headers
        fputcsv($file, [
            'Date & Time',
            'Vehicle',
            'Plate',
            'Type',
            'Title',
            'Cost (RM)',
            'Status',
            'Service Provider',
            'Next Due'
        ]);

        // Add data rows
        foreach($data['maintenance_data'] as $log) {
            fputcsv($file, [
                $log->performed_at->format('Y-m-d H:i'),
                $log->vehicle->model ?? 'N/A',
                $log->vehicle->plate_number ?? 'N/A',
                ucfirst(str_replace('_', ' ', $log->maintenance_type)),
                $log->title,
                number_format($log->cost ?? 0, 2),
                ucfirst($log->status),
                $log->service_provider ?? '-',
                $log->next_maintenance_due ? $log->next_maintenance_due->format('Y-m-d') : '-'
            ]);
        }
    }

    private function addOverviewDataToCsv($file, $data)
    {
        if (!isset($data['overview_data']['vehicles']) || $data['overview_data']['vehicles']->count() == 0) {
            fputcsv($file, ["No vehicles found."]);
            return;
        }

        // Add summary
        $recordCount = $data['overview_data']['vehicles']->count();
        fputcsv($file, ["Summary"]);
        fputcsv($file, ["Total Records: " . $recordCount]);
        fputcsv($file, []); // Empty row

        // Add table headers
        fputcsv($file, [
            'ID',
            'Model',
            'Plate Number',
            'Status',
            'Engine Number',
            'Chassis Number',
            'Year',
            'Registered'
        ]);

        // Add data rows
        foreach($data['overview_data']['vehicles'] as $vehicle) {
            fputcsv($file, [
                $vehicle->id,
                $vehicle->model,
                $vehicle->plate_number,
                ucfirst($vehicle->status),
                $vehicle->engine_number ?? '-',
                $vehicle->chassis_number ?? '-',
                $vehicle->year ?? '-',
                $vehicle->created_at->format('Y-m-d')
            ]);
        }
    }

    private function exportToPdf($data, $analyticsType)
    {
        $filename = "vehicle_analytics_{$analyticsType}_" . date('Y-m-d_H-i-s') . '.pdf';

        try {
            \Log::info('Starting PDF generation', [
                'filename' => $filename,
                'analytics_type' => $analyticsType,
                'data_keys' => array_keys($data)
            ]);
            
            // Create a simple HTML content for testing
            $html = '<h1>Vehicle Analytics Report - ' . ucfirst($analyticsType) . '</h1>';
            $html .= '<p>Date Range: ' . $data['date_from'] . ' to ' . $data['date_to'] . '</p>';
            $html .= '<p>Generated at: ' . $data['generated_at'] . '</p>';
            
            // Add some basic data based on analytics type
            if ($analyticsType === 'fuel' && isset($data['fuel_data'])) {
                $html .= '<h2>Fuel Records: ' . count($data['fuel_data']) . '</h2>';
            } elseif ($analyticsType === 'odometer' && isset($data['odometer_data'])) {
                $html .= '<h2>Odometer Records: ' . count($data['odometer_data']) . '</h2>';
            } elseif ($analyticsType === 'maintenance' && isset($data['maintenance_data'])) {
                $html .= '<h2>Maintenance Records: ' . count($data['maintenance_data']) . '</h2>';
            } elseif ($analyticsType === 'overview' && isset($data['overview_data']['vehicles'])) {
                $html .= '<h2>Total Vehicles: ' . count($data['overview_data']['vehicles']) . '</h2>';
            }
            
            // Create PDF using simple HTML instead of complex Blade template
            $pdf = Pdf::loadHTML($html);
            
            // Set PDF options for better compatibility
            $pdf->setPaper('A4', 'landscape');
            
            \Log::info('PDF generated successfully, returning download response');
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('PDF generation failed', [
                'error' => $e->getMessage(),
                'analytics_type' => $analyticsType,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a fallback response with error details
            return response()->json([
                'error' => 'PDF generation failed',
                'message' => $e->getMessage(),
                'analytics_type' => $analyticsType
            ], 500);
        }
    }
}