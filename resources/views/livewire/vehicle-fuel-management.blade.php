<div class="relative mb-6 w-full">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Vehicle Fuel Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Track and manage vehicle fuel consumption</p>
            </div>
            <flux:modal.trigger name="fuel-form">
                <div class="flex justify-between items-center">
                    <flux:button variant="primary" wire:click="showAddForm" icon="plus" class="w-full sm:w-auto">
                        Add Fuel Log
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
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 lucide lucide-fuel-icon lucide-fuel" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <line stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x1="3" x2="15" y1="22" y2="22"/><line x1="4" x2="14" y1="9" y2="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 13h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2a2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 5"/>
                    </svg>                         
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Fuel</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_fuel']) }} L</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 lucide lucide-circle-dollar-sign-icon lucide-circle-dollar-sign" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="12" cy="12" r="10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18V6"/>
                    </svg>   
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Cost</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">RM{{ number_format($stats['total_cost'], 2) }}</flux:text>
                </div>
            </div>
        </flux:card>  

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400 lucide lucide-wallet-icon lucide-wallet" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>
                    </svg>                      
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Avg Cost/Liter</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">RM{{ number_format($stats['avg_cost_per_liter'], 3) }}</flux:text>
                </div>
            </div>
        </flux:card>   
        
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Sessions</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_sessions']) }}</flux:text>
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
                        <svg class="w-5 h-5 transition-transform duration-200 accordion-icon" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <flux:label>Fuel Type</flux:label>
                            <flux:select variant="listbox" wire:model.live="filterFuelType" placeholder="All Types">
                                <flux:select.option value="petrol">Petrol</flux:select.option>
                                <flux:select.option value="diesel">Diesel</flux:select.option>
                                <flux:select.option value="hybrid">Hybrid</flux:select.option>
                                <flux:select.option value="electric">Electric</flux:select.option>
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
                        <flux:button variant="filled" size="sm" wire:click="exportFuelData('excel')" icon="document-arrow-down" class="bg-green-600 hover:bg-green-700">
                            Export Excel
                        </flux:button>
                        <flux:button variant="filled" size="sm" wire:click="exportFuelData('pdf')" icon="document-arrow-down" class="bg-red-600 hover:bg-red-700">
                            Export PDF
                        </flux:button>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </div>

    <!-- Fuel Logs Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th wire:click="sortBy('filled_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Date</span>
                                @if($sortField === 'filled_at')
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
                        <th wire:click="sortBy('fuel_amount')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Amount</span>
                                @if($sortField === 'fuel_amount')
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
                        <th wire:click="sortBy('fuel_cost')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Cost</span>
                                @if($sortField === 'fuel_cost')
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Station</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Filled By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($fuelLogs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $log->filled_at->format('M j, Y') }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900 dark:text-neutral-200 mt-1">{{ $log->vehicle->model ?? 'N/A' }}</span>
                                <span class="text-sm text-gray-500 font-normal">({{ $log->vehicle->plate_number }})</span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                {{ number_format($log->fuel_amount, 2) }} L
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:text class="text-gray-900 dark:text-white"> {{ ucfirst($log->fuel_type) }}</flux:text>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @if($log->fuel_cost)
                                    RM{{ number_format($log->fuel_cost, 2) }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $log->fuel_station ?: '-' }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $log->filledBy->name ? preg_replace('/\s+(BIN|BINTI|BT)\b.*/i', '', $log->filledBy->name) : ($log->filledBy->name ? preg_replace('/\s+(BIN|BINTI|BT)\b.*/i', '', $log->filledBy->name) : 'N/A') }}
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                                    </svg>
                                    <h3 class="font-medium text-gray-900 dark:text-white mb-1">No fuel logs</h3>
                                    <p class="text-gray-500 dark:text-gray-400">Get started by adding your first fuel log.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($fuelLogs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $fuelLogs->links() }}
            </div>
        @endif
    </div>

    <!-- FluxUI Modal -->
    <flux:modal name="fuel-form" class="max-w-4xl" variant="flyout">
        <form wire:submit="saveLog">
            <flux:heading size="lg" class="mb-6">
                {{ $editingLog ? 'Edit Fuel Log' : 'Add New Fuel Log' }}
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
                    <flux:label>Booking <span class="text-gray-500 ms-1">(Optional)</span></flux:label>
                    <flux:select wire:model="booking_id" placeholder="Select Booking">
                        @foreach($bookings as $booking)
                            <flux:select.option value="{{ $booking->id }}">{{ $booking->user->name ?? 'N/A' }} - #{{ $booking->id }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="booking_id" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <flux:field>
                    <flux:label>Fuel Amount (L) <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:input type="number" wire:model="fuel_amount" placeholder="Amount of fuel" min="0.1" max="1000" step="0.1" />
                    <flux:error name="fuel_amount" />
                </flux:field>

                <flux:field>
                    <flux:label>Fuel Type <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:select wire:model="fuel_type" placeholder="Select Type" >
                        <flux:select.option value="petrol">Petrol</flux:select.option>
                        <flux:select.option value="diesel">Diesel</flux:select.option>
                        <flux:select.option value="hybrid">Hybrid</flux:select.option>
                        <flux:select.option value="electric">Electric</flux:select.option>
                    </flux:select>
                    <flux:error name="fuel_type" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <flux:field>
                    <flux:label>Fuel Cost (RM)</flux:label>
                    <flux:input type="number" wire:model="fuel_cost" placeholder="Cost of fuel" min="0" max="10000" step="0.01" />
                    <flux:error name="fuel_cost" />
                </flux:field>

                <flux:field>
                    <flux:label>Fuel Station</flux:label>
                    <flux:input wire:model="fuel_station" placeholder="Name of fuel station" maxlength="255" />
                    <flux:error name="fuel_station" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <flux:field>
                    <flux:label>Odometer Reading (km)</flux:label>
                    <flux:input type="number" wire:model="odometer_at_fill" placeholder="Odometer reading at fill" min="0" max="999999" />
                    <flux:error name="odometer_at_fill" />
                </flux:field>

                <flux:field>
                    <flux:label>Filled At <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:input type="datetime-local" wire:model="filled_at"  />
                    <flux:error name="filled_at" />
                </flux:field>
            </div>

            <flux:field class="mb-6">
                <flux:label>Notes</flux:label>
                <flux:textarea wire:model="notes" placeholder="Additional notes about this fuel log" rows="3" maxlength="1000" />
                <flux:error name="notes" />
            </flux:field>

            <div class="flex justify-end space-x-3 mt-6">
                <flux:button variant="ghost" wire:click="cancelForm">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editingLog ? 'Update Log' : 'Save Log' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    @script
    <script>
        $wire.on('open-modal', () => {
            $flux.modal('fuel-form').show();
        });

        $wire.on('close-modal', () => {
            $flux.modal('fuel-form').close();
        });

        $wire.on('fuel-export', (data) => {
            const params = new URLSearchParams({
                vehicle_id: data.vehicle_id || '',
                date_from: data.date_from || '',
                date_to: data.date_to || '',
                analytics_type: 'fuel',
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