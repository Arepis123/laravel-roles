<div class="relative mb-6 w-full">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Vehicle Odometer Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Track and manage vehicle odometer readings</p>
            </div>
            <flux:modal.trigger name="odometer-form">
                <div class="flex justify-between items-center">
                    <flux:button variant="primary" wire:click="showAddForm" icon="plus" class="w-full sm:w-auto">
                        Add Reading
                    </flux:button>
                </div>                
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 lucide lucide-land-plot-icon lucide-land-plot" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 14 4-4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.34 19a10 10 0 1 1 17.32 0"/>
                    </svg>                         
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Distance</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_distance']) }} km</flux:text>
                </div>
            </div>
        </flux:card>  
        
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Avg Distance/Trip</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['avg_distance_per_trip'], 1) }} km</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>  
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Readings</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_readings']) }}</flux:text>
                </div>
            </div>
        </flux:card>   

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400 lucide lucide-car-front-icon lucide-car-front" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 8-2 2-1.5-3.7A2 2 0 0 0 15.646 5H8.4a2 2 0 0 0-1.903 1.257L5 10 3 8"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 14h.01"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14h.01"/>
                        <rect stroke-linecap="round" stroke-linejoin="round" stroke-width="2" width="18" height="8" x="3" y="10" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 18v2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 18v2"/>
                    </svg>                     
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Vehicles Tracked</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['vehicles_tracked'] }}</flux:text>
                </div>
            </div>
        </flux:card>
    </div>    

    <!-- Filters -->
    <div class="mb-6 mx-2">
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filters & Export
                    </span>
                </flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 pt-4 mx-3">
                        <flux:field>
                            <flux:label>Vehicle</flux:label>
                            <flux:select variant="listbox" wire:model.live="filterVehicle" placeholder="All Vehicles">
                                @foreach($vehicles as $vehicle)
                                    <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->model }} ({{ $vehicle->plate_number }})</flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>Reading Type</flux:label>
                            <flux:select variant="listbox" wire:model.live="filterReadingType" placeholder="All Types">
                                <flux:select.option value="start">Start</flux:select.option>
                                <flux:select.option value="end">End</flux:select.option>
                                <flux:select.option value="manual">Manual</flux:select.option>
                                <flux:select.option value="service">Service</flux:select.option>
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>From Date</flux:label>
                            <flux:date-picker wire:model.live="filterDateFrom" with-today/>
                        </flux:field>

                        <flux:field>
                            <flux:label>To Date</flux:label>
                            <flux:date-picker wire:model.live="filterDateTo" with-today/>
                        </flux:field>
                    </div>

                    <!-- Export Buttons -->
                    <div class="flex gap-3 pt-4 mx-3">
                        <flux:button variant="filled" size="sm" wire:click="exportOdometerData('excel')" icon="document-arrow-down" class="bg-green-600 hover:bg-green-700">
                            Export Excel
                        </flux:button>
                        <flux:button variant="filled" size="sm" wire:click="exportOdometerData('pdf')" icon="document-arrow-down" class="bg-red-600 hover:bg-red-700">
                            Export PDF
                        </flux:button>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th wire:click="sortBy('recorded_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Date</span>
                                @if($sortField === 'recorded_at')
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
                        <th wire:click="sortBy('vehicle_id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Vehicle</span>
                                @if($sortField === 'vehicle_id')
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
                        <th wire:click="sortBy('odometer_reading')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Reading</span>
                                @if($sortField === 'odometer_reading')
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
                        <th class="px-6 py-3">
                            <div class="flex items-center">
                                <span class="text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</span>
                                <flux:tooltip toggleable>
                                    <flux:button size="xs" variant="ghost" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                        <svg width="15" height="15" viewBox="0 0 24 23" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="M12 16v-4"/>
                                            <path d="M12 8h.01"/>
                                        </svg>
                                    </flux:button>
                                    <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                        <p><strong>Manual</strong>: tesaa</p>
                                        <p><strong>Trip Start</strong>: aser</p>
                                        <p><strong>Trip End</strong>: </p>
                                        <p><strong>Service</strong>: </p>
                                    </flux:tooltip.content>
                                </flux:tooltip>
                            </div>

                        </th>
                        <th wire:click="sortBy('distance_traveled')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Distance</span>
                                @if($sortField === 'distance_traveled')
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Recorded By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Booking</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($odometerLogs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $log->recorded_at->format('M j, Y') }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900 dark:text-neutral-200 mt-1">{{ $log->vehicle->model ?? 'N/A' }}</span>
                                <span class="text-sm text-gray-500 font-normal">({{ $log->vehicle->plate_number }})</span>                                 
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                {{ number_format($log->odometer_reading) }} km
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeColor = match($log->reading_type) {
                                        'start' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                        'end' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                        'manual' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                        'service' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/50 dark:text-gray-300'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
                                    {{ ucfirst($log->reading_type) }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @if($log->distance_traveled)
                                    <div class="flex items-center space-x-2">
                                        <span>{{ number_format($log->distance_traveled) }} km</span>
                                        @if(str_contains($log->notes ?? '', '[Manual Distance Entry]'))
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Manual
                                            </span>
                                        @elseif(str_contains($log->notes ?? '', '[Auto-calculated Distance]'))
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 0l-3-3m3 3l-3 3m-3 4h6m0 0l-3-3m3 3l-3 3"/>
                                                </svg>
                                                Auto
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $log->recordedBy ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $log->recordedBy->name) : ($log->performed_by ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $log->performed_by) : 'N/A') }}                                
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @if($log->booking)
                                    <div class="text-sm font-medium">{{ $log->booking->user->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $log->booking->user->name) : ($log->booking->user->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $log->booking->user->name) : 'N/A') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">#{{ $log->booking->id }}</div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @can('vehicle.edit')
                                    <flux:button size="xs" wire:click="editLog({{ $log->id }})" variant="ghost">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </flux:button>
                                    @endcan
                                    @can('vehicle.delete')
                                    <flux:button size="xs" wire:click="deleteLog({{ $log->id }})" wire:confirm="Are you sure you want to delete this fuel log?" variant="ghost">
                                        <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </flux:button>
                                    @endcan                                      
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <h3 class="font-medium text-gray-900 dark:text-white mb-1">No odometer readings</h3>
                                    <p class="text-gray-500 dark:text-gray-400">Get started by recording your first odometer reading.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($odometerLogs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $odometerLogs->links() }}
            </div>
        @endif
    </div>

    <!-- FluxUI Modal -->
    <flux:modal name="odometer-form" class="max-w-4xl" variant="flyout">
        <form wire:submit="saveLog">
            <flux:heading size="lg" class="mb-6">
                {{ $editingLog ? 'Edit Odometer Reading' : 'Record New Odometer Reading' }}
            </flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <flux:field>
                    <flux:label>Vehicle <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:select wire:model.live="vehicle_id" placeholder="Select Vehicle" >
                        @foreach($vehicles as $vehicle)
                            <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->model }} ({{ $vehicle->plate_number }})</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="vehicle_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Reading Type <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:select wire:model="reading_type" placeholder="Select Type" >
                        <flux:select.option value="manual">Manual</flux:select.option>
                        <flux:select.option value="start">Trip Start</flux:select.option>
                        <flux:select.option value="end">Trip End</flux:select.option>
                        <flux:select.option value="service">Service</flux:select.option>
                    </flux:select>
                    <flux:error name="reading_type" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <flux:field>
                    <flux:label>Odometer Reading (km) <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:input type="number" wire:model.live="odometer_reading" placeholder="Current km reading" min="0" max="9999999" />
                    <flux:error name="odometer_reading" />
                </flux:field>

                <flux:field>
                    <flux:label>Distance Traveled (km)</flux:label>

                    @if($calculated_distance && $vehicle_id && $odometer_reading)
                        <div class="space-y-3">
                            <!-- Calculated Distance Info -->
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 0l-3-3m3 3l-3 3m-3 4h6m0 0l-3-3m3 3l-3 3"/>
                                        </svg>
                                        <span class="text-sm text-blue-700 dark:text-blue-300">
                                            System calculated: <strong>{{ number_format($calculated_distance) }} km</strong>
                                        </span>
                                    </div>
                                    @if($is_manual_distance)
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            wire:click="useCalculatedDistance"
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400"
                                        >
                                            Use calculated
                                        </flux:button>
                                    @endif
                                </div>
                                @if($this->getLatestOdometerForSelectedVehicle())
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                        Based on previous reading: {{ number_format($this->getLatestOdometerForSelectedVehicle()) }} km
                                    </p>
                                @endif
                            </div>

                            <!-- Distance Input -->
                            <div class="relative">
                                <flux:input
                                    type="number"
                                    wire:model.live="distance_traveled"
                                    placeholder="Enter manual distance or use calculated"
                                    min="0"
                                    max="99999"
                                    step="0.1"
                                    class="{{ $is_manual_distance ? 'border-orange-300 focus:border-orange-500 focus:ring-orange-200' : '' }}"
                                />
                                @if($is_manual_distance)
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            @if($is_manual_distance && $calculated_distance != $distance_traveled)
                                <div class="text-xs text-orange-600 dark:text-orange-400">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    Manual entry: differs from calculated distance
                                </div>
                            @endif
                        </div>
                    @else
                        <flux:input
                            type="number"
                            wire:model.live="distance_traveled"
                            placeholder="Distance for this trip (will auto-calculate when odometer entered)"
                            min="0"
                            max="99999"
                            step="0.1"
                        />
                    @endif

                    <flux:error name="distance_traveled" />
                </flux:field>
            </div>

            <flux:field class="mb-4">
                <flux:label>Performed By <span class="text-red-500 ms-1">*</span></flux:label>
                <flux:input wire:model="performed_by" placeholder="Enter person who recorded the reading" maxlength="255"  />
                <flux:error name="performed_by" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <flux:field>
                    <flux:label>Recorded At <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:date-picker wire:model="recorded_at" with-today/>
                    <flux:error name="recorded_at" />
                </flux:field>

                <flux:field>
                    <flux:label>Booking <span class="text-gray-500 ms-1">(Optional)</span></flux:label>
                    <flux:select wire:model="booking_id" placeholder="Select Booking">
                        @foreach($bookings as $booking)
                            <flux:select.option value="{{ $booking->id }}">{{ $booking->user->name ?? 'N/A' }} - #{{ $booking->id }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="booking_id" />
                </flux:field>
            </div>

            <flux:field class="mb-6">
                <flux:label>Notes</flux:label>
                <flux:textarea wire:model="notes" placeholder="Additional notes about this reading" rows="3" maxlength="1000" />
                <flux:error name="notes" />
            </flux:field>

            <div class="flex justify-end space-x-3 mt-6">
                <flux:button variant="ghost" wire:click="cancelForm">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editingLog ? 'Update Reading' : 'Record Reading' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    @script
    <script>
        $wire.on('open-modal', () => {
            $flux.modal('odometer-form').show();
        });

        $wire.on('close-modal', () => {
            $flux.modal('odometer-form').close();
        });

        $wire.on('odometer-export', (data) => {
            const params = new URLSearchParams({
                vehicle_id: data.vehicle_id || '',
                date_from: data.date_from || '',
                date_to: data.date_to || '',
                analytics_type: 'odometer',
                format: data.format || 'excel'
            });

            // Create export URL
            const exportUrl = `/vehicle-analytics/export?${params}`;
            
            // Download the file
            window.open(exportUrl, '_blank');
            
            // Show success message
            $flux.toast({
                title: 'Export Started',
                body: `${data.format.toUpperCase()} export is being generated...`,
                variant: 'success'
            });
        });
    </script>
    @endscript
</div>