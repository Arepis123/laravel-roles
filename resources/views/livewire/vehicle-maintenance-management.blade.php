<div class="relative mb-6 w-full">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white dark:text-white">Vehicle Maintenance Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Track and manage vehicle maintenance records</p>
            </div>
            <flux:modal.trigger name="maintenance-form">
                <div class="flex justify-between items-center">
                    <flux:button variant="primary" wire:click="showAddForm" icon="plus" class="w-full sm:w-auto">
                        Add Maintenance
                    </flux:button>
                </div>                
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Alert Cards -->
    @if($stats['overdue_maintenance'] > 0 || $stats['upcoming_maintenance'] > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            @if($stats['overdue_maintenance'] > 0)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-300">{{ $stats['overdue_maintenance'] }} Overdue Maintenance</h3>
                            <p class="text-sm text-red-700 dark:text-red-400">Vehicles need immediate attention</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($stats['upcoming_maintenance'] > 0)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">{{ $stats['upcoming_maintenance'] }} Upcoming Maintenance</h3>
                            <p class="text-sm text-yellow-700 dark:text-yellow-400">Due within 30 days</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
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
                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>  
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Records</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_records']) }}</flux:text>
                </div>
            </div>
        </flux:card>  

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 lucide lucide-wallet-icon lucide-wallet" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>
                    </svg>                      
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Avg Cost/Liter</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">RM{{ number_format($stats['avg_cost_per_service'], 2) }}</flux:text>
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
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Vehicles Serviced</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['vehicles_serviced'] }}</flux:text>
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
                    <div class="space-y-4 pt-4 mx-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <flux:field>
                                <flux:label>Vehicle</flux:label>
                                <flux:select variant="listbox" wire:model.live="filterVehicle" placeholder="All Vehicles">
                                    @foreach($vehicles as $vehicle)
                                        <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->model }} ({{ $vehicle->plate_number }})</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                            <flux:field>
                                <flux:label>Maintenance Type</flux:label>
                                <flux:select variant="listbox" wire:model.live="filterMaintenanceType" placeholder="All Types">
                                    <flux:select.option value="service">Service</flux:select.option>
                                    <flux:select.option value="repair">Repair</flux:select.option>
                                    <flux:select.option value="inspection">Inspection</flux:select.option>
                                    <flux:select.option value="oil_change">Oil Change</flux:select.option>
                                    <flux:select.option value="tire_change">Tire Change</flux:select.option>
                                    <flux:select.option value="brake_service">Brake Service</flux:select.option>
                                    <flux:select.option value="other">Other</flux:select.option>
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

                        <div class="flex gap-4 *:gap-x-2">
                            <flux:checkbox wire:model.live="filterUpcoming" label="Upcoming Maintenance" />
                            <flux:checkbox wire:model.live="filterOverdue" label="Overdue Maintenance" />
                        </div>

                        <!-- Export Buttons -->
                        <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-zinc-700">
                            <flux:button variant="filled" size="sm" wire:click="exportMaintenanceData('excel')" icon="document-arrow-down" class="bg-green-600 hover:bg-green-700">
                                Export Excel
                            </flux:button>
                            <flux:button variant="filled" size="sm" wire:click="exportMaintenanceData('pdf')" icon="document-arrow-down" class="bg-red-600 hover:bg-red-700">
                                Export PDF
                            </flux:button>
                        </div>
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
                        <th wire:click="sortBy('performed_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Date</span>
                                @if($sortField === 'performed_at')
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th wire:click="sortBy('cost')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Cost</span>
                                @if($sortField === 'cost')
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Service Provider</th>
                        <th wire:click="sortBy('next_maintenance_due')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Next Due</span>
                                @if($sortField === 'next_maintenance_due')
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($maintenanceLogs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $log->performed_at->format('M j, Y') }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $log->vehicle->model ?? 'N/A' }}</h3> 
                                <span class="text-sm text-gray-500 font-normal">{{ $log->vehicle->plate_number ?? 'N/A' }}</span>  
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeColor = match($log->maintenance_type) {
                                        'service' => 'lime',
                                        'repair' => 'rose',
                                        'inspection' => 'yellow',
                                        'oil_change' => 'emerald',
                                        'tire_change' => 'cyan',
                                        'brake_service' => 'indigo',
                                        default => 'zinc'
                                    };
                                @endphp
                                <flux:badge size="sm" color="{{ $badgeColor }}">{{ ucfirst(str_replace('_', ' ', $log->maintenance_type)) }}</flux:badge>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->status === 'ongoing')
                                    <flux:text>Ongoing</flux:text>
                                @elseif($log->status === 'scheduled')
                                    <flux:text class="text-gray-900 dark:text-white">Scheduled</flux:text>
                                @else
                                    <flux:text class="text-gray-900 dark:text-white">Completed</flux:text>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->cost)
                                    <flux:text class="text-gray-900 dark:text-white">RM{{ number_format($log->cost, 2) }}</flux:text>
                                @else
                                    <flux:text class="text-gray-900 dark:text-white">-</flux:text>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">                                
                                <flux:text class="text-gray-900 dark:text-white">{{ $log->service_provider ?: '-' }}</flux:text>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($log->next_maintenance_due)
                                    @php
                                        $daysUntilDue = floor(now()->diffInDays($log->next_maintenance_due, false));
                                    @endphp
                                    <div class="
                                        {{ $daysUntilDue < 0 ? 'text-red-600 font-medium' : '' }}
                                        {{ $daysUntilDue >= 0 && $daysUntilDue <= 30 ? 'text-yellow-600 font-medium' : '' }}
                                        {{ $daysUntilDue > 30 ? 'text-gray-900 dark:text-white' : '' }}">
                                        {{ $log->next_maintenance_due->format('M j, Y') }}
                                        @if($daysUntilDue < 0)
                                            <div class="text-xs text-red-500">{{ abs($daysUntilDue) }} days overdue</div>
                                        @elseif($daysUntilDue <= 30)
                                            <div class="text-xs text-yellow-600">{{ $daysUntilDue }} days</div>
                                        @endif
                                    </div>
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                    <h3 class="font-medium text-gray-900 dark:text-white mb-1">No maintenance records</h3>
                                    <p class="text-gray-500 dark:text-gray-400">Get started by adding your first maintenance record.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($maintenanceLogs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $maintenanceLogs->links() }}
            </div>
        @endif
    </div>

    <!-- FluxUI Modal -->
    <flux:modal name="maintenance-form" class="max-w-4xl" variant="flyout">
        <form wire:submit="saveLog">
            <flux:heading size="lg" class="mb-4">
                {{ $editingLog ? 'Edit Maintenance Record' : 'Add New Maintenance Record' }}
            </flux:heading>

            @if(!$editingLog)
                <!-- Mode Toggle -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg border">
                    <flux:checkbox wire:model.live="isScheduleMode" label="Schedule Future Maintenance" />
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $isScheduleMode ? 'Schedule maintenance for a future date without logging completed work' : 'Log completed or ongoing maintenance work' }}
                    </p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <flux:field>
                    <flux:label>Vehicle <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:select variant="listbox" wire:model.live="vehicle_id" placeholder="Select Vehicle" >
                        @foreach($vehicles as $vehicle)
                            <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->model }} ({{ $vehicle->plate_number }})</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="vehicle_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Maintenance Type <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:select variant="listbox" wire:model.live="maintenance_type" placeholder="Select Type" >
                        <flux:select.option value="service">Service</flux:select.option>
                        <flux:select.option value="repair">Repair</flux:select.option>
                        <flux:select.option value="inspection">Inspection</flux:select.option>
                        <flux:select.option value="oil_change">Oil Change</flux:select.option>
                        <flux:select.option value="tire_change">Tire Change</flux:select.option>
                        <flux:select.option value="brake_service">Brake Service</flux:select.option>
                        <flux:select.option value="other">Other</flux:select.option>
                    </flux:select>
                    <flux:error name="maintenance_type" />
                </flux:field>
            </div>

            <flux:field class="mb-4">
                <flux:label>Title<span class="text-red-500 ms-1"><span class="text-red-500 ms-1">*</span></span></flux:label>
                <flux:input wire:model="title" placeholder="Enter maintenance title" maxlength="255"  />
                <flux:error name="title" />
            </flux:field>

            <flux:field class="mb-4">
                <flux:label>Description @if(!$isScheduleMode)<span class="text-red-500 ms-1">*</span>@endif</flux:label>
                <flux:textarea wire:model="description" placeholder="{{ $isScheduleMode ? 'Describe the scheduled maintenance (optional)' : 'Describe the maintenance work' }}" rows="3" maxlength="500" />
                <flux:error name="description" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <flux:field>
                    <flux:label>Cost (RM)</flux:label>
                    <flux:input type="number" wire:model="cost" placeholder="0.00" min="0" max="999999.99" step="0.01" />
                    <flux:error name="cost" />
                </flux:field>

                <flux:field>
                    <flux:label>Service Provider</flux:label>
                    <flux:input wire:model="service_provider" placeholder="Enter service provider name" maxlength="255" />
                    <flux:error name="service_provider" />
                </flux:field>
            </div>

            <flux:field class="mb-4">
                <flux:label>{{ $isScheduleMode ? 'Assigned To' : 'Performed By' }} @if(!$isScheduleMode)<span class="text-red-500 ms-1">*</span>@endif</flux:label>
                <flux:input wire:model="performed_by" placeholder="{{ $isScheduleMode ? 'Who will perform this maintenance (optional)' : 'Enter technician or person who performed maintenance' }}" maxlength="255" />
                <flux:error name="performed_by" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <flux:field>
                    <flux:label>Odometer Reading (km)</flux:label>
                    <flux:input type="number" wire:model="odometer_at_service" placeholder="Current km reading" min="0" max="9999999" />
                    <flux:error name="odometer_at_service" />
                </flux:field>

                <flux:field>
                    <flux:label>Booking<span class="text-gray-500 ms-1">(Optional)</span></flux:label>
                    <flux:select variant="listbox" wire:model="booking_id" placeholder="Select Booking">
                        @foreach($bookings as $booking)
                            <flux:select.option value="{{ $booking->id }}">{{ $booking->user->name ?? 'N/A' }} - #{{ $booking->id }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="booking_id" />
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <flux:field>
                    <flux:label>{{ $isScheduleMode ? 'Scheduled For' : 'Performed At' }} <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:input type="datetime-local" wire:model="performed_at" />
                    <flux:error name="performed_at" />
                    @if($isScheduleMode)
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Select a future date and time</div>
                    @endif
                </flux:field>

                @if(!$isScheduleMode)
                    <flux:field>
                        <flux:label>Next Maintenance Due</flux:label>
                        <flux:date-picker wire:model="next_maintenance_due" with-today/>
                        <flux:error name="next_maintenance_due" />
                    </flux:field>
                @endif
            </div>

            @if(!$isScheduleMode)
                <flux:field class="mb-4">
                    <flux:heading class="flex items-center">
                        Maintenance Status <span class="text-red-500 ms-1">*</span>
                        <flux:tooltip toggleable>
                            <flux:button icon="information-circle" size="sm" variant="ghost"/>
                            <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                <p>Ongoing - Vehicle will be unavailable for booking</p>
                                <p>Completed - Vehicle will be available for booking</p>
                                @if($this->showScheduledOption)
                                    <p>Scheduled - Maintenance planned for future date</p>
                                @endif
                            </flux:tooltip.content>
                        </flux:tooltip>
                    </flux:heading>
                    <flux:select variant="listbox" wire:model="status" >
                        <flux:select.option value="ongoing">Ongoing</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                        @if($this->showScheduledOption)
                            <flux:select.option value="scheduled">Scheduled</flux:select.option>
                        @endif
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>
            @else
                <!-- Schedule mode automatically sets status to 'scheduled' -->
                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-medium text-blue-800 dark:text-blue-300">This maintenance will be marked as "Scheduled"</span>
                    </div>
                </div>
            @endif

            <flux:field class="mb-6">
                <flux:label>Notes</flux:label>
                <flux:textarea wire:model="notes" placeholder="Additional notes about the maintenance" rows="3" maxlength="1000" />
                <flux:error name="notes" />
            </flux:field>

            <div class="flex justify-end space-x-3 mt-6">
                <flux:button variant="ghost" wire:click="cancelForm">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editingLog ? 'Update Record' : 'Save Record' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    @script
    <script>
        $wire.on('open-modal', () => {
            $flux.modal('maintenance-form').show();
        });

        $wire.on('close-modal', () => {
            $flux.modal('maintenance-form').close();
        });

        $wire.on('maintenance-export', (data) => {
            // Debug logging
            console.log('Maintenance export data received:', data);
            console.log('Format received:', data.format);
            
            const params = new URLSearchParams({
                vehicle_id: data.vehicle_id || '',
                date_from: data.date_from || '',
                date_to: data.date_to || '',
                analytics_type: 'maintenance',
                format: data.format || 'excel'
            });

            // Create export URL
            const exportUrl = `/vehicle-analytics/export?${params}`;
            
            console.log('Constructed URL:', exportUrl);
            
            // Download the file
            window.open(exportUrl, '_blank');
            
            // Show success message
            $flux.toast({
                title: 'Export Started',
                body: `${(data.format || 'Unknown').toUpperCase()} export is being generated...`,
                variant: 'success'
            });
        });
    </script>
    @endscript
</div>