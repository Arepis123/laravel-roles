<div class="p-4 sm:p-6 bg-white dark:bg-zinc-800 min-h-screen">
    <!-- Vehicle Header -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700 p-4 sm:p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                <div class="p-4 bg-blue-100 dark:bg-blue-900/50 rounded-lg self-start">
                    <svg class="w-12 h-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">{{ $vehicle->model }}</h1>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-2">
                        <span class="px-3 py-1 bg-gray-100 dark:bg-zinc-700 text-gray-800 dark:text-gray-200 text-sm rounded-full font-medium">
                            {{ $vehicle->license_plate }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 hidden sm:inline">•</span>
                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ $vehicle->year ?? 'N/A' }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 hidden sm:inline">•</span>
                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ $vehicle->make ?? 'N/A' }}</span>
                        @if($vehicle->status)
                            <span class="text-sm text-gray-500 dark:text-gray-400 hidden sm:inline">•</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                {{ $vehicle->status === 'available' ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300' : '' }}
                                {{ $vehicle->status === 'in_use' ? 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300' : '' }}
                                {{ $vehicle->status === 'maintenance' ? 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300' : '' }}
                                {{ $vehicle->status === 'inactive' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
                <button wire:click="exportVehicleData('excel')" 
                        class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white px-4 py-2 rounded-lg flex-1 sm:flex-initial">
                    Export Excel
                </button>
                <button wire:click="exportVehicleData('pdf')" 
                        class="bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white px-4 py-2 rounded-lg flex-1 sm:flex-initial">
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Cards -->
    @if(count($overdueMaintenance) > 0 || count($upcomingMaintenance) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
            @if(count($overdueMaintenance) > 0)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-300">{{ count($overdueMaintenance) }} Overdue Maintenance</h3>
                            @foreach($overdueMaintenance as $maintenance)
                                <p class="text-sm text-red-700 dark:text-red-400 mt-1">{{ $maintenance->description }} - Due {{ $maintenance->next_maintenance_due->format('M j, Y') }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            @if(count($upcomingMaintenance) > 0)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">{{ count($upcomingMaintenance) }} Upcoming Maintenance</h3>
                            @foreach($upcomingMaintenance as $maintenance)
                                <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-1">{{ $maintenance->description }} - Due {{ $maintenance->next_maintenance_due->format('M j, Y') }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Date Range Filter -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Date Range</h3>
            <div class="flex items-center space-x-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <input type="date" wire:model.live="dateFrom" class="border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="date" wire:model.live="dateTo" class="border-gray-300 rounded-lg text-sm">
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Distance</h3>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ number_format($vehicleStats['odometer_data']['total_distance'] ?? 0) }} km
                    </p>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Fuel Consumed</h3>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ number_format($vehicleStats['fuel_data']['total_fuel'] ?? 0, 1) }} L
                    </p>
                </div>
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Fuel Efficiency</h3>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ number_format($vehicleStats['fuel_data']['average_efficiency'] ?? 0, 2) }} km/L
                    </p>
                </div>
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Maintenance Cost</h3>
                    <p class="text-2xl font-semibold text-gray-900">
                        ${{ number_format($vehicleStats['maintenance_data']['total_cost'] ?? 0, 2) }}
                    </p>
                </div>
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Readings Panel -->
    <div class="bg-white rounded-lg shadow-sm border mb-6">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Latest Readings</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-medium text-gray-700">Last Fuel Fill</h4>
                    <button wire:click="toggleQuickAction('fuel')" 
                            class="text-blue-600 hover:text-blue-700 text-sm">
                        Add New
                    </button>
                </div>
                @if($latestReadings['fuel'])
                    <div class="space-y-2">
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($latestReadings['fuel']->fuel_amount, 1) }} L</p>
                        <p class="text-sm text-gray-500">{{ $latestReadings['fuel']->filled_at->format('M j, Y H:i') }}</p>
                        @if($latestReadings['fuel']->fuel_cost)
                            <p class="text-sm text-gray-600">${{ number_format($latestReadings['fuel']->fuel_cost, 2) }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500">No fuel records yet</p>
                @endif
            </div>

            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-medium text-gray-700">Last Odometer</h4>
                    <button wire:click="toggleQuickAction('odometer')" 
                            class="text-blue-600 hover:text-blue-700 text-sm">
                        Add New
                    </button>
                </div>
                @if($latestReadings['odometer'])
                    <div class="space-y-2">
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($latestReadings['odometer']->odometer_reading) }} km</p>
                        <p class="text-sm text-gray-500">{{ $latestReadings['odometer']->recorded_at->format('M j, Y H:i') }}</p>
                        @if($latestReadings['odometer']->distance_traveled)
                            <p class="text-sm text-gray-600">+{{ number_format($latestReadings['odometer']->distance_traveled) }} km</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500">No odometer records yet</p>
                @endif
            </div>

            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-medium text-gray-700">Last Maintenance</h4>
                    <button wire:click="toggleQuickAction('maintenance')" 
                            class="text-blue-600 hover:text-blue-700 text-sm">
                        Add New
                    </button>
                </div>
                @if($latestReadings['maintenance'])
                    <div class="space-y-2">
                        <p class="text-lg font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $latestReadings['maintenance']->maintenance_type)) }}</p>
                        <p class="text-sm text-gray-500">{{ $latestReadings['maintenance']->performed_at->format('M j, Y') }}</p>
                        @if($latestReadings['maintenance']->cost)
                            <p class="text-sm text-gray-600">${{ number_format($latestReadings['maintenance']->cost, 2) }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500">No maintenance records yet</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button wire:click="setActiveTab('overview')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Overview
                </button>
                <button wire:click="setActiveTab('fuel')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'fuel' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Fuel Logs
                </button>
                <button wire:click="setActiveTab('odometer')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'odometer' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Odometer Logs
                </button>
                <button wire:click="setActiveTab('maintenance')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'maintenance' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Maintenance
                </button>
                <button wire:click="setActiveTab('bookings')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'bookings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Bookings
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    @if($activeTab === 'overview')
        <!-- Overview Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Vehicle Information -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicle Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Model</span>
                        <span class="text-sm font-medium">{{ $vehicle->model }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">License Plate</span>
                        <span class="text-sm font-medium">{{ $vehicle->license_plate }}</span>
                    </div>
                    @if($vehicle->make)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Make</span>
                            <span class="text-sm font-medium">{{ $vehicle->make }}</span>
                        </div>
                    @endif
                    @if($vehicle->year)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Year</span>
                            <span class="text-sm font-medium">{{ $vehicle->year }}</span>
                        </div>
                    @endif
                    @if($vehicle->color)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Color</span>
                            <span class="text-sm font-medium">{{ $vehicle->color }}</span>
                        </div>
                    @endif
                    @if($vehicle->engine_type)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Engine Type</span>
                            <span class="text-sm font-medium">{{ $vehicle->engine_type }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            {{ $vehicle->status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $vehicle->status === 'in_use' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $vehicle->status === 'maintenance' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $vehicle->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $vehicle->status ?? 'Unknown')) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Period Summary</h3>
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Fuel Statistics</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Total Fuel:</span>
                                <span class="font-medium ml-2">{{ number_format($vehicleStats['fuel_data']['total_fuel'] ?? 0, 1) }}L</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Avg Efficiency:</span>
                                <span class="font-medium ml-2">{{ number_format($vehicleStats['fuel_data']['average_efficiency'] ?? 0, 2) }} km/L</span>
                            </div>
                        </div>
                    </div>
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Distance Statistics</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Total Distance:</span>
                                <span class="font-medium ml-2">{{ number_format($vehicleStats['odometer_data']['total_distance'] ?? 0) }} km</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Avg Trip:</span>
                                <span class="font-medium ml-2">{{ number_format($vehicleStats['odometer_data']['average_trip_distance'] ?? 0, 1) }} km</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Usage & Cost</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Total Bookings:</span>
                                <span class="font-medium ml-2">{{ $vehicleStats['booking_stats']['total_bookings'] ?? 0 }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Cost per km:</span>
                                <span class="font-medium ml-2">${{ number_format($vehicleStats['maintenance_data']['cost_per_km'] ?? 0, 3) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($activeTab === 'fuel')
        <!-- Fuel Logs Table -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filled By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentFuelLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->filled_at->format('M j, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                    {{ number_format($log->fuel_amount, 2) }} L
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($log->fuel_cost)
                                        ${{ number_format($log->fuel_cost, 2) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($log->fuel_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->filledBy->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                    {{ $log->notes ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No fuel logs found for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $recentFuelLogs->links() }}
            </div>
        </div>
    @endif

    @if($activeTab === 'odometer')
        <!-- Odometer Logs Table -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reading</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentOdometerLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->recorded_at->format('M j, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                    {{ number_format($log->odometer_reading) }} km
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($log->distance_traveled)
                                        {{ number_format($log->distance_traveled) }} km
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $log->reading_type === 'start' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $log->reading_type === 'end' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $log->reading_type === 'manual' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $log->reading_type === 'service' ? 'bg-purple-100 text-purple-800' : '' }}">
                                        {{ ucfirst($log->reading_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->recordedBy->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                    {{ $log->notes ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No odometer logs found for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $recentOdometerLogs->links() }}
            </div>
        </div>
    @endif

    @if($activeTab === 'maintenance')
        <!-- Maintenance Logs Table -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Due</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentMaintenanceLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->performed_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst(str_replace('_', ' ', $log->maintenance_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                    {{ $log->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($log->cost)
                                        ${{ number_format($log->cost, 2) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($log->next_maintenance_due)
                                        {{ $log->next_maintenance_due->format('M j, Y') }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->recordedBy->name ?? 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No maintenance logs found for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $recentMaintenanceLogs->links() }}
            </div>
        </div>
    @endif

    @if($activeTab === 'bookings')
        <!-- Bookings Table -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentBookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->start_time?->format('M j') }} - {{ $booking->end_time?->format('M j') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $booking->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $booking->status === 'ongoing' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $booking->status === 'done' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $booking->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                    {{ $booking->purpose ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No bookings found for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $recentBookings->links() }}
            </div>
        </div>
    @endif

    <!-- Quick Action Modals would go here if needed -->
    @if($showQuickFuel)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-medium mb-4">Quick Fuel Entry</h3>
                <p class="text-sm text-gray-600 mb-4">
                    For full fuel management, visit the dedicated 
                    <a href="#" class="text-blue-600 hover:underline">Fuel Management</a> page.
                </p>
                <button wire:click="toggleQuickAction('')" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Close
                </button>
            </div>
        </div>
    @endif

    @if($showQuickOdometer)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-medium mb-4">Quick Odometer Entry</h3>
                <p class="text-sm text-gray-600 mb-4">
                    For full odometer management, visit the dedicated 
                    <a href="#" class="text-blue-600 hover:underline">Odometer Management</a> page.
                </p>
                <button wire:click="toggleQuickAction('')" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Close
                </button>
            </div>
        </div>
    @endif

    @if($showQuickMaintenance)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-medium mb-4">Quick Maintenance Entry</h3>
                <p class="text-sm text-gray-600 mb-4">
                    For full maintenance management, visit the dedicated 
                    <a href="#" class="text-blue-600 hover:underline">Maintenance Management</a> page.
                </p>
                <button wire:click="toggleQuickAction('')" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Close
                </button>
            </div>
        </div>
    @endif
</div>