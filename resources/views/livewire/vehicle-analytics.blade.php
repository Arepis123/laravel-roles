<div class="p-4 sm:p-6 bg-white dark:bg-zinc-800 min-h-screen">
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white dark:text-white">Vehicle Analytics Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Comprehensive insights and analytics for your vehicle fleet</p>
    </div>

    <!-- Date Range Filters -->
    <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                    <input type="date" wire:model.live="dateTo" class="w-full border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vehicle</label>
                    <select wire:model.live="selectedVehicle" class="w-full border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400">
                        <option value="">All Vehicles</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->model }} ({{ $vehicle->license_plate }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <button wire:click="exportAnalytics('excel')" 
                        class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white px-4 py-2 rounded-lg w-full sm:w-auto mb-2 sm:mb-0 sm:mr-2">
                    Export Excel
                </button>
                <button wire:click="exportAnalytics('pdf')" 
                        class="bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white px-4 py-2 rounded-lg w-full sm:w-auto">
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Analytics Type Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-zinc-700">
            <nav class="-mb-px flex flex-wrap gap-2 sm:space-x-8">
                <button wire:click="setAnalyticsType('overview')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $analyticsType === 'overview' ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-zinc-600' }}">
                    Fleet Overview
                </button>
                <button wire:click="setAnalyticsType('fuel')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $analyticsType === 'fuel' ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-zinc-600' }}">
                    Fuel Analytics
                </button>
                <button wire:click="setAnalyticsType('odometer')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $analyticsType === 'odometer' ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-zinc-600' }}">
                    Odometer Analytics
                </button>
                <button wire:click="setAnalyticsType('maintenance')" 
                        class="py-2 px-1 border-b-2 font-medium text-sm {{ $analyticsType === 'maintenance' ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-zinc-600' }}">
                    Maintenance Analytics
                </button>
            </nav>
        </div>
    </div>

    <!-- Fleet Overview -->
    @if($analyticsType === 'overview')
        <!-- Fleet Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Vehicles</h3>
                        <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ $fleetOverview['total_vehicles'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Vehicles</h3>
                        <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ $fleetOverview['active_vehicles'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Distance</h3>
                        <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($fleetOverview['total_distance_traveled']) }} km</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Fuel Cost</h3>
                        <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($fleetOverview['total_fuel_cost'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Fleet Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Fuel Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Fuel Consumed</span>
                        <span class="text-sm font-medium">{{ number_format($fleetOverview['total_fuel_consumed'], 2) }} L</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Fuel Cost</span>
                        <span class="text-sm font-medium">${{ number_format($fleetOverview['total_fuel_cost'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Average Cost/Liter</span>
                        <span class="text-sm font-medium">
                            ${{ $fleetOverview['total_fuel_consumed'] > 0 ? number_format($fleetOverview['total_fuel_cost'] / $fleetOverview['total_fuel_consumed'], 3) : '0.00' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Maintenance Overview</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Maintenance Cost</span>
                        <span class="text-sm font-medium">${{ number_format($fleetOverview['total_maintenance_cost'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Needs Maintenance Soon</span>
                        <span class="text-sm font-medium text-yellow-600">{{ $fleetOverview['vehicles_needing_maintenance'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Overdue Maintenance</span>
                        <span class="text-sm font-medium text-red-600">{{ $fleetOverview['overdue_maintenance'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Fleet Efficiency</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Avg Distance/Vehicle</span>
                        <span class="text-sm font-medium">
                            {{ $fleetOverview['total_vehicles'] > 0 ? number_format($fleetOverview['total_distance_traveled'] / $fleetOverview['total_vehicles'], 1) : '0' }} km
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Cost per km</span>
                        <span class="text-sm font-medium">
                            ${{ $fleetOverview['total_distance_traveled'] > 0 ? number_format(($fleetOverview['total_fuel_cost'] + $fleetOverview['total_maintenance_cost']) / $fleetOverview['total_distance_traveled'], 3) : '0.00' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Fleet Utilization</span>
                        <span class="text-sm font-medium">
                            {{ $fleetOverview['total_vehicles'] > 0 ? number_format(($fleetOverview['active_vehicles'] / $fleetOverview['total_vehicles']) * 100, 1) : '0' }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performing Vehicles -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Top Performing Vehicles</h3>
                <p class="text-sm text-gray-600">Based on fuel efficiency and utilization</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vehicle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Efficiency (km/L)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Distance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bookings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cost/km</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topPerformingVehicles as $vehicleData)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $vehicleData['vehicle']->model }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $vehicleData['vehicle']->license_plate }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ number_format($vehicleData['efficiency'], 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ number_format($vehicleData['distance']) }} km
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $vehicleData['utilization'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    ${{ number_format($vehicleData['cost_per_km'], 3) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No performance data available for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Fuel Analytics -->
    @if($analyticsType === 'fuel' && $selectedVehicle && $fuelAnalytics)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Fuel</h3>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($fuelAnalytics['total_fuel'], 2) }} L</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Cost</h3>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($fuelAnalytics['total_cost'], 2) }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Sessions</h3>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ $fuelAnalytics['fuel_sessions'] }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Efficiency</h3>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($fuelAnalytics['average_efficiency'], 2) }} km/L</p>
            </div>
        </div>

        <!-- Fuel Logs -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Fuel Logs</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Filled By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($fuelAnalytics['logs'] as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->filled_at->format('M j, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ number_format($log->fuel_amount, 2) }} L
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    ${{ number_format($log->fuel_cost, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ ucfirst($log->fuel_type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->filledBy->name ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Odometer Analytics -->
    @if($analyticsType === 'odometer' && $selectedVehicle && $odometerAnalytics)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Distance</h3>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($odometerAnalytics['total_distance']) }} km</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Readings Count</h3>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ $odometerAnalytics['readings_count'] }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Distance/Trip</h3>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($odometerAnalytics['average_distance'], 1) }} km</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Odometer Range</h3>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ number_format($odometerAnalytics['odometer_range']['min']) }} - {{ number_format($odometerAnalytics['odometer_range']['max']) }} km
                </p>
            </div>
        </div>

        <!-- Odometer Logs -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Odometer Readings</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reading</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Distance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recorded By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($odometerAnalytics['logs'] as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->recorded_at->format('M j, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                    {{ number_format($log->odometer_reading) }} km
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->distance_traveled ? number_format($log->distance_traveled) . ' km' : '-' }}
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->recordedBy->name ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Maintenance Analytics -->
    @if($analyticsType === 'maintenance' && $selectedVehicle && $maintenanceAnalytics)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Cost</h3>
                <p class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($maintenanceAnalytics['total_cost'], 2) }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Upcoming Maintenance</h3>
                <p class="text-xl sm:text-2xl font-semibold text-yellow-600">{{ count($maintenanceAnalytics['upcoming_maintenance']) }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Overdue Maintenance</h3>
                <p class="text-xl sm:text-2xl font-semibold text-red-600">{{ count($maintenanceAnalytics['overdue_maintenance']) }}</p>
            </div>
        </div>

        <!-- Maintenance by Type -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Maintenance by Type</h3>
                <div class="space-y-3">
                    @foreach($maintenanceAnalytics['maintenance_count'] as $type => $count)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                            <span class="text-sm font-medium">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-4 sm:p-6 rounded-lg shadow-sm dark:shadow-zinc-700 border border-gray-200 dark:border-zinc-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Maintenance Status</h3>
                <div class="space-y-4">
                    @if(count($maintenanceAnalytics['upcoming_maintenance']) > 0)
                        <div>
                            <h4 class="text-sm font-medium text-yellow-600 mb-2">Upcoming ({{ count($maintenanceAnalytics['upcoming_maintenance']) }})</h4>
                            @foreach($maintenanceAnalytics['upcoming_maintenance'] as $maintenance)
                                <div class="text-sm text-gray-600">
                                    {{ $maintenance->description }} - Due {{ $maintenance->next_maintenance_due->format('M j, Y') }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(count($maintenanceAnalytics['overdue_maintenance']) > 0)
                        <div>
                            <h4 class="text-sm font-medium text-red-600 mb-2">Overdue ({{ count($maintenanceAnalytics['overdue_maintenance']) }})</h4>
                            @foreach($maintenanceAnalytics['overdue_maintenance'] as $maintenance)
                                <div class="text-sm text-gray-600">
                                    {{ $maintenance->description }} - Due {{ $maintenance->next_maintenance_due->format('M j, Y') }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Maintenance -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Maintenance Records</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Next Due</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($maintenanceAnalytics['logs'] as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->performed_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst(str_replace('_', ' ', $log->maintenance_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white max-w-xs truncate">
                                    {{ $log->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    ${{ number_format($log->cost, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->next_maintenance_due?->format('M j, Y') ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Empty State for Vehicle-Specific Analytics -->
    @if(in_array($analyticsType, ['fuel', 'odometer', 'maintenance']) && !$selectedVehicle)
        <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Select a Vehicle</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose a vehicle from the dropdown above to view detailed {{ $analyticsType }} analytics.</p>
        </div>
    @endif
</div>
