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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
        <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 lucide lucide-land-plot-icon lucide-land-plot" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 8 6-3-6-3v10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 11.99-5.5 3.14a1 1 0 0 0 0 1.74l8.5 4.86a2 2 0 0 0 2 0l8.5-4.86a1 1 0 0 0 0-1.74L16 12"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6.49 12.85 11.02 6.3"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.51 12.85 6.5 19.15"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Distance</h3>
                    <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_distance']) }} km</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Readings</h3>
                    <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_readings']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Distance/Trip</h3>
                    <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['avg_distance_per_trip'], 1) }} km</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400 lucide lucide-car-front-icon lucide-car-front" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 8-2 2-1.5-3.7A2 2 0 0 0 15.646 5H8.4a2 2 0 0 0-1.903 1.257L5 10 3 8"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 14h.01"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14h.01"/>
                        <rect stroke-linecap="round" stroke-linejoin="round" stroke-width="2" width="18" height="8" x="3" y="10" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 18v2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 18v2"/>
                    </svg>                                                         
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Vehicles Tracked</h3>
                    <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['vehicles_tracked'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filters</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-4">
            <flux:field>
                <flux:label>Vehicle</flux:label>
                <flux:select wire:model.live="filterVehicle" placeholder="All Vehicles">
                    @foreach($vehicles as $vehicle)
                        <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->model }} ({{ $vehicle->plate_number }})</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Reading Type</flux:label>
                <flux:select wire:model.live="filterReadingType" placeholder="All Types">
                    <flux:select.option value="start">Start</flux:select.option>
                    <flux:select.option value="end">End</flux:select.option>
                    <flux:select.option value="manual">Manual</flux:select.option>
                    <flux:select.option value="service">Service</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>From Date</flux:label>
                <flux:input type="date" wire:model.live="filterDateFrom" />
            </flux:field>

            <flux:field>
                <flux:label>To Date</flux:label>
                <flux:input type="date" wire:model.live="filterDateTo" />
            </flux:field>
        </div>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
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
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $log->vehicle->model ?? 'N/A' }}</h3> 
                                <span class="text-sm text-gray-500 font-normal">{{ $log->vehicle->plate_number ?? 'N/A' }}</span>  
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
                                    {{ number_format($log->distance_traveled) }} km
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
                                    <button 
                                        wire:click="editLog({{ $log->id }})" 
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button 
                                        wire:click="deleteLog({{ $log->id }})" 
                                        wire:confirm="Are you sure you want to delete this odometer reading?"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <flux:input type="number" wire:model="distance_traveled" placeholder="Distance for this trip" min="0" max="99999" step="0.1" />
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
                    <flux:input type="datetime-local" wire:model="recorded_at"  />
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
    </script>
    @endscript
</div>