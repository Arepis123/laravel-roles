<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">QR Code Management</h1>
                <p class="text-gray-600 mt-1 dark:text-gray-400">Manage QR codes for all assets</p>
            </div>
            <div>
                <flux:button wire:click="showAnalytics" variant="primary">
                    <flux:icon.chart-bar class="size-4 inline" />
                    Analytics
                </flux:button>
            </div>
        </div>
        <flux:separator variant="subtle" class="my-4" />

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg hidden sm:block">
                        <flux:icon.cube class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Assets</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_assets']['all'] }}</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg hidden sm:block">
                        <flux:icon.qr-code class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">QR Codes Generated</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['with_qr']['all'] }}</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg hidden sm:block">
                        <flux:icon.chart-pie class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Coverage</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['coverage']['all'] }}%</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/50 rounded-lg hidden sm:block">
                        <flux:icon.clock class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">
                            Recent Activity
                        </flux:heading>

                        <div class="flex items-center gap-1">
                            <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ $stats['recent_scans']->count() }}
                            </flux:text>
                            <span class="text-gray-500 dark:text-gray-400">•</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Last 7 days</span>
                        </div>
                    </div>
                </div>
            </flux:card>
        </div>

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
                    <flux:button wire:click="$set('showBulkModal', true)" variant="primary">
                        Bulk Actions ({{ count($selectedAssets) }})
                    </flux:button>
                @endif

                <flux:button wire:click="$set('showTemplateModal', true)" variant="primary">
                    Print Templates
                </flux:button>
            </div>
        </div>
    </flux:card>

    <!-- Assets Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <flux:checkbox
                                wire:model.live="selectAll"
                            />
                        </th>
                        <th wire:click="sortBy('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Asset</span>
                                @if($sortField === 'name')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('type')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Type</span>
                                @if($sortField === 'type')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('qr_status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>QR Status</span>
                                @if($sortField === 'qr_status')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('qr_scan_count')" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center justify-center space-x-1">
                                <span>QR Scans</span>
                                @if($sortField === 'qr_scan_count')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('last_booking')"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300
                                uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center justify-center space-x-1">
                                <span>Last Used</span>
                                @if($sortField === 'last_booking')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:checkbox
                                    wire:model.live="selectedAssets"
                                    value="{{ $asset['id'] }}"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white mt-1 block">{{ $asset['name'] }}</span>
                                    <span class="text-sm text-gray-500 font-normal">{{ $asset['details'] }}</span>                                    
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge
                                    color="{{ $asset['type'] === 'vehicle' ? 'blue' : ($asset['type'] === 'meeting_room' ? 'green' : 'purple') }}"
                                    size="sm"
                                >
                                    {{ $asset['type_label'] }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($asset['has_qr'])
                                    <flux:badge size="sm">
                                        <flux:icon.check class="size-3 mr-1" />
                                        Generated
                                    </flux:badge>
                                @else
                                    <flux:badge size="sm">
                                        <flux:icon.x-mark class="size-3 mr-1" />
                                        Missing
                                    </flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <div class="font-semibold">{{ $asset['qr_scan_count'] }}</div>
                                    <div class="text-gray-500 dark:text-gray-400">
                                        @if($asset['last_qr_scan'])
                                            {{ $asset['last_qr_scan']->diffForHumans() }}
                                        @else
                                            Never scanned
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">
                                {{ $asset['last_booking'] ? $asset['last_booking']->format('M j, Y') : 'Never' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex space-x-2 justify-center">
                                    @if($asset['has_qr'])
                                        <flux:tooltip content="View details">
                                            <flux:button size="xs" wire:click="showPreview('{{ $asset['id'] }}')" variant="ghost">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </flux:button>  
                                        </flux:tooltip>
                                        <flux:tooltip content="Download SVG">                                  
                                            <flux:button size="xs" wire:click="downloadQrCode('{{ $asset['id'] }}')" variant="ghost">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </flux:button> 
                                        </flux:tooltip>
                                        <flux:tooltip content="Regenerate code">                                        
                                            <flux:button size="xs" wire:click="regenerateQrCode('{{ $asset['id'] }}')" variant="ghost">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                            </flux:button> 
                                        </flux:tooltip>                                       
                                    @else
                                        <button wire:click="generateQrCode('{{ $asset['id'] }}')"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Generate
                                        </button>
                                        <flux:button size="xs" wire:click="generateQrCode('{{ $asset['id'] }}')" variant="ghost">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">No assets found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination inside table -->
            <div class="bg-white dark:bg-zinc-900 px-6 py-3 border-t border-gray-200 dark:border-zinc-700">
                {{ $assets->links() }}
            </div>
        </div>
    </div>

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

                <div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-6">
                    <div>
                        <flux:subheading>Asset Information</flux:subheading>
                        <div class="mt-2 space-y-1 text-sm">
                            <div><strong>Name:</strong> {{ $previewAsset['model']->model }}</div>
                            <div><strong>Type:</strong> {{ $previewAsset['type_label'] }}</div>
                            @if($previewAsset['type_label'] == 'Vehicle') 
                                <div><strong>Plate Number:</strong> {{ $previewAsset['model']->plate_number }}</div>
                            @else
                                <div><strong>Details:</strong> {{ $previewAsset['details'] }}</div>
                            @endif
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
                        Download SVG
                    </flux:button>
                    <div class="flex gap-2">
                        <flux:button wire:click="regenerateQrCode('{{ $previewAsset['id'] }}')" variant="ghost">
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
                        Generate QR Codes (Missing Only)
                    </flux:button>

                    <flux:button wire:click="bulkRegenerateQr" variant="primary" class="w-full">
                        Regenerate All QR Codes
                    </flux:button>

                    <flux:button wire:click="downloadBulkQr" variant="primary" class="w-full">
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
                    <flux:field variant="inline">
                        <flux:checkbox wire:model="terms" wire:model="includeAssetInfo"/>
                        <flux:label>Include Asset Information</flux:label>
                        <flux:error name="Information" />
                    </flux:field>
                    <flux:field variant="inline">
                        <flux:checkbox wire:model="terms" wire:model="includeLogo"/>
                        <flux:label>Include Company Logo</flux:label>
                        <flux:error name="Logo" />
                    </flux:field>                 
                </div>
            </div>

            <div></div>

            <div class="">
                <div class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                    {{ count($selectedAssets) }} assets selected for printing
                </div>
                <div class="flex gap-2 justify-end">
                    <flux:button wire:click="downloadPrintTemplate" variant="primary">
                        {{-- <flux:icon.printer class="size-4" /> --}}
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

    <script>

        // Function to initialize charts
        function initializeCharts() {
            if (typeof Chart === 'undefined') {
                return;
            }

            // Destroy existing charts if they exist
            if (Chart.instances && Chart.instances.length > 0) {
                Chart.instances.forEach(chart => chart.destroy());
            }

            // Scan Trend Line Chart
            const scanTrendCtx = document.getElementById('scanTrendChart');
            if (scanTrendCtx) {

                    new Chart(scanTrendCtx, {
                        type: 'line',
                        data: {
                            labels: @json(collect($analyticsData['scan_trend'] ?? [])->pluck('date')->toArray()),
                            datasets: [
                                {
                                    label: 'Total Scans',
                                    data: @json(collect($analyticsData['scan_trend'] ?? [])->pluck('scans')->toArray()),
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: 'Completions',
                                    data: @json(collect($analyticsData['scan_trend'] ?? [])->pluck('completions')->toArray()),
                                    borderColor: 'rgb(34, 197, 94)',
                                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: 'Failures',
                                    data: @json(collect($analyticsData['scan_trend'] ?? [])->pluck('failures')->toArray()),
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
                console.log('Asset usage canvas found:', !!assetUsageCtx);
                if (assetUsageCtx) {
                    console.log('Creating asset usage chart...');
                    new Chart(assetUsageCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Vehicles', 'Meeting Rooms', 'IT Assets'],
                            datasets: [{
                                data: [
                                    {{ $analyticsData['asset_usage']['vehicles'] ?? 0 }},
                                    {{ $analyticsData['asset_usage']['meeting_rooms'] ?? 0 }},
                                    {{ $analyticsData['asset_usage']['it_assets'] ?? 0 }}
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
                console.log('Peak hours canvas found:', !!peakHoursCtx);
                if (peakHoursCtx) {
                    console.log('Creating peak hours chart...');
                    new Chart(peakHoursCtx, {
                        type: 'bar',
                        data: {
                            labels: @json(collect($analyticsData['peak_hours'] ?? [])->pluck('hour')->map(function($hour) { return $hour . ':00'; })->values()->toArray()),
                            datasets: [{
                                label: 'Scans per Hour',
                                data: @json(collect($analyticsData['peak_hours'] ?? [])->pluck('count')->values()->toArray()),
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
                console.log('Device chart canvas found:', !!deviceCtx);
                if (deviceCtx) {
                    console.log('Creating device chart...');
                    new Chart(deviceCtx, {
                        type: 'pie',
                        data: {
                            labels: ['Mobile', 'Desktop', 'Tablet'],
                            datasets: [{
                                data: [
                                    {{ $analyticsData['device_breakdown']['mobile'] ?? 0 }},
                                    {{ $analyticsData['device_breakdown']['desktop'] ?? 0 }},
                                    {{ $analyticsData['device_breakdown']['tablet'] ?? 0 }}
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
        }

        // Initialize charts with data passed from Livewire event
        function initializeChartsWithData(analyticsData) {
            if (typeof Chart === 'undefined') {
                return;
            }

            // Destroy existing charts if they exist
            if (Chart.instances && Chart.instances.length > 0) {
                Chart.instances.forEach(chart => chart.destroy());
            }

            // Scan Trend Line Chart
            const scanTrendCtx = document.getElementById('scanTrendChart');
            if (scanTrendCtx && analyticsData?.scan_trend) {
                const labels = analyticsData.scan_trend.map(item => item.date);
                const scans = analyticsData.scan_trend.map(item => item.scans);
                const completions = analyticsData.scan_trend.map(item => item.completions);
                const failures = analyticsData.scan_trend.map(item => item.failures);

                new Chart(scanTrendCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Total Scans',
                                data: scans,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Completions',
                                data: completions,
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Failures',
                                data: failures,
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
            if (assetUsageCtx && analyticsData?.asset_usage) {

                new Chart(assetUsageCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Vehicles', 'Meeting Rooms', 'IT Assets'],
                        datasets: [{
                            data: [
                                analyticsData.asset_usage.vehicles || 0,
                                analyticsData.asset_usage.meeting_rooms || 0,
                                analyticsData.asset_usage.it_assets || 0
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
            if (peakHoursCtx && analyticsData?.peak_hours) {

                const hourLabels = analyticsData.peak_hours.map(item => item.hour + ':00');
                const hourCounts = analyticsData.peak_hours.map(item => item.count);

                new Chart(peakHoursCtx, {
                    type: 'bar',
                    data: {
                        labels: hourLabels,
                        datasets: [{
                            label: 'Scans per Hour',
                            data: hourCounts,
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
            if (deviceCtx && analyticsData?.device_breakdown) {

                new Chart(deviceCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Mobile', 'Desktop', 'Tablet'],
                        datasets: [{
                            data: [
                                analyticsData.device_breakdown.mobile || 0,
                                analyticsData.device_breakdown.desktop || 0,
                                analyticsData.device_breakdown.tablet || 0
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
        }

        // Initialize charts when modal is opened
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Livewire !== 'undefined') {
                Livewire.on('analytics-modal-opened', (...args) => {
                    let analyticsData = args[0];

                    // If it's still an array, get the first element
                    if (Array.isArray(analyticsData)) {
                        analyticsData = analyticsData[0];
                    }

                    // Wait for Livewire to update the DOM
                    setTimeout(() => {
                        initializeChartsWithData(analyticsData);
                    }, 300);
                });
            }
        });

    </script>
</div>