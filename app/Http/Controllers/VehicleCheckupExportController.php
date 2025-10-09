<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleCheckupLog;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleCheckupExportController extends Controller
{
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');

        // Build query with filters
        $query = VehicleCheckupLog::with(['vehicle', 'user', 'booking', 'template']);

        // Apply filters
        if ($request->has('vehicle') && $request->vehicle) {
            $query->where('vehicle_id', $request->vehicle);
        }

        if ($request->has('checkup_type') && $request->checkup_type) {
            $query->where('checkup_type', $request->checkup_type);
        }

        if ($request->has('status') && $request->status) {
            $query->where('overall_status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('checked_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('checked_at', '<=', $request->date_to . ' 23:59:59');
        }

        $checkups = $query->orderBy('checked_at', 'desc')->get();

        $data = [
            'checkups' => $checkups,
            'date_from' => $request->get('date_from', now()->startOfMonth()->format('Y-m-d')),
            'date_to' => $request->get('date_to', now()->endOfMonth()->format('Y-m-d')),
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];

        if ($format === 'pdf') {
            return $this->exportPdf($data);
        } else {
            return $this->exportCsv($data);
        }
    }

    private function exportCsv($data)
    {
        $filename = 'vehicle-checkups-' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Add report header
            fputcsv($file, ["Vehicle Checkup Report"]);
            fputcsv($file, ["Date Range: " . $data['date_from'] . " to " . $data['date_to']]);
            fputcsv($file, ["Generated At: " . $data['generated_at']]);
            fputcsv($file, ["Total Records: " . $data['checkups']->count()]);
            fputcsv($file, []); // Empty row

            if ($data['checkups']->count() == 0) {
                fputcsv($file, ["No checkup records found for the selected period."]);
            } else {
                foreach($data['checkups'] as $index => $checkup) {
                    // Checkup header
                    fputcsv($file, ["CHECKUP #" . ($index + 1)]);
                    fputcsv($file, ["Date & Time:", $checkup->checked_at->format('Y-m-d H:i')]);
                    fputcsv($file, ["Vehicle:", ($checkup->vehicle->model ?? 'N/A') . ' (' . ($checkup->vehicle->plate_number ?? 'N/A') . ')']);
                    fputcsv($file, ["Checkup Type:", $this->formatCheckupType($checkup->checkup_type)]);
                    fputcsv($file, ["Template Used:", $checkup->template->name ?? 'N/A']);
                    fputcsv($file, ["Checked By:", $checkup->user->name ?? 'N/A']);
                    fputcsv($file, ["Odometer Reading:", $checkup->odometer_reading ? number_format($checkup->odometer_reading) . ' km' : '-']);                    
                    fputcsv($file, ["Fuel Level:", $checkup->fuel_level ? (int)$checkup->fuel_level . ' bars' : '-']);
                    fputcsv($file, ["Overall Status:", $this->formatStatus($checkup->overall_status)]);
                    fputcsv($file, []); // Empty row

                    // Get applicable checks from template
                    $applicableChecks = $checkup->template ? $checkup->template->applicable_checks : [];

                    // Exterior Checks
                    if ($this->hasCategoryChecks($applicableChecks, 'exterior')) {
                        fputcsv($file, ["EXTERIOR CHECKS"]);
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'exterior_body_condition', 'Body Condition');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'exterior_lights', 'Lights');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'exterior_mirrors', 'Mirrors');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'exterior_windshield', 'Windshield');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'exterior_tires', 'Tires');
                        fputcsv($file, []); // Empty row
                    }

                    // Interior Checks
                    if ($this->hasCategoryChecks($applicableChecks, 'interior')) {
                        fputcsv($file, ["INTERIOR CHECKS"]);
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'interior_seats_seatbelts', 'Seats & Seatbelts');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'interior_dashboard', 'Dashboard');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'interior_horn', 'Horn');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'interior_wipers', 'Wipers');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'interior_ac', 'AC/Heating');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'interior_cleanliness', 'Cleanliness');
                        fputcsv($file, []); // Empty row
                    }

                    // Engine Checks
                    if ($this->hasCategoryChecks($applicableChecks, 'engine')) {
                        fputcsv($file, ["ENGINE & FLUIDS CHECKS"]);
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'engine_oil', 'Engine Oil');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'engine_coolant', 'Coolant');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'engine_brake_fluid', 'Brake Fluid');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'engine_battery', 'Battery');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'engine_washer_fluid', 'Windshield Washer Fluid');
                        fputcsv($file, []); // Empty row
                    }

                    // Functional Checks
                    if ($this->hasCategoryChecks($applicableChecks, 'functional')) {
                        fputcsv($file, ["FUNCTIONAL TESTS"]);
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'functional_brakes', 'Brakes');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'functional_steering', 'Steering');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'functional_transmission', 'Transmission');
                        $this->addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, 'functional_emergency_kit', 'Emergency Kit');
                        fputcsv($file, []); // Empty row
                    }

                    // General Notes
                    if ($checkup->general_notes) {
                        fputcsv($file, ["GENERAL NOTES:", $checkup->general_notes]);
                    }

                    fputcsv($file, []); // Empty row
                    fputcsv($file, ["=" . str_repeat("=", 80)]); // Separator
                    fputcsv($file, []); // Empty row
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function addCheckToCsvIfApplicable($file, $checkup, $applicableChecks, $field, $label)
    {
        // If no template or check is in applicable list, show it
        if (empty($applicableChecks) || in_array($field, $applicableChecks)) {
            $status = $checkup->$field ? 'PASS' : 'FAIL';
            $notesField = $field . '_notes';
            $notes = $checkup->$notesField ?? '';

            if ($notes) {
                fputcsv($file, [$label . ':', $status, 'Notes:', $notes]);
            } else {
                fputcsv($file, [$label . ':', $status]);
            }
        }
    }

    private function hasCategoryChecks($applicableChecks, $category)
    {
        // If no template, show all categories
        if (empty($applicableChecks)) {
            return true;
        }

        // Check if any check from this category is in applicable list
        foreach ($applicableChecks as $check) {
            if (strpos($check, $category . '_') === 0) {
                return true;
            }
        }

        return false;
    }

    private function exportPdf($data)
    {
        $filename = 'vehicle-checkups-' . date('Y-m-d_H-i-s') . '.pdf';

        try {
            $html = '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 9px; }
                    h1 { text-align: center; font-size: 16px; margin-bottom: 5px; }
                    .header { text-align: center; margin-bottom: 15px; font-size: 8px; }
                    .checkup-container { page-break-after: always; margin-bottom: 20px; }
                    .checkup-header { background-color: #1BA79D; color: white; padding: 8px; margin-bottom: 10px; }
                    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
                    .info-table td { padding: 4px 8px; border: 1px solid #ddd; font-size: 8px; }
                    .info-table td:first-child { font-weight: bold; width: 30%; background-color: #f3f4f6; }

                    .check-section { margin-bottom: 8px; }
                    .check-section-title { background-color: #6b7280; color: white; padding: 4px 8px; font-weight: bold; font-size: 9px; margin-bottom: 3px; }
                    .check-item { padding: 3px 8px; border-bottom: 1px solid #e5e7eb; font-size: 8px; }
                    .check-item:last-child { border-bottom: none; }
                    .pass { color: #059669; font-weight: bold; }
                    .fail { color: #dc2626; font-weight: bold; }
                    .notes { color: #6b7280; font-style: italic; margin-left: 10px; }

                    .status-approved { color: #059669; font-weight: bold; }
                    .status-rejected { color: #dc2626; font-weight: bold; }
                    .status-needs-maintenance { color: #f59e0b; font-weight: bold; }
                    .status-approved-with-notes { color: #2563eb; font-weight: bold; }
                </style>
            </head>
            <body>
                <h1>Vehicle Checkup Report</h1>
                <div class="header">
                    <p>Date Range: ' . $data['date_from'] . ' to ' . $data['date_to'] . ' | Generated At: ' . $data['generated_at'] . ' | Total Records: ' . $data['checkups']->count() . '</p>
                </div>';

            if ($data['checkups']->count() == 0) {
                $html .= '<p style="text-align:center;">No checkup records found for the selected period.</p>';
            } else {
                foreach($data['checkups'] as $index => $checkup) {
                    $statusClass = 'status-' . str_replace('_', '-', $checkup->overall_status);

                    $html .= '<div class="checkup-container">';
                    $html .= '<div class="checkup-header">CHECKUP #' . ($index + 1) . '</div>';

                    // Basic Information Table
                    $html .= '<table class="info-table">
                        <tr>
                            <td>Date & Time</td>
                            <td>' . $checkup->checked_at->format('Y-m-d H:i') . '</td>
                            <td>Vehicle</td>
                            <td>' . ($checkup->vehicle->model ?? 'N/A') . ' (' . ($checkup->vehicle->plate_number ?? 'N/A') . ')</td>
                        </tr>
                        <tr>
                            <td>Checkup Type</td>
                            <td>' . $this->formatCheckupType($checkup->checkup_type) . '</td>
                            <td>Template Used</td>
                            <td>' . ($checkup->template->name ?? 'N/A') . '</td>
                        </tr>
                        <tr>
                            <td>Checked By</td>
                            <td>' . ($checkup->user->name ?? 'N/A') . '</td>
                            <td>Odometer Reading</td>
                            <td>' . ($checkup->odometer_reading ? number_format($checkup->odometer_reading) . ' km' : '-') . '</td>
                        </tr>
                        <tr>
                            <td>Fuel Level</td>
                            <td>' . ($checkup->fuel_level ? (int)$checkup->fuel_level . ' bars' : '-') . '</td>
                            <td>Overall Status</td>
                            <td class="' . $statusClass . '">' . $this->formatStatus($checkup->overall_status) . '</td>
                        </tr>
                    </table>';

                    // Get applicable checks from template
                    $applicableChecks = $checkup->template ? $checkup->template->applicable_checks : [];

                    // Exterior Checks
                    if ($this->hasCategoryChecks($applicableChecks, 'exterior')) {
                        $html .= '<div class="check-section">';
                        $html .= '<div class="check-section-title">EXTERIOR CHECKS</div>';
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'exterior_body_condition', 'Body Condition');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'exterior_lights', 'Lights');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'exterior_mirrors', 'Mirrors');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'exterior_windshield', 'Windshield');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'exterior_tires', 'Tires');
                        $html .= '</div>';
                    }

                    // Interior Checks
                    if ($this->hasCategoryChecks($applicableChecks, 'interior')) {
                        $html .= '<div class="check-section">';
                        $html .= '<div class="check-section-title">INTERIOR CHECKS</div>';
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'interior_seats_seatbelts', 'Seats & Seatbelts');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'interior_dashboard', 'Dashboard');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'interior_horn', 'Horn');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'interior_wipers', 'Wipers');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'interior_ac', 'AC/Heating');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'interior_cleanliness', 'Cleanliness');
                        $html .= '</div>';
                    }

                    // Engine Checks
                    if ($this->hasCategoryChecks($applicableChecks, 'engine')) {
                        $html .= '<div class="check-section">';
                        $html .= '<div class="check-section-title">ENGINE & FLUIDS CHECKS</div>';
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'engine_oil', 'Engine Oil');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'engine_coolant', 'Coolant');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'engine_brake_fluid', 'Brake Fluid');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'engine_battery', 'Battery');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'engine_washer_fluid', 'Windshield Washer Fluid');
                        $html .= '</div>';
                    }

                    // Functional Checks
                    if ($this->hasCategoryChecks($applicableChecks, 'functional')) {
                        $html .= '<div class="check-section">';
                        $html .= '<div class="check-section-title">FUNCTIONAL TESTS</div>';
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'functional_brakes', 'Brakes');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'functional_steering', 'Steering');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'functional_transmission', 'Transmission');
                        $html .= $this->formatCheckForPdfIfApplicable($checkup, $applicableChecks, 'functional_emergency_kit', 'Emergency Kit');
                        $html .= '</div>';
                    }

                    // General Notes
                    if ($checkup->general_notes) {
                        $html .= '<div class="check-section">';
                        $html .= '<div class="check-section-title">GENERAL NOTES</div>';
                        $html .= '<div class="check-item">' . nl2br(htmlspecialchars($checkup->general_notes)) . '</div>';
                        $html .= '</div>';
                    }

                    $html .= '</div>'; // End checkup-container
                }
            }

            $html .= '</body></html>';

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Checkup PDF generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'PDF generation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function formatCheckForPdfIfApplicable($checkup, $applicableChecks, $field, $label)
    {
        // If no template or check is in applicable list, show it
        if (empty($applicableChecks) || in_array($field, $applicableChecks)) {
            $status = $checkup->$field ? 'PASS' : 'FAIL';
            $statusClass = $checkup->$field ? 'pass' : 'fail';
            $notesField = $field . '_notes';
            $notes = $checkup->$notesField ?? '';

            $html = '<div class="check-item">';
            $html .= '<strong>' . htmlspecialchars($label) . ':</strong> <span class="' . $statusClass . '">' . $status . '</span>';

            if ($notes) {
                $html .= '<span class="notes"> - ' . htmlspecialchars($notes) . '</span>';
            }

            $html .= '</div>';

            return $html;
        }

        return '';
    }

    private function countFailedChecks($checkup)
    {
        $checks = [
            'exterior_body_condition', 'exterior_lights', 'exterior_mirrors', 'exterior_windshield', 'exterior_tires',
            'interior_seats_seatbelts', 'interior_dashboard', 'interior_horn', 'interior_wipers', 'interior_ac', 'interior_cleanliness',
            'engine_oil', 'engine_coolant', 'engine_brake_fluid', 'engine_battery', 'engine_washer_fluid',
            'functional_brakes', 'functional_steering', 'functional_transmission', 'functional_emergency_kit'
        ];

        $failed = 0;
        foreach ($checks as $check) {
            if ($checkup->$check === false || $checkup->$check === 0) {
                $failed++;
            }
        }

        return $failed;
    }

    private function formatCheckupType($type)
    {
        return match($type) {
            'pre_trip' => 'Pre-Trip',
            'post_trip' => 'Post-Trip',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'annual' => 'Annual',
            default => ucfirst($type)
        };
    }

    private function formatStatus($status)
    {
        return match($status) {
            'approved' => 'Approved',
            'approved_with_notes' => 'Approved with Notes',
            'rejected' => 'Rejected',
            'needs_maintenance' => 'Needs Maintenance',
            default => ucfirst($status)
        };
    }
}
