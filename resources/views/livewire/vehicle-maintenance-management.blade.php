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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
        <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 lucide lucide-circle-dollar-sign-icon lucide-circle-dollar-sign" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="12" cy="12" r="10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18V6"/>
                    </svg>                    
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Cost</h3>
                    <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($stats['total_cost'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Records</h3>
                    <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_records']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 lucide lucide-wallet-icon lucide-wallet" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>
                    </svg>                    
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Cost/Service</h3>
                    <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($stats['avg_cost_per_service'], 2) }}</p>
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
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Vehicles Serviced</h3>
                    <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['vehicles_serviced'] }}</p>
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
                        <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->model }} ({{ $vehicle->license_plate }})</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Maintenance Type</flux:label>
                <flux:select wire:model.live="filterMaintenanceType" placeholder="All Types">
                    <flux:select.option value="routine">Routine</flux:select.option>
                    <flux:select.option value="repair">Repair</flux:select.option>
                    <flux:select.option value="inspection">Inspection</flux:select.option>
                    <flux:select.option value="emergency">Emergency</flux:select.option>
                    <flux:select.option value="tire">Tire</flux:select.option>
                    <flux:select.option value="oil_change">Oil Change</flux:select.option>
                    <flux:select.option value="brake">Brake</flux:select.option>
                    <flux:select.option value="engine">Engine</flux:select.option>
                    <flux:select.option value="electrical">Electrical</flux:select.option>
                    <flux:select.option value="bodywork">Bodywork</flux:select.option>
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

        <div class="flex gap-4 *:gap-x-2">
            <flux:checkbox wire:model.live="filterUpcoming" label="Upcoming Maintenance" />
            <flux:checkbox wire:model.live="filterOverdue" label="Overdue Maintenance" />
        </div>
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
                                        'routine' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                        'repair', 'emergency' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                        'inspection' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                        'oil_change' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
                                        default => 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $log->maintenance_type)) }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->status === 'ongoing')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                        🔧 Ongoing
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                        ✅ Completed
                                    </span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @if($log->cost)
                                    ${{ number_format($log->cost, 2) }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $log->service_provider ?: '-' }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($log->next_maintenance_due)
                                    @php
                                        $daysUntilDue = now()->diffInDays($log->next_maintenance_due, false);
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
                                        wire:confirm="Are you sure you want to delete this maintenance record?"
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
            <flux:heading size="lg" class="mb-6">
                {{ $editingLog ? 'Edit Maintenance Record' : 'Add New Maintenance Record' }}
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
                    <flux:label>Maintenance Type <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:select wire:model.live="maintenance_type" placeholder="Select Type" >
                        <flux:select.option value="routine">Routine</flux:select.option>
                        <flux:select.option value="repair">Repair</flux:select.option>
                        <flux:select.option value="inspection">Inspection</flux:select.option>
                        <flux:select.option value="emergency">Emergency</flux:select.option>
                        <flux:select.option value="tire">Tire</flux:select.option>
                        <flux:select.option value="oil_change">Oil Change</flux:select.option>
                        <flux:select.option value="brake">Brake</flux:select.option>
                        <flux:select.option value="engine">Engine</flux:select.option>
                        <flux:select.option value="electrical">Electrical</flux:select.option>
                        <flux:select.option value="bodywork">Bodywork</flux:select.option>
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
                <flux:label>Description <span class="text-red-500 ms-1">*</span></flux:label>
                <flux:textarea wire:model="description" placeholder="Describe the maintenance work" rows="3" maxlength="500"  />
                <flux:error name="description" />
            </flux:field>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <flux:field>
                    <flux:label>Cost ($)</flux:label>
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
                <flux:label>Performed By <span class="text-red-500 ms-1">*</span></flux:label>
                <flux:input wire:model="performed_by" placeholder="Enter technician or person who performed maintenance" maxlength="255"  />
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
                    <flux:label>Performed At <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:input type="datetime-local" wire:model="performed_at"  />
                    <flux:error name="performed_at" />
                </flux:field>

                <flux:field>
                    <flux:label>Next Maintenance Due</flux:label>
                    <flux:input type="date" wire:model="next_maintenance_due" />
                    <flux:error name="next_maintenance_due" />
                </flux:field>
            </div>

            <flux:field class="mb-4">
                <!-- <flux:label>Maintenance Status <span class="text-red-500 ms-1">*</span></flux:label> -->
                <flux:heading class="flex items-center">
                    Maintenance Status <span class="text-red-500 ms-1">*</span>
                    <flux:tooltip toggleable>
                        <flux:button icon="information-circle" size="sm" variant="ghost"/>
                        <flux:tooltip.content class="max-w-[20rem] space-y-2">
                            <p>Ongoing - Vehicle will be unavailable for booking</p>
                            <p>Completed - Vehicle will be available for booking</p>
                        </flux:tooltip.content>
                    </flux:tooltip>
                </flux:heading>                
                <flux:select wire:model="status" >
                    <flux:select.option value="ongoing">Ongoing</flux:select.option>
                    <flux:select.option value="completed">Completed</flux:select.option>
                </flux:select>
                <flux:error name="status" />
            </flux:field>

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
    </script>
    @endscript
</div>