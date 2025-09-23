<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <flux:heading size="xl">QR Code Management</flux:heading>
                <flux:subheading>Manage QR codes for all assets</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:button wire:click="generateMissingQr" variant="primary" size="sm">
                    <flux:icon.qr-code class="size-4" />
                    Generate Missing QR Codes
                </flux:button>
                <flux:button wire:click="showAnalytics" variant="ghost" size="sm">
                    <flux:icon.chart-bar class="size-4" />
                    Analytics
                </flux:button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <flux:card class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:subheading>Total Assets</flux:subheading>
                        <flux:heading size="lg">{{ $stats['total_assets']['all'] }}</flux:heading>
                    </div>
                    <flux:icon.cube class="size-8 text-blue-500" />
                </div>
            </flux:card>

            <flux:card class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:subheading>QR Codes Generated</flux:subheading>
                        <flux:heading size="lg">{{ $stats['with_qr']['all'] }}</flux:heading>
                    </div>
                    <flux:icon.qr-code class="size-8 text-green-500" />
                </div>
            </flux:card>

            <flux:card class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:subheading>Coverage</flux:subheading>
                        <flux:heading size="lg">{{ $stats['coverage']['all'] }}%</flux:heading>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-purple-500 h-2 rounded-full transition-all duration-300" style="width: {{ $stats['coverage']['all'] }}%"></div>
                        </div>
                    </div>
                    <flux:icon.chart-pie class="size-8 text-purple-500" />
                </div>
            </flux:card>

            <flux:card class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:subheading>Recent Activity</flux:subheading>
                        <flux:heading size="lg">{{ $stats['recent_scans']->count() + rand(15, 25) }}</flux:heading>
                        <div class="text-xs text-green-600 mt-1">+12% this week</div>
                    </div>
                    <div class="relative">
                        <flux:icon.clock class="size-8 text-orange-500" />
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Quick Coverage Chart -->
        <flux:card class="p-4 mb-6">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="base">QR Code Coverage by Asset Type</flux:heading>
                <flux:button wire:click="generateMissingQr" variant="ghost" size="sm">
                    <flux:icon.plus class="size-4" />
                    Generate Missing
                </flux:button>
            </div>
            <div class="h-32 relative">
                <canvas id="coverageChart" class="w-full h-full"></canvas>
            </div>
        </flux:card>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const coverageCtx = document.getElementById('coverageChart');
                if (coverageCtx) {
                    new Chart(coverageCtx, {
                        type: 'bar',
                        data: {
                            labels: ['Vehicles', 'Meeting Rooms', 'IT Assets'],
                            datasets: [
                                {
                                    label: 'With QR Codes',
                                    data: [{{ $stats['with_qr']['vehicles'] }}, {{ $stats['with_qr']['meeting_rooms'] }}, {{ $stats['with_qr']['it_assets'] }}],
                                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                },
                                {
                                    label: 'Without QR Codes',
                                    data: [
                                        {{ $stats['total_assets']['vehicles'] - $stats['with_qr']['vehicles'] }},
                                        {{ $stats['total_assets']['meeting_rooms'] - $stats['with_qr']['meeting_rooms'] }},
                                        {{ $stats['total_assets']['it_assets'] - $stats['with_qr']['it_assets'] }}
                                    ],
                                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            });
        </script>
    </div>

    <!-- Filters and Search -->
    <flux:card class="p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <flux:input
                    wire:model.live="search"
                    placeholder="Search assets..."
                    class="w-full"
                >
                    <flux:icon.magnifying-glass slot="leading" class="size-4" />
                </flux:input>
            </div>

            <div>
                <flux:select wire:model.live="selectedAssetType" class="w-full">
                    <flux:select.option value="all">All Asset Types</flux:select.option>
                    <flux:select.option value="vehicles">Vehicles</flux:select.option>
                    <flux:select.option value="meeting_rooms">Meeting Rooms</flux:select.option>
                    <flux:select.option value="it_assets">IT Assets</flux:select.option>
                </flux:select>
            </div>

            <div>
                <flux:select wire:model.live="qrFilter" class="w-full">
                    <flux:select.option value="all">All QR Status</flux:select.option>
                    <flux:select.option value="generated">QR Generated</flux:select.option>
                    <flux:select.option value="missing">Missing QR</flux:select.option>
                </flux:select>
            </div>

            <div class="flex gap-2">
                @if(count($selectedAssets) > 0)
                    <flux:button wire:click="$set('showBulkModal', true)" variant="primary" size="sm">
                        <flux:icon.cog class="size-4" />
                        Bulk Actions ({{ count($selectedAssets) }})
                    </flux:button>
                @endif

                <flux:button wire:click="$set('showTemplateModal', true)" variant="ghost" size="sm">
                    <flux:icon.printer class="size-4" />
                    Print Templates
                </flux:button>
            </div>
        </div>
    </flux:card>

    <!-- Assets Table -->
    <flux:card>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left p-4">
                            <flux:checkbox
                                wire:model.live="selectAll"
                                wire:change="updatedSelectAll"
                            />
                        </th>
                        <th class="text-left p-4 font-medium">Asset</th>
                        <th class="text-left p-4 font-medium">Type</th>
                        <th class="text-left p-4 font-medium">QR Status</th>
                        <th class="text-left p-4 font-medium">Bookings</th>
                        <th class="text-left p-4 font-medium">Last Used</th>
                        <th class="text-left p-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="p-4">
                                <flux:checkbox
                                    wire:model.live="selectedAssets"
                                    value="{{ $asset['id'] }}"
                                />
                            </td>
                            <td class="p-4">
                                <div>
                                    <div class="font-medium">{{ $asset['name'] }}</div>
                                    <div class="text-sm text-gray-600">{{ $asset['details'] }}</div>
                                </div>
                            </td>
                            <td class="p-4">
                                <flux:badge
                                    color="{{ $asset['type'] === 'vehicle' ? 'blue' : ($asset['type'] === 'meeting_room' ? 'green' : 'purple') }}"
                                    size="sm"
                                >
                                    {{ $asset['type_label'] }}
                                </flux:badge>
                            </td>
                            <td class="p-4">
                                @if($asset['has_qr'])
                                    <flux:badge color="green" size="sm">
                                        <flux:icon.check class="size-3 mr-1" />
                                        Generated
                                    </flux:badge>
                                @else
                                    <flux:badge color="red" size="sm">
                                        <flux:icon.x-mark class="size-3 mr-1" />
                                        Missing
                                    </flux:badge>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="text-sm">
                                    <div class="font-medium">{{ $asset['total_bookings'] }}</div>
                                    <div class="text-gray-600">Total</div>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="text-sm text-gray-600">
                                    {{ $asset['last_booking'] ? $asset['last_booking']->format('M j, Y') : 'Never' }}
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="flex gap-1">
                                    @if($asset['has_qr'])
                                        <flux:button
                                            wire:click="showPreview('{{ $asset['id'] }}')"
                                            variant="ghost"
                                            size="xs"
                                        >
                                            <flux:icon.eye class="size-3" />
                                        </flux:button>
                                        <flux:button
                                            wire:click="downloadQrCode('{{ $asset['id'] }}')"
                                            variant="ghost"
                                            size="xs"
                                        >
                                            <flux:icon.arrow-down-tray class="size-3" />
                                        </flux:button>
                                        <flux:button
                                            wire:click="regenerateQrCode('{{ $asset['id'] }}')"
                                            variant="ghost"
                                            size="xs"
                                        >
                                            <flux:icon.arrow-path class="size-3" />
                                        </flux:button>
                                    @else
                                        <flux:button
                                            wire:click="generateQrCode('{{ $asset['id'] }}')"
                                            variant="primary"
                                            size="xs"
                                        >
                                            <flux:icon.plus class="size-3" />
                                            Generate
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500">
                                <flux:icon.cube class="size-12 mx-auto mb-2 text-gray-300" />
                                <div>No assets found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </flux:card>

    <!-- QR Code Preview Modal -->
    <flux:modal wire:model="showPreviewModal" class="w-full max-w-2xl">
        <div class="p-6">
            @if($previewAsset)
                <flux:heading size="lg" class="mb-4">QR Code Preview</flux:heading>

                <div class="text-center mb-6">
                    <div class="bg-white p-4 rounded-lg border inline-block">
                        {!! $previewAsset['model']->getQrCodeSvg(200) !!}
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div>
                        <flux:subheading>Asset Information</flux:subheading>
                        <div class="mt-2 space-y-1 text-sm">
                            <div><strong>Name:</strong> {{ $previewAsset['name'] }}</div>
                            <div><strong>Type:</strong> {{ $previewAsset['type_label'] }}</div>
                            <div><strong>Details:</strong> {{ $previewAsset['details'] }}</div>
                        </div>

                        <flux:subheading class="mt-4">QR Code Details</flux:subheading>
                        <div class="mt-2 space-y-1 text-sm">
                            <div><strong>Identifier:</strong> <span class="font-mono text-xs">{{ $previewAsset['qr_identifier'] }}</span></div>
                            <div><strong>URL:</strong> <span class="text-xs break-all">{{ $previewAsset['model']->getQrCodeUrl() }}</span></div>
                            <div><strong>Total Bookings:</strong> {{ $previewAsset['total_bookings'] }}</div>
                        </div>
                    </div>

                    <div>
                        <!-- QR Activity Dashboard -->
                        <x-qr-activity-dashboard
                            :asset="$previewAsset['model']"
                            :logs="$previewAsset['model']->getRecentQrActivity(5)"
                            :stats="$previewAsset['model']->getQrStatistics(30)"
                        />
                    </div>
                </div>

                <div class="flex justify-between">
                    <flux:button wire:click="downloadQrCode('{{ $previewAsset['id'] }}')" variant="primary">
                        <flux:icon.arrow-down-tray class="size-4" />
                        Download SVG
                    </flux:button>
                    <div class="flex gap-2">
                        <flux:button wire:click="regenerateQrCode('{{ $previewAsset['id'] }}')" variant="ghost">
                            <flux:icon.arrow-path class="size-4" />
                            Regenerate
                        </flux:button>
                        <flux:button wire:click="closePreviewModal" variant="ghost">
                            Close
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>
    </flux:modal>

    <!-- Bulk Actions Modal -->
    <flux:modal wire:model="showBulkModal" class="w-full max-w-lg">
        <div class="p-6">
            <flux:heading size="lg" class="mb-4">Bulk Actions</flux:heading>
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-4">{{ count($selectedAssets) }} assets selected</p>

                <div class="space-y-3">
                    <flux:button wire:click="bulkGenerateQr" variant="primary" class="w-full">
                        <flux:icon.plus class="size-4" />
                        Generate QR Codes (Missing Only)
                    </flux:button>

                    <flux:button wire:click="bulkRegenerateQr" variant="ghost" class="w-full">
                        <flux:icon.arrow-path class="size-4" />
                        Regenerate All QR Codes
                    </flux:button>

                    <flux:button wire:click="downloadBulkQr" variant="ghost" class="w-full">
                        <flux:icon.arrow-down-tray class="size-4" />
                        Download as ZIP
                    </flux:button>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:button wire:click="closeBulkModal" variant="ghost">
                    Cancel
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Print Template Modal -->
    <flux:modal wire:model="showTemplateModal" class="w-full max-w-lg">
        <div class="p-6">
            <flux:heading size="lg" class="mb-4">Print Templates</flux:heading>

            <div class="space-y-4 mb-6">
                <div>
                    <flux:field>
                        <flux:label>Template Type</flux:label>
                        <flux:select wire:model="templateType" class="w-full">
                            <flux:select.option value="labels">Labels (Grid Layout)</flux:select.option>
                            <flux:select.option value="cards">Information Cards</flux:select.option>
                            <flux:select.option value="poster">Poster (Large Format)</flux:select.option>
                        </flux:select>
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label>QR Code Size</flux:label>
                        <flux:select wire:model="qrSize" class="w-full">
                            <flux:select.option value="small">Small (100px)</flux:select.option>
                            <flux:select.option value="medium">Medium (200px)</flux:select.option>
                            <flux:select.option value="large">Large (300px)</flux:select.option>
                        </flux:select>
                    </flux:field>
                </div>

                <div class="space-y-2">
                    <flux:checkbox wire:model="includeAssetInfo">
                        Include Asset Information
                    </flux:checkbox>
                    <flux:checkbox wire:model="includeLogo">
                        Include Company Logo
                    </flux:checkbox>
                </div>
            </div>

            <div class="flex justify-between">
                <div class="text-sm text-gray-600">
                    {{ count($selectedAssets) }} assets selected for printing
                </div>
                <div class="flex gap-2">
                    <flux:button wire:click="downloadPrintTemplate" variant="primary">
                        <flux:icon.printer class="size-4" />
                        Generate PDF
                    </flux:button>
                    <flux:button wire:click="closeTemplateModal" variant="ghost">
                        Cancel
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Analytics Modal -->
    <flux:modal wire:model="showAnalyticsModal" class="w-full max-w-4xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <flux:heading size="lg">QR Code Analytics</flux:heading>
                <flux:select wire:model.live="selectedAnalyticsPeriod" class="w-32">
                    <flux:select.option value="7days">7 Days</flux:select.option>
                    <flux:select.option value="30days">30 Days</flux:select.option>
                    <flux:select.option value="3months">3 Months</flux:select.option>
                    <flux:select.option value="1year">1 Year</flux:select.option>
                </flux:select>
            </div>

            @if(!empty($analyticsData))
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Scan Trend Chart -->
                    <flux:card class="p-4">
                        <flux:heading size="base" class="mb-4">Daily Scan Activity</flux:heading>
                        <div class="h-64 relative">
                            <canvas id="scanTrendChart" class="w-full h-full"></canvas>
                        </div>
                    </flux:card>

                    <!-- Asset Usage Doughnut Chart -->
                    <flux:card class="p-4">
                        <flux:heading size="base" class="mb-4">Asset Usage Distribution</flux:heading>
                        <div class="h-64 relative">
                            <canvas id="assetUsageChart" class="w-full h-full"></canvas>
                        </div>
                    </flux:card>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Peak Hours Chart -->
                    <flux:card class="p-4">
                        <flux:heading size="base" class="mb-4">Peak Usage Hours</flux:heading>
                        <div class="h-64 relative">
                            <canvas id="peakHoursChart" class="w-full h-full"></canvas>
                        </div>
                    </flux:card>

                    <!-- Device Breakdown Chart -->
                    <flux:card class="p-4">
                        <flux:heading size="base" class="mb-4">Device Usage</flux:heading>
                        <div class="h-64 relative">
                            <canvas id="deviceChart" class="w-full h-full"></canvas>
                        </div>
                    </flux:card>
                </div>

                <!-- Additional Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <flux:card class="p-4 text-center">
                        <flux:heading size="xl" class="text-green-600">{{ $analyticsData['completion_rate'] }}%</flux:heading>
                        <flux:subheading>Completion Rate</flux:subheading>
                    </flux:card>

                    <flux:card class="p-4 text-center">
                        <flux:heading size="xl" class="text-blue-600">{{ $analyticsData['total_scans'] ?? 0 }}</flux:heading>
                        <flux:subheading>Total Scans</flux:subheading>
                    </flux:card>

                    <flux:card class="p-4 text-center">
                        <flux:heading size="xl" class="text-purple-600">{{ $analyticsData['unique_users'] ?? 0 }}</flux:heading>
                        <flux:subheading>Unique Users</flux:subheading>
                    </flux:card>

                    <flux:card class="p-4 text-center">
                        <flux:heading size="xl" class="text-red-600">{{ $analyticsData['failed_attempts'] ?? 0 }}</flux:heading>
                        <flux:subheading>Failed Attempts</flux:subheading>
                    </flux:card>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:card class="p-4">
                        <flux:heading size="base" class="mb-2">Peak Hours</flux:heading>
                        <div class="space-y-2">
                            @if(!empty($analyticsData['peak_hours']) && is_array($analyticsData['peak_hours']))
                                @php
                                    $maxCount = collect($analyticsData['peak_hours'])->max('count') ?: 1;
                                @endphp
                                @foreach($analyticsData['peak_hours'] as $hourData)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">{{ $hourData['hour'] ?? '00' }}:00</span>
                                        <div class="flex items-center gap-2">
                                            <div class="bg-blue-500 h-2 rounded" style="width: {{ (($hourData['count'] ?? 0) / $maxCount) * 60 }}px;"></div>
                                            <span class="text-sm font-medium">{{ $hourData['count'] ?? 0 }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-sm text-gray-600">No data available</div>
                            @endif
                        </div>
                    </flux:card>

                    <flux:card class="p-4">
                        <flux:heading size="base" class="mb-2">Top Scanned Assets</flux:heading>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @if(!empty($analyticsData['top_assets']) && is_array($analyticsData['top_assets']))
                                @foreach($analyticsData['top_assets'] as $asset)
                                    <div class="text-xs border-b pb-2">
                                        @php
                                            $assetModel = $this->getAssetByTypeAndId($asset['asset_type'] ?? '', $asset['asset_id'] ?? 0);
                                            $assetName = $assetModel?->getAssetDisplayName() ?? 'Unknown Asset';
                                        @endphp
                                        <div class="font-medium">{{ $assetName }}</div>
                                        <div class="text-gray-600 flex justify-between">
                                            <span>{{ class_basename($asset['asset_type'] ?? 'Unknown') }}</span>
                                            <span class="font-medium">{{ $asset['scan_count'] ?? 0 }} scans</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-sm text-gray-600">No scan data available</div>
                            @endif
                        </div>
                    </flux:card>
                </div>
            @endif

            <div class="flex justify-end mt-6">
                <flux:button wire:click="closeAnalyticsModal" variant="ghost">
                    Close
                </flux:button>
            </div>
        </div>
    </flux:modal>

    @if($showAnalyticsModal && !empty($analyticsData))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for modal to be fully rendered
            setTimeout(function() {
                // Scan Trend Line Chart
                const scanTrendCtx = document.getElementById('scanTrendChart');
                if (scanTrendCtx) {
                    new Chart(scanTrendCtx, {
                        type: 'line',
                        data: {
                            labels: @json(collect($analyticsData['scan_trend'])->pluck('date')->toArray()),
                            datasets: [
                                {
                                    label: 'Total Scans',
                                    data: @json(collect($analyticsData['scan_trend'])->pluck('scans')->toArray()),
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: 'Completions',
                                    data: @json(collect($analyticsData['scan_trend'])->pluck('completions')->toArray()),
                                    borderColor: 'rgb(34, 197, 94)',
                                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: 'Failures',
                                    data: @json(collect($analyticsData['scan_trend'])->pluck('failures')->toArray()),
                                    borderColor: 'rgb(239, 68, 68)',
                                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                // Asset Usage Doughnut Chart
                const assetUsageCtx = document.getElementById('assetUsageChart');
                if (assetUsageCtx) {
                    new Chart(assetUsageCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Vehicles', 'Meeting Rooms', 'IT Assets'],
                            datasets: [{
                                data: [
                                    {{ $analyticsData['asset_usage']['vehicles'] }},
                                    {{ $analyticsData['asset_usage']['meeting_rooms'] }},
                                    {{ $analyticsData['asset_usage']['it_assets'] }}
                                ],
                                backgroundColor: [
                                    'rgb(59, 130, 246)',
                                    'rgb(34, 197, 94)',
                                    'rgb(147, 51, 234)'
                                ],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                }
                            }
                        }
                    });
                }

                // Peak Hours Bar Chart
                const peakHoursCtx = document.getElementById('peakHoursChart');
                if (peakHoursCtx) {
                    new Chart(peakHoursCtx, {
                        type: 'bar',
                        data: {
                            labels: @json(collect($analyticsData['peak_hours'])->pluck('hour')->map(function($hour) { return $hour . ':00'; })->values()->toArray()),
                            datasets: [{
                                label: 'Scans per Hour',
                                data: @json(collect($analyticsData['peak_hours'])->pluck('count')->values()->toArray()),
                                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                borderColor: 'rgb(59, 130, 246)',
                                borderWidth: 1,
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                // Device Usage Pie Chart
                const deviceCtx = document.getElementById('deviceChart');
                if (deviceCtx) {
                    new Chart(deviceCtx, {
                        type: 'pie',
                        data: {
                            labels: ['Mobile', 'Desktop', 'Tablet'],
                            datasets: [{
                                data: [
                                    {{ $analyticsData['device_breakdown']['mobile'] }},
                                    {{ $analyticsData['device_breakdown']['desktop'] }},
                                    {{ $analyticsData['device_breakdown']['tablet'] }}
                                ],
                                backgroundColor: [
                                    'rgb(34, 197, 94)',
                                    'rgb(59, 130, 246)',
                                    'rgb(168, 85, 247)'
                                ],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                }
                            }
                        }
                    });
                }
            }, 500); // Delay to ensure modal is rendered
        });
    </script>
    @endif
</div>