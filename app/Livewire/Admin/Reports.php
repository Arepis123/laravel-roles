<?php

namespace App\Livewire\Admin;

use App\Models\ReportLog;
use App\Services\ReportService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class Reports extends Component
{
    use WithPagination;

    public $reportType = 'assets';
    public $reportFormat = 'excel';
    
    // Filters
    public $dateFrom = '';
    public $dateTo = '';
    public $status = '';
    public $categoryId = '';
    public $role = '';
    public $bookingDateFrom = '';
    public $bookingDateTo = '';

    // UI State
    public $showFilters = false;
    public $isGenerating = false;

    // Sorting
    public $sortField = 'generated_at';
    public $sortDirection = 'desc';

    protected $reportService;

    public function boot(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function mount()
    {
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subMonth()->format('Y-m-d');
    }

    public function updatedReportType()
    {
        $this->resetFilters();
    }

    public function resetFilters()
    {
        $this->status = '';
        $this->categoryId = '';
        $this->role = '';
        $this->bookingDateFrom = '';
        $this->bookingDateTo = '';
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        
        $this->resetPage();
    }

    public function generateReport()
    {
        $this->isGenerating = true;

        try {
            $filters = $this->getFilters();

            switch ($this->reportType) {
                case 'assets':
                    $report = $this->reportService->generateAssetsReport($filters, $this->reportFormat);
                    break;
                case 'bookings':
                    $report = $this->reportService->generateBookingsReport($filters, $this->reportFormat);
                    break;
                case 'users':
                    $report = $this->reportService->generateUsersReport($filters, $this->reportFormat);
                    break;
                default:
                    throw new \Exception('Invalid report type');
            }

            $this->dispatch('report-generated', [
                'message' => 'Report generated successfully! File: ' . $report->file_name
            ]);

        } catch (\Exception $e) {
            $this->dispatch('report-error', [
                'message' => 'Error generating report: ' . $e->getMessage()
            ]);
        } finally {
            $this->isGenerating = false;
        }
    }

    public function downloadReport($reportId)
    {
        try {
            \Log::info('Download attempt started', ['report_id' => $reportId]);
            
            $report = ReportLog::findOrFail($reportId);
            
            // Use direct file path instead of Storage facade
            $fullPath = storage_path('app/' . $report->file_path);
            
            \Log::info('Report found', [
                'file_path' => $report->file_path,
                'file_name' => $report->file_name,
                'full_path' => $fullPath,
                'file_exists' => file_exists($fullPath),
                'is_readable' => is_readable($fullPath)
            ]);
            
            // Check if physical file exists
            if (!file_exists($fullPath)) {
                \Log::error('Physical file does not exist', ['full_path' => $fullPath]);
                $this->dispatch('report-error', [
                    'message' => 'Report file not found. Please generate a new report.'
                ]);
                return;
            }

            // Check if file is readable
            if (!is_readable($fullPath)) {
                \Log::error('File is not readable', ['full_path' => $fullPath]);
                $this->dispatch('report-error', [
                    'message' => 'Report file is not accessible. Please check file permissions.'
                ]);
                return;
            }

            \Log::info('File exists and is readable, starting download');
            
            // Use direct file download instead of Storage::download
            return response()->download($fullPath, $report->file_name, [
                'Content-Type' => $this->getContentType($report->report_format),
                'Content-Disposition' => 'attachment; filename="' . $report->file_name . '"'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Download error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $this->dispatch('report-error', [
                'message' => 'Error downloading report: ' . $e->getMessage()
            ]);
            return;
        }
    }

    public function emailReport($reportId)
    {
        try {
            \Log::info('Email report attempt started', ['report_id' => $reportId]);
            
            $report = ReportLog::findOrFail($reportId);
            $user = auth()->user();
            
            // Check if file exists
            $fullPath = storage_path('app/' . $report->file_path);
            if (!file_exists($fullPath)) {
                $this->dispatch('report-error', [
                    'message' => 'Report file not found. Please generate a new report.'
                ]);
                return;
            }

            // Send email with attachment
            Mail::send('emails.report', [
                'user' => $user,
                'report' => $report,
                'reportType' => ucfirst($report->report_type),
                'generatedAt' => $report->generated_at->format('M d, Y H:i')
            ], function ($message) use ($user, $report, $fullPath) {
                $message->to($user->email, $user->name)
                        ->subject('Your ' . ucfirst($report->report_type) . ' Report - ' . $report->generated_at->format('M d, Y'))
                        ->attach($fullPath, [
                            'as' => $report->file_name,
                            'mime' => $this->getContentType($report->report_format)
                        ]);
            });

            $this->dispatch('report-emailed', [
                'message' => 'Report has been sent to your email: ' . $user->email
            ]);

            \Log::info('Report email sent successfully', [
                'report_id' => $reportId,
                'email' => $user->email
            ]);

        } catch (\Exception $e) {
            \Log::error('Email report error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $this->dispatch('report-error', [
                'message' => 'Error sending report email: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteReport($reportId)
    {
        $report = ReportLog::findOrFail($reportId);
        
        if (Storage::exists($report->file_path)) {
            Storage::delete($report->file_path);
        }
        
        $report->delete();
        
        $this->dispatch('report-deleted', [
            'message' => 'Report deleted successfully!'
        ]);
    }

    private function getFilters()
    {
        $filters = [
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ];

        if ($this->status) {
            $filters['status'] = $this->status;
        }

        if ($this->reportType === 'assets' && $this->categoryId) {
            $filters['category_id'] = $this->categoryId;
        }

        if ($this->reportType === 'users' && $this->role) {
            $filters['role'] = $this->role;
        }

        if ($this->reportType === 'bookings') {
            if ($this->bookingDateFrom) {
                $filters['booking_date_from'] = $this->bookingDateFrom;
            }
            if ($this->bookingDateTo) {
                $filters['booking_date_to'] = $this->bookingDateTo;
            }
        }

        return $filters;
    }

    private function getContentType($format)
    {
        switch ($format) {
            case 'pdf':
                return 'application/pdf';
            case 'csv':
                return 'text/csv';
            case 'excel':
            default:
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        }
    }

    public function render()
    {
        $reportLogs = ReportLog::with('generatedBy')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        // Since we don't have categories, return empty array
        $categories = [];

        return view('livewire.admin.reports', [
            'reportLogs' => $reportLogs,
            'categories' => $categories,
        ]);
    }
}