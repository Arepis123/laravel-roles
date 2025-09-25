<?php

namespace App\Livewire\Admin;

use App\Models\MeetingRoom;
use App\Models\Vehicle;
use App\Models\ItAsset;
use App\Models\Booking;
use App\Models\User;
use App\Models\QrCodeLog;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QrCodeManagement extends Component
{
    use WithPagination;

    public $selectedAssetType = 'all';
    public $search = '';
    public $qrFilter = 'all'; // all, generated, missing
    public $selectedAssets = [];
    public $selectAll = false;

    // Sorting properties
    public $sortField = 'name';
    public $sortDirection = 'asc';

    // Modal states
    public $showPreviewModal = false;
    public $showAnalyticsModal = false;
    public $showBulkModal = false;
    public $showTemplateModal = false;

    // Current asset for preview
    public $previewAsset = null;

    // Template settings
    public $templateType = 'labels'; // labels, cards (test)
    public $qrSize = 'medium'; // small, medium, large
    public $includeAssetInfo = true;
    public $includeLogo = false;

    // Analytics data
    public $analyticsData = [];
    public $selectedAnalyticsPeriod = '7days'; // 7days, 30days, 3months, 1year

    public function mount()
    {
        $this->resetPage();

        // Initialize analytics data on mount
        $this->analyticsData = [];

        // Check for URL parameters to auto-open QR modal for specific asset
        if (request('open_modal') && request('asset_type') && request('asset_id')) {
            $assetType = request('asset_type');
            $assetId = request('asset_id');

            // Find the asset and show preview
            $this->autoOpenPreview($assetType, $assetId);
        }
    }

    private function autoOpenPreview($assetType, $assetId)
    {
        // Convert asset type to match the format used in getFilteredAssets
        // class_basename() returns: Vehicle, MeetingRoom, ItAsset
        $assetTypeMap = [
            'vehicle' => 'vehicles',
            'meetingroom' => 'meeting_rooms',
            'meeting_room' => 'meeting_rooms',
            'itasset' => 'it_assets',
            'it_asset' => 'it_assets'
        ];

        $mappedType = $assetTypeMap[strtolower($assetType)] ?? 'all';

        // Set the filter to show only this asset type
        $this->selectedAssetType = $mappedType;

        // Create the asset ID in the format used by the component based on the original asset type
        $assetIdMap = [
            'vehicle' => 'vehicle_' . $assetId,
            'meetingroom' => 'meeting_room_' . $assetId,
            'meeting_room' => 'meeting_room_' . $assetId,
            'itasset' => 'it_asset_' . $assetId,
            'it_asset' => 'it_asset_' . $assetId
        ];

        $fullAssetId = $assetIdMap[strtolower($assetType)] ?? null;

        if ($fullAssetId) {
            // Try to find the asset in filtered results
            $assets = $this->getFilteredAssets();
            $foundAsset = $assets->firstWhere('id', $fullAssetId);

            if ($foundAsset) {
                $this->previewAsset = $foundAsset;
                $this->showPreviewModal = true;
            }
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedAssetType()
    {
        $this->resetPage();
        $this->selectedAssets = [];
        $this->selectAll = false;
    }

    public function updatedQrFilter()
    {
        $this->resetPage();
        $this->selectedAssets = [];
        $this->selectAll = false;
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

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedAssets = $this->getFilteredAssets()->pluck('id')->toArray();
        } else {
            $this->selectedAssets = [];
        }
    }

    public function toggleSelectAll()
    {
        $this->selectAll = !$this->selectAll;
        $this->updatedSelectAll();
    }

    public function getQrStatistics()
    {
        $stats = [];

        // Total assets by type
        $stats['total_assets'] = [
            'vehicles' => Vehicle::count(),
            'meeting_rooms' => MeetingRoom::count(),
            'it_assets' => ItAsset::count(),
        ];
        $stats['total_assets']['all'] = array_sum($stats['total_assets']);

        // Assets with QR codes
        $stats['with_qr'] = [
            'vehicles' => Vehicle::whereNotNull('qr_code_identifier')->count(),
            'meeting_rooms' => MeetingRoom::whereNotNull('qr_code_identifier')->count(),
            'it_assets' => ItAsset::whereNotNull('qr_code_identifier')->count(),
        ];
        $stats['with_qr']['all'] = array_sum($stats['with_qr']);

        // Calculate coverage percentage
        $stats['coverage'] = [
            'vehicles' => $stats['total_assets']['vehicles'] > 0 ? round(($stats['with_qr']['vehicles'] / $stats['total_assets']['vehicles']) * 100) : 0,
            'meeting_rooms' => $stats['total_assets']['meeting_rooms'] > 0 ? round(($stats['with_qr']['meeting_rooms'] / $stats['total_assets']['meeting_rooms']) * 100) : 0,
            'it_assets' => $stats['total_assets']['it_assets'] > 0 ? round(($stats['with_qr']['it_assets'] / $stats['total_assets']['it_assets']) * 100) : 0,
        ];
        $stats['coverage']['all'] = $stats['total_assets']['all'] > 0 ? round(($stats['with_qr']['all'] / $stats['total_assets']['all']) * 100) : 0;

        // Recent QR activity (from actual logs)
        $stats['recent_scans'] = $this->getRecentQrActivity();

        // Most scanned assets (from actual scan logs)
        $stats['most_scanned'] = $this->getMostScannedAssets();

        return $stats;
    }

    private function getRecentQrActivity()
    {
        // Get actual recent QR activity from logs
        return QrCodeLog::scans()
            ->where('created_at', '>=', now()->subDays(7))
            ->with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'asset_name' => $log->getAssetDisplayName(),
                    'asset_type' => class_basename($log->asset_type),
                    'user_name' => $log->user->name ?? 'Unknown User',
                    'scanned_at' => $log->scanned_at ?? $log->created_at,
                    'action' => $log->getFormattedAction()
                ];
            });
    }

    private function getMostScannedAssets()
    {
        // Get most scanned assets from actual logs
        $assetScanCounts = QrCodeLog::scans()
            ->where('created_at', '>=', now()->subDays(30))
            ->select('asset_type', 'asset_id', DB::raw('count(*) as scan_count'))
            ->groupBy('asset_type', 'asset_id')
            ->orderByDesc('scan_count')
            ->take(10)
            ->get();

        return $assetScanCounts->map(function ($item) {
            $asset = $this->getAssetByTypeAndId($item->asset_type, $item->asset_id);
            return [
                'asset_name' => $asset ? $asset->getAssetDisplayName() : 'Unknown Asset',
                'asset_type' => class_basename($item->asset_type),
                'scan_count' => $item->scan_count,
                'has_qr' => $asset && $asset->qr_code_identifier ? true : false
            ];
        });
    }

    private function getAssetFromBooking($booking)
    {
        return $this->getAssetByTypeAndId($booking->asset_type, $booking->asset_id);
    }

    private function getAssetByTypeAndId($assetType, $assetId)
    {
        return match ($assetType) {
            'App\Models\Vehicle' => Vehicle::find($assetId),
            'App\Models\MeetingRoom' => MeetingRoom::find($assetId),
            'App\Models\ItAsset' => ItAsset::find($assetId),
            default => null
        };
    }

    public function getFilteredAssets()
    {
        $assets = collect();

        // Get assets based on selected type
        if ($this->selectedAssetType === 'all' || $this->selectedAssetType === 'vehicles') {
            $vehicles = Vehicle::when($this->search, function($query) {
                $query->where('model', 'like', '%' . $this->search . '%')
                      ->orWhere('plate_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->qrFilter !== 'all', function($query) {
                if ($this->qrFilter === 'generated') {
                    $query->whereNotNull('qr_code_identifier');
                } else {
                    $query->whereNull('qr_code_identifier');
                }
            })
            ->get()
            ->map(function($vehicle) {
                return [
                    'id' => 'vehicle_' . $vehicle->id,
                    'real_id' => $vehicle->id,
                    'type' => 'vehicle',
                    'type_label' => 'Vehicle',
                    'name' => $vehicle->getAssetDisplayName(),
                    'details' => $vehicle->driver_name ? "Driver: {$vehicle->driver_name}" : 'No driver assigned',
                    'has_qr' => $vehicle->qr_code_identifier ? true : false,
                    'qr_identifier' => $vehicle->qr_code_identifier,
                    'last_booking' => $this->getLastBookingDate('App\Models\Vehicle', $vehicle->id),
                    'total_bookings' => Booking::where('asset_type', 'App\Models\Vehicle')->where('asset_id', $vehicle->id)->count(),
                    'qr_scan_count' => $this->getQrScanCount('App\Models\Vehicle', $vehicle->id),
                    'last_qr_scan' => $this->getLastQrScan('App\Models\Vehicle', $vehicle->id),
                    'model' => $vehicle
                ];
            });
            $assets = $assets->merge($vehicles);
        }

        if ($this->selectedAssetType === 'all' || $this->selectedAssetType === 'meeting_rooms') {
            $meetingRooms = MeetingRoom::when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('location', 'like', '%' . $this->search . '%');
            })
            ->when($this->qrFilter !== 'all', function($query) {
                if ($this->qrFilter === 'generated') {
                    $query->whereNotNull('qr_code_identifier');
                } else {
                    $query->whereNull('qr_code_identifier');
                }
            })
            ->get()
            ->map(function($room) {
                return [
                    'id' => 'meeting_room_' . $room->id,
                    'real_id' => $room->id,
                    'type' => 'meeting_room',
                    'type_label' => 'Meeting Room',
                    'name' => $room->getAssetDisplayName(),
                    'details' => $room->location . ($room->capacity ? " (Capacity: {$room->capacity})" : ''),
                    'has_qr' => $room->qr_code_identifier ? true : false,
                    'qr_identifier' => $room->qr_code_identifier,
                    'last_booking' => $this->getLastBookingDate('App\Models\MeetingRoom', $room->id),
                    'total_bookings' => Booking::where('asset_type', 'App\Models\MeetingRoom')->where('asset_id', $room->id)->count(),
                    'qr_scan_count' => $this->getQrScanCount('App\Models\MeetingRoom', $room->id),
                    'last_qr_scan' => $this->getLastQrScan('App\Models\MeetingRoom', $room->id),
                    'model' => $room
                ];
            });
            $assets = $assets->merge($meetingRooms);
        }

        if ($this->selectedAssetType === 'all' || $this->selectedAssetType === 'it_assets') {
            $itAssets = ItAsset::when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('asset_tag', 'like', '%' . $this->search . '%');
            })
            ->when($this->qrFilter !== 'all', function($query) {
                if ($this->qrFilter === 'generated') {
                    $query->whereNotNull('qr_code_identifier');
                } else {
                    $query->whereNull('qr_code_identifier');
                }
            })
            ->get()
            ->map(function($asset) {
                return [
                    'id' => 'it_asset_' . $asset->id,
                    'real_id' => $asset->id,
                    'type' => 'it_asset',
                    'type_label' => 'IT Asset',
                    'name' => $asset->getAssetDisplayName(),
                    'details' => $asset->location . ($asset->specs ? " - {$asset->specs}" : ''),
                    'has_qr' => $asset->qr_code_identifier ? true : false,
                    'qr_identifier' => $asset->qr_code_identifier,
                    'last_booking' => $this->getLastBookingDate('App\Models\ItAsset', $asset->id),
                    'total_bookings' => Booking::where('asset_type', 'App\Models\ItAsset')->where('asset_id', $asset->id)->count(),
                    'qr_scan_count' => $this->getQrScanCount('App\Models\ItAsset', $asset->id),
                    'last_qr_scan' => $this->getLastQrScan('App\Models\ItAsset', $asset->id),
                    'model' => $asset
                ];
            });
            $assets = $assets->merge($itAssets);
        }

        // Apply sorting
        if ($this->sortField === 'name') {
            $assets = $this->sortDirection === 'asc'
                ? $assets->sortBy('name')
                : $assets->sortByDesc('name');
        } elseif ($this->sortField === 'type') {
            $assets = $this->sortDirection === 'asc'
                ? $assets->sortBy('type_label')
                : $assets->sortByDesc('type_label');
        } elseif ($this->sortField === 'last_booking') {
            $assets = $this->sortDirection === 'asc'
                ? $assets->sortBy(function($asset) {
                    return $asset['last_booking'] ? $asset['last_booking']->timestamp : 0;
                })
                : $assets->sortByDesc(function($asset) {
                    return $asset['last_booking'] ? $asset['last_booking']->timestamp : 0;
                });
        } elseif ($this->sortField === 'total_bookings') {
            $assets = $this->sortDirection === 'asc'
                ? $assets->sortBy('total_bookings')
                : $assets->sortByDesc('total_bookings');
        } elseif ($this->sortField === 'qr_scan_count') {
            $assets = $this->sortDirection === 'asc'
                ? $assets->sortBy('qr_scan_count')
                : $assets->sortByDesc('qr_scan_count');
        } elseif ($this->sortField === 'last_qr_scan') {
            $assets = $this->sortDirection === 'asc'
                ? $assets->sortBy(function($asset) {
                    return $asset['last_qr_scan'] ? $asset['last_qr_scan']->timestamp : 0;
                })
                : $assets->sortByDesc(function($asset) {
                    return $asset['last_qr_scan'] ? $asset['last_qr_scan']->timestamp : 0;
                });
        } elseif ($this->sortField === 'qr_status') {
            $assets = $this->sortDirection === 'asc'
                ? $assets->sortBy('has_qr')
                : $assets->sortByDesc('has_qr');
        } else {
            $assets = $assets->sortBy('name');
        }

        return $assets;
    }

    private function getLastBookingDate($assetType, $assetId)
    {
        $lastBooking = Booking::where('asset_type', $assetType)
            ->where('asset_id', $assetId)
            ->latest('end_time')
            ->first();

        return $lastBooking ? $lastBooking->end_time : null;
    }

    private function getQrScanCount($assetType, $assetId)
    {
        return QrCodeLog::scans()
            ->where('asset_type', $assetType)
            ->where('asset_id', $assetId)
            ->count();
    }

    private function getLastQrScan($assetType, $assetId)
    {
        $lastScan = QrCodeLog::scans()
            ->where('asset_type', $assetType)
            ->where('asset_id', $assetId)
            ->latest('created_at')
            ->first();

        return $lastScan ? $lastScan->created_at : null;
    }

    public function showPreview($assetId)
    {
        $asset = $this->findAssetById($assetId);
        if ($asset) {
            $this->previewAsset = $asset;
            $this->showPreviewModal = true;
        }
    }

    public function generateQrCode($assetId)
    {
        $asset = $this->findAssetById($assetId);
        if ($asset) {
            $asset['model']->generateQrCodeIdentifier();
            session()->flash('success', 'QR code generated successfully!');
        }
    }

    public function regenerateQrCode($assetId)
    {
        $asset = $this->findAssetById($assetId);
        if ($asset) {
            $asset['model']->regenerateQrCodeIdentifier();
            session()->flash('success', 'QR code regenerated successfully!');
        }
    }

    public function bulkGenerateQr()
    {
        $count = 0;
        foreach ($this->selectedAssets as $assetId) {
            $asset = $this->findAssetById($assetId);
            if ($asset && !$asset['has_qr']) {
                $asset['model']->generateQrCodeIdentifier();
                $count++;
            }
        }

        $this->selectedAssets = [];
        $this->selectAll = false;
        session()->flash('success', "Generated QR codes for {$count} assets!");
    }

    public function bulkRegenerateQr()
    {
        $count = 0;
        foreach ($this->selectedAssets as $assetId) {
            $asset = $this->findAssetById($assetId);
            if ($asset) {
                $asset['model']->regenerateQrCodeIdentifier();
                $count++;
            }
        }

        $this->selectedAssets = [];
        $this->selectAll = false;
        session()->flash('success', "Regenerated QR codes for {$count} assets!");
    }

    public function generateMissingQr()
    {
        $assets = $this->getFilteredAssets()->where('has_qr', false);
        $count = 0;

        foreach ($assets as $asset) {
            $asset['model']->generateQrCodeIdentifier();
            $count++;
        }

        session()->flash('success', "Generated QR codes for {$count} assets!");
    }

    public function downloadQrCode($assetId)
    {
        $asset = $this->findAssetById($assetId);
        if ($asset && $asset['has_qr']) {
            $qrCodeSvg = $asset['model']->getQrCodeSvg(300);
            $filename = 'qr-code-' . strtolower($asset['type']) . '-' . $asset['real_id'] . '.svg';

            return response()->streamDownload(function () use ($qrCodeSvg) {
                echo $qrCodeSvg;
            }, $filename, [
                'Content-Type' => 'image/svg+xml',
            ]);
        }
    }

    public function downloadBulkQr()
    {
        // Create a ZIP file with all selected QR codes
        $zip = new \ZipArchive();
        $zipFileName = 'qr-codes-' . date('Y-m-d-H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($this->selectedAssets as $assetId) {
                $asset = $this->findAssetById($assetId);
                if ($asset && $asset['has_qr']) {
                    $qrCodeSvg = $asset['model']->getQrCodeSvg(300);
                    $filename = 'qr-code-' . strtolower($asset['type']) . '-' . $asset['real_id'] . '.svg';
                    $zip->addFromString($filename, $qrCodeSvg);
                }
            }
            $zip->close();

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        }

        session()->flash('error', 'Failed to create ZIP file');
    }

    public function downloadPrintTemplate()
    {
        $assets = [];
        foreach ($this->selectedAssets as $assetId) {
            $asset = $this->findAssetById($assetId);
            if ($asset && $asset['has_qr']) {
                $assets[] = $asset;
            }
        }

        if (empty($assets)) {
            session()->flash('error', 'No assets with QR codes selected');
            return;
        }

        $pdf = Pdf::loadView('qr-templates.' . $this->templateType, [
            'assets' => $assets,
            'qrSize' => $this->qrSize,
            'includeAssetInfo' => $this->includeAssetInfo,
            'includeLogo' => $this->includeLogo
        ]);

        // Configure PDF options for better image rendering
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'Helvetica',
            'isFontSubsettingEnabled' => false,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultMediaType' => 'print'
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'qr-codes-' . $this->templateType . '-' . date('Y-m-d') . '.pdf');
    }

    public function showAnalytics()
    {
        // Force load analytics data first
        $this->loadAnalyticsData();


        $this->showAnalyticsModal = true;

        // Dispatch the event with the analytics data directly
        $this->dispatch('analytics-modal-opened', $this->analyticsData);
    }

    public function updatedSelectedAnalyticsPeriod()
    {
        $this->loadAnalyticsData();
        $this->dispatch('analytics-modal-opened');
    }

    private function loadAnalyticsData()
    {
        try {
            $days = match($this->selectedAnalyticsPeriod) {
                '7days' => 7,
                '30days' => 30,
                '3months' => 90,
                '1year' => 365,
                default => 7
            };

            // Get real analytics data from QR code logs
            $this->analyticsData = QrCodeLog::getAnalytics($days);

            // Add computed data
            $this->analyticsData['scan_trend'] = $this->getScanTrendData($days);
            $this->analyticsData['asset_usage'] = $this->getAssetUsageData($days);
            $this->analyticsData['peak_hours'] = $this->getPeakHoursData($days);
            $this->analyticsData['device_breakdown'] = $this->getDeviceBreakdownData();
            $this->analyticsData['completion_rate'] = $this->getCompletionRateData($days);
            $this->analyticsData['user_engagement'] = $this->getUserEngagementData();

        } catch (\Exception $e) {
            // Fallback to empty data structure
            $this->analyticsData = [
                'total_scans' => 0,
                'unique_users' => 0,
                'successful_completions' => 0,
                'failed_attempts' => 0,
                'completion_rate' => 0,
                'scan_trend' => [],
                'asset_usage' => ['vehicles' => 0, 'meeting_rooms' => 0, 'it_assets' => 0],
                'peak_hours' => [],
                'device_breakdown' => ['mobile' => 0, 'desktop' => 0, 'tablet' => 0],
                'user_engagement' => ['first_time_users' => 0, 'returning_users' => 0, 'power_users' => 0, 'avg_scans_per_user' => 0],
                'top_assets' => [],
                'daily_scans' => []
            ];
        }
    }


    private function getScanTrendData($days)
    {
        // Get actual daily scan data from logs
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $scans = QrCodeLog::scans()
                ->whereDate('created_at', $date)
                ->count();
            $completions = QrCodeLog::byAction('booking_completed')
                ->whereDate('created_at', $date)
                ->count();
            $failures = QrCodeLog::byAction('scan_failed')
                ->whereDate('created_at', $date)
                ->count();
            $data[] = [
                'date' => $date->format('M j'),
                'scans' => $scans,
                'completions' => $completions,
                'failures' => $failures
            ];
        }
        return $data;
    }

    private function getAssetUsageData($days)
    {
        return [
            'vehicles' => QrCodeLog::scans()
                ->where('asset_type', 'App\Models\Vehicle')
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
            'meeting_rooms' => QrCodeLog::scans()
                ->where('asset_type', 'App\Models\MeetingRoom')
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
            'it_assets' => QrCodeLog::scans()
                ->where('asset_type', 'App\Models\ItAsset')
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
        ];
    }

    private function getCompletionRateData($days)
    {
        $totalScans = QrCodeLog::scans()
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
        $completedBookings = QrCodeLog::byAction('booking_completed')
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

        return $totalScans > 0 ? round(($completedBookings / $totalScans) * 100) : 0;
    }

    private function getPeakHoursData($days)
    {
        return QrCodeLog::scans()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->take(3)
            ->get()
            ->map(function($item) {
                return [
                    'hour' => str_pad($item->hour, 2, '0', STR_PAD_LEFT),
                    'count' => $item->count
                ];
            })
            ->toArray();
    }





    private function getDeviceBreakdownData()
    {
        // Get real device data from user agent analysis
        $logs = QrCodeLog::selectRaw('user_agent, COUNT(*) as count')
            ->whereNotNull('user_agent')
            ->groupBy('user_agent')
            ->get();

        $mobile = 0;
        $desktop = 0;
        $tablet = 0;

        foreach ($logs as $log) {
            $userAgent = strtolower($log->user_agent);
            if (str_contains($userAgent, 'mobile') || str_contains($userAgent, 'android') || str_contains($userAgent, 'iphone')) {
                $mobile += $log->count;
            } elseif (str_contains($userAgent, 'tablet') || str_contains($userAgent, 'ipad')) {
                $tablet += $log->count;
            } else {
                $desktop += $log->count;
            }
        }

        $total = $mobile + $desktop + $tablet;
        if ($total === 0) {
            return ['mobile' => 0, 'desktop' => 0, 'tablet' => 0];
        }

        return [
            'mobile' => round(($mobile / $total) * 100),
            'desktop' => round(($desktop / $total) * 100),
            'tablet' => round(($tablet / $total) * 100),
        ];
    }

    private function getUserEngagementData()
    {
        $userCounts = QrCodeLog::selectRaw('user_id, COUNT(*) as scan_count')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->get();

        $firstTimeUsers = $userCounts->where('scan_count', 1)->count();
        $powerUsers = $userCounts->where('scan_count', '>=', 10)->count();
        $returningUsers = $userCounts->where('scan_count', '>', 1)->where('scan_count', '<', 10)->count();
        $totalScans = $userCounts->sum('scan_count');
        $totalUsers = $userCounts->count();

        return [
            'first_time_users' => $firstTimeUsers,
            'returning_users' => $returningUsers,
            'power_users' => $powerUsers,
            'avg_scans_per_user' => $totalUsers > 0 ? round($totalScans / $totalUsers, 1) : 0,
        ];
    }

    private function findAssetById($assetId)
    {
        $assets = $this->getFilteredAssets();
        return $assets->firstWhere('id', $assetId);
    }

    public function closePreviewModal()
    {
        $this->showPreviewModal = false;
        $this->previewAsset = null;
    }

    public function closeBulkModal()
    {
        $this->showBulkModal = false;
    }

    public function closeAnalyticsModal()
    {
        $this->showAnalyticsModal = false;
    }

    public function closeTemplateModal()
    {
        $this->showTemplateModal = false;
    }

    public function getPaginatedAssets()
    {
        $assets = $this->getFilteredAssets();

        // Convert collection to paginated result
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $items = $assets->forPage($currentPage, $perPage);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $assets->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }

    public function render()
    {
        return view('livewire.admin.qr-code-management', [
            'assets' => $this->getPaginatedAssets(),
            'stats' => $this->getQrStatistics(),
        ]);
    }
}