<div class="relative mb-6 w-full">
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white dark:text-white">Vehicle Analytics Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Comprehensive insights and analytics for company vehicles</p>
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
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>From Date</flux:label>
                                <flux:date-picker wire:model.live="dateFrom" with-today/>
                            </flux:field>

                            <flux:field>
                                <flux:label>To Date</flux:label>
                                <flux:date-picker wire:model.live="dateTo" with-today/>
                            </flux:field>

                            <flux:field>
                                <flux:label>Vehicle</flux:label>
                                <flux:select variant="listbox" wire:model.live="selectedVehicle" placeholder="All Vehicles">
                                    @foreach($vehicles as $vehicle)
                                        <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->model }} ({{ $vehicle->plate_number }})</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>
                        </div>

                        <!-- Export Buttons -->
                        <div class="flex gap-3 pt-2 border-t border-gray-200 dark:border-zinc-700">
                            <flux:button variant="filled" size="sm" wire:click="exportAnalytics('excel')" icon="document-arrow-down" class="bg-green-600 hover:bg-green-700">
                                Export Excel
                            </flux:button>
                            <flux:button variant="filled" size="sm" wire:click="exportAnalytics('pdf')" icon="document-arrow-down" class="bg-red-600 hover:bg-red-700">
                                Export PDF
                            </flux:button>
                        </div>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </div>

    <!-- Analytics Type Tabs -->
    <div class="mb-6">
        <flux:tab.group>
            <flux:tabs class="px-4 min-w-max" wire:model.live="analyticsType">
                <flux:tab name="overview" wire:click="setAnalyticsType('overview')">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="sm:hidden">Overview</span>
                        <span class="hidden sm:inline">Vehicle Overview</span>
                    </span>
                </flux:tab>
                <flux:tab name="fuel" wire:click="setAnalyticsType('fuel')">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <line stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" x1="3" x2="15" y1="22" y2="22"/>
                            <line stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" x1="4" x2="14" y1="9" y2="9"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 13h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2a2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 5"/>
                        </svg>
                        <span class="sm:hidden">Fuel</span>
                        <span class="hidden sm:inline">Fuel Analytics</span>
                    </span>
                </flux:tab>
                <flux:tab name="odometer" wire:click="setAnalyticsType('odometer')">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m12 14 4-4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3.34 19a10 10 0 1 1 17.32 0"/>
                        </svg>
                        <span class="sm:hidden">Odometer</span>
                        <span class="hidden sm:inline">Odometer Analytics</span>
                    </span>
                </flux:tab>
                <flux:tab name="maintenance" wire:click="setAnalyticsType('maintenance')">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 lucide lucide-wrench-icon lucide-wrench hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z"/>
                        </svg>
                        <span class="sm:hidden">Maintenance</span>
                        <span class="hidden sm:inline">Maintenance Analytics</span>
                    </span>
                </flux:tab>
            </flux:tabs>
        </flux:tab.group>
    </div>

    <!-- Vehicle Overview -->
    @if($analyticsType === 'overview')
        <!-- Vehicle Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/>
                            <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="7" cy="17" r="2"/><path d="M9 17h6"/>
                            <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="17" cy="17" r="2"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Vehicles</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $fleetOverview['total_vehicles'] }}</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Active Vehicles</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $fleetOverview['active_vehicles'] }}</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Distance</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($fleetOverview['total_distance_traveled']) }} km</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="12" cy="12" r="10"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18V6"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Fuel Cost</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">RM{{ number_format($fleetOverview['total_fuel_cost'], 2) }}</flux:text>
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Additional Vehicle Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex items-center justify-between mb-4">
                    <flux:heading>Fuel Statistics</flux:heading>
                    @if($fleetOverview['fuel_by_type']->count() > 0)
                        <button wire:click="toggleFuelDetails" 
                                class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium flex items-center">
                            @if($showFuelDetails)
                                View Less
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            @else
                                View More
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            @endif
                        </button>
                    @endif
                </div>
                
                <div class="space-y-3">
                    <!-- Always visible: Overall Totals -->
                    <div class="flex justify-between">
                        <flux:text class="text-gray-600 dark:text-gray-400 font-medium">Total Consumed</flux:text>
                        <flux:text class="font-semibold text-gray-900 dark:text-white">{{ number_format($fleetOverview['total_fuel_consumed'], 2) }} L</flux:text>
                    </div>
                    <div class="flex justify-between">
                        <flux:text class="text-gray-600 dark:text-gray-400 font-medium">Total Cost</flux:text>
                        <flux:text class="font-semibold text-gray-900 dark:text-white">RM{{ number_format($fleetOverview['total_fuel_cost'], 2) }}</flux:text>
                    </div>
                    
                    <!-- Expandable: Breakdown by Fuel Type -->
                    @if($showFuelDetails)
                        <div class="pt-3 border-t border-gray-200 dark:border-zinc-600 space-y-4">
                            @forelse($fleetOverview['fuel_by_type'] as $fuelType => $stats)
                                <div class="space-y-2">
                                    <flux:heading class="text-gray-800 dark:text-gray-200 capitalize flex items-center">
                                        @if($fuelType === 'petrol')
                                            <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                                        @elseif($fuelType === 'diesel')
                                            <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                        @else
                                            <div class="w-3 h-3 rounded-full bg-gray-500 mr-2"></div>
                                        @endif
                                        {{ $fuelType }}
                                    </flux:heading>
                                    <div class="text-xs space-y-1 ml-5">
                                        <div class="flex justify-between">
                                            <flux:text size="xs" class="text-gray-600 dark:text-gray-400">Amount</flux:text>
                                            <flux:text size="xs" class="text-gray-900 dark:text-white font-medium">{{ number_format($stats->total_amount, 1) }} L</flux:text>
                                        </div>
                                        <div class="flex justify-between">
                                            <flux:text size="xs" class="text-gray-600 dark:text-gray-400">Cost</flux:text>
                                            <flux:text size="xs" class="text-gray-900 dark:text-white font-medium">RM{{ number_format($stats->total_cost, 2) }}</flux:text>
                                        </div>
                                        <div class="flex justify-between">
                                            <flux:text size="xs" class="text-gray-600 dark:text-gray-400">Avg/L</flux:text>
                                            <flux:text size="xs" class="text-gray-900 dark:text-white font-medium">RM{{ number_format($stats->avg_cost_per_liter, 3) }}</flux:text>
                                        </div>
                                        <div class="flex justify-between">
                                            <flux:text size="xs" class="text-gray-600 dark:text-gray-400">Fill Sessions</flux:text>
                                            <flux:text size="xs" class="text-gray-900 dark:text-white font-medium">{{ $stats->fill_count }}</flux:text>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 dark:text-gray-400 text-sm py-4">
                                    No fuel breakdown data available
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <flux:heading class="mb-4">Maintenance Overview</flux:heading>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <flux:text class="text-gray-600 dark:text-gray-400">Total Maintenance Cost</flux:text>
                        <flux:text class="font-medium text-gray-900 dark:text-white">RM{{ number_format($fleetOverview['total_maintenance_cost'], 2) }}</flux:text>
                    </div>
                    <div class="flex justify-between">
                        <flux:text class="text-gray-600 dark:text-gray-400">Needs Maintenance Soon</flux:text>
                        <flux:text class="font-medium text-yellow-600">{{ $fleetOverview['vehicles_needing_maintenance'] }}</flux:text>
                    </div>
                    <div class="flex justify-between">
                        <flux:text class="text-gray-600 dark:text-gray-400">Overdue Maintenance</flux:text>
                        <flux:text class="font-medium text-red-600">{{ $fleetOverview['overdue_maintenance'] }}</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <flux:heading class="mb-4">Vehicle Efficiency</flux:heading>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Avg Distance/Vehicle</span>
                            <flux:tooltip toggleable>
                                <flux:button size="xs" variant="ghost" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                    <svg width="15" height="15" viewBox="0 0 24 23" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M12 16v-4"/>
                                        <path d="M12 8h.01"/>
                                    </svg>
                                </flux:button>
                                <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                    <p><strong>Average kilometers traveled per vehicle</strong> in the selected date range.</p>
                                    <p><em>Calculation:</em> Total distance traveled ÷ Total vehicles</p>
                                    <p>Low values indicate vehicles aren't being used much. Higher values suggest better vehicle utilization.</p>
                                </flux:tooltip.content>
                            </flux:tooltip>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $fleetOverview['total_vehicles'] > 0 ? number_format($fleetOverview['total_distance_traveled'] / $fleetOverview['total_vehicles'], 1) : '0' }} km
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Cost per km</span>
                            <flux:tooltip toggleable>
                                <flux:button size="xs" variant="ghost" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                    <svg width="15" height="15" viewBox="0 0 24 23" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M12 16v-4"/>
                                        <path d="M12 8h.01"/>
                                    </svg>
                                </flux:button>
                                <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                    <p><strong>Combined operational cost per kilometer</strong> including both fuel and maintenance expenses.</p>
                                    <p><em>Calculation:</em> (Total fuel cost + Total maintenance cost) ÷ Total distance traveled</p>
                                    <p>Lower values indicate better cost efficiency. High values suggest expensive vehicle operations relative to usage.</p>
                                </flux:tooltip.content>
                            </flux:tooltip>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            RM{{ $fleetOverview['total_distance_traveled'] > 0 ? number_format(($fleetOverview['total_fuel_cost'] + $fleetOverview['total_maintenance_cost']) / $fleetOverview['total_distance_traveled'], 3) : '0.00' }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Vehicle Utilization</span>
                            <flux:tooltip toggleable>
                                <flux:button size="xs" variant="ghost" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                    <svg width="15" height="15" viewBox="0 0 24 23" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M12 16v-4"/>
                                        <path d="M12 8h.01"/>
                                    </svg>
                                </flux:button>
                                <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                    <p><strong>Percentage of vehicles actively used</strong> during the selected period.</p>
                                    <p><em>Calculation:</em> (Active vehicles ÷ Total vehicles) × 100</p>
                                    <p>Higher percentages indicate better vehicle utilization. Low values suggest many vehicles are sitting idle and not being booked.</p>
                                </flux:tooltip.content>
                            </flux:tooltip>
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $fleetOverview['total_vehicles'] > 0 ? number_format(($fleetOverview['active_vehicles'] / $fleetOverview['total_vehicles']) * 100, 1) : '0' }}%
                        </span>
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Top Performing Vehicles -->
        <flux:card class="dark:bg-zinc-900">
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <div class="flex items-center gap-0">
                    <flux:heading>Top Performing Vehicles</flux:heading>
                    <flux:tooltip toggleable>
                        <flux:button size="xs" variant="ghost"
                            class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                            <svg width="15" height="15" viewBox="0 0 24 23" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                        </flux:button>
                        <flux:tooltip.content class="max-w-[20rem] space-y-2 text-left">
                            <p>The stats help identify which vehicles are most cost-effective to operate and getting the best usage rates.</p>                           
                        </flux:tooltip.content>
                    </flux:tooltip>
                </div>               
                <flux:text class="text-gray-600 dark:text-gray-400">Based on fuel efficiency and utilization</flux:text>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vehicle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Efficiency (km/L)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Distance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bookings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cost/km</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                        @forelse($topPerformingVehicles as $vehicleData)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                                <td class="px-6 py-4 whitespace-nowrap">                                    
                                    <span class="text-sm font-medium text-gray-900 dark:text-neutral-200">{{$loop->iteration}}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">                                    
                                    <span class="text-sm font-medium text-gray-900 dark:text-neutral-200 mt-1">{{ $vehicleData['vehicle']->model }}</span>
                                    <span class="text-sm text-gray-500 font-normal">({{ $vehicleData['vehicle']->plate_number }})</span>                                 
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                    @if($vehicleData['efficiency'] > 0)
                                        {{ number_format($vehicleData['efficiency'], 2) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                    @if($vehicleData['distance'] > 0)
                                        {{ number_format($vehicleData['distance']) }} km
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $bookingCount = $vehicleData['utilization'];
                                        $badgeColor = match(true) {
                                            $bookingCount >= 10 => 'lime',
                                            $bookingCount >= 5 => 'amber',
                                            $bookingCount >= 1 => 'sky',
                                            default => 'zinc'
                                        };
                                    @endphp
                                    <flux:badge size="sm" color="{{ $badgeColor }}">{{ $bookingCount }} bookings</flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                    @if($vehicleData['cost_per_km'] > 0)
                                        RM{{ number_format($vehicleData['cost_per_km'], 3) }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
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
        </flux:card>
    @endif

    <!-- Fuel Analytics -->
    @if($analyticsType === 'fuel' && $selectedVehicle && $fuelAnalytics)
        <!-- Fuel Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <line stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" x1="3" x2="15" y1="22" y2="22"/>
                            <line stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" x1="4" x2="14" y1="9" y2="9"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 13h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2a2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 5"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Fuel</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($fuelAnalytics['total_fuel'], 2) }} L</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="12" cy="12" r="10"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18V6"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Cost</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">RM{{ number_format($fuelAnalytics['total_cost'], 2) }}</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Sessions</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $fuelAnalytics['fuel_sessions'] }}</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Avg Efficiency</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($fuelAnalytics['average_efficiency'], 2) }} km/L</flux:text>
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Fuel Logs -->
        <flux:card class="dark:bg-zinc-900">
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <flux:heading>Recent Fuel Logs</flux:heading>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Filled By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach($fuelAnalytics['logs'] as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->filled_at->format('M j, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                    {{ number_format($log->fuel_amount, 2) }} L
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                    RM{{ number_format($log->fuel_cost, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:badge size="sm" color="sky">{{ ucfirst($log->fuel_type) }}</flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->filledBy->name ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </flux:card>
    @endif

    <!-- Odometer Analytics -->
    @if($analyticsType === 'odometer' && $selectedVehicle && $odometerAnalytics)
        <!-- Odometer Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m12 14 4-4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3.34 19a10 10 0 1 1 17.32 0"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Distance</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($odometerAnalytics['total_distance']) }} km</flux:text>
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
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Readings Count</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $odometerAnalytics['readings_count'] }}</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Avg Distance/Trip</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($odometerAnalytics['average_distance'], 1) }} km</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Odometer Range</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ number_format($odometerAnalytics['odometer_range']['min']) }} - {{ number_format($odometerAnalytics['odometer_range']['max']) }} km
                        </flux:text>
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Odometer Logs -->
        <flux:card class="dark:bg-zinc-900">
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <flux:heading>Recent Odometer Readings</flux:heading>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reading</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Distance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Recorded By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach($odometerAnalytics['logs'] as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->recorded_at->format('M j, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                    {{ number_format($log->odometer_reading) }} km
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                    {{ $log->distance_traveled ? number_format($log->distance_traveled) . ' km' : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $badgeColor = match($log->reading_type) {
                                            'start' => 'lime',
                                            'end' => 'rose',
                                            'manual' => 'sky',
                                            'service' => 'amber',
                                            default => 'zinc'
                                        };
                                    @endphp
                                    <flux:badge size="sm" color="{{ $badgeColor }}">{{ ucfirst($log->reading_type) }}</flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->recordedBy->name ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </flux:card>
    @endif

    <!-- Maintenance Analytics -->
    @if($analyticsType === 'maintenance' && $selectedVehicle && $maintenanceAnalytics)
        <!-- Maintenance Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="12" cy="12" r="10"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18V6"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Cost</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">RM{{ number_format($maintenanceAnalytics['total_cost'], 2) }}</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Upcoming Maintenance</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ count($maintenanceAnalytics['upcoming_maintenance']) }}</flux:text>
                    </div>
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                    <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg hidden sm:block">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-0 sm:ml-4">
                        <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Overdue Maintenance</flux:heading>
                        <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ count($maintenanceAnalytics['overdue_maintenance']) }}</flux:text>
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Maintenance by Type -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <flux:heading class="mb-4">Maintenance by Type</flux:heading>
                <div class="space-y-3">
                    @foreach($maintenanceAnalytics['maintenance_count'] as $type => $count)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </flux:card>

            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
                <flux:heading class="mb-4">Maintenance Status</flux:heading>
                <div class="space-y-4">
                    @if(count($maintenanceAnalytics['upcoming_maintenance']) > 0)
                        <div>
                            <h4 class="text-sm font-medium text-yellow-600 mb-2">Upcoming ({{ count($maintenanceAnalytics['upcoming_maintenance']) }})</h4>
                            @foreach($maintenanceAnalytics['upcoming_maintenance'] as $maintenance)
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $maintenance->description }} - Due {{ $maintenance->next_maintenance_due->format('M j, Y') }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(count($maintenanceAnalytics['overdue_maintenance']) > 0)
                        <div>
                            <h4 class="text-sm font-medium text-red-600 mb-2">Overdue ({{ count($maintenanceAnalytics['overdue_maintenance']) }})</h4>
                            @foreach($maintenanceAnalytics['overdue_maintenance'] as $maintenance)
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $maintenance->description }} - Due {{ $maintenance->next_maintenance_due->format('M j, Y') }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>

        <!-- Recent Maintenance -->
        <flux:card class="dark:bg-zinc-900">
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <flux:heading>Recent Maintenance Records</flux:heading>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Next Due</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach($maintenanceAnalytics['logs'] as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->performed_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:badge size="sm" color="sky">{{ ucfirst(str_replace('_', ' ', $log->maintenance_type)) }}</flux:badge>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white max-w-xs truncate">
                                    {{ $log->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                    RM{{ number_format($log->cost, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $log->next_maintenance_due?->format('M j, Y') ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </flux:card>
    @endif

    <!-- Empty State for Vehicle-Specific Analytics -->
    @if(in_array($analyticsType, ['fuel', 'odometer', 'maintenance']) && !$selectedVehicle)
        <flux:card class="p-12 text-center dark:bg-zinc-900">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/>
                <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="7" cy="17" r="2"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h6"/>
                <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="17" cy="17" r="2"/>
            </svg>        
            <flux:heading class="mt-2">Select a Vehicle</flux:heading>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose a vehicle from the dropdown above to view detailed {{ $analyticsType }} analytics.</p>
        </flux:card>
    @endif

    @script
    <script>
        $wire.on('analytics-export', (data) => {
            const params = new URLSearchParams({
                vehicle_id: data.vehicle_id || '',
                date_from: data.date_from || '',
                date_to: data.date_to || '',
                analytics_type: data.analytics_type || 'overview',
                format: data.format || 'excel'
            });

            // Create export URL
            const exportUrl = `/vehicle-analytics/export?${params}`;
            
            // Debug logging
            console.log('Export data received:', data);
            console.log('Constructed URL:', exportUrl);
            console.log('Format parameter:', data.format);
            
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
