<x-layouts.app :title="__('Dashboard')">
    @php
        $userRole = auth()->user()->getRoleNames()->first();
        $isAdminRole = in_array($userRole, ['Super Admin', 'Admin']);
    @endphp
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Custom Schedule-X styles to match your design */
        .sx__calendar-wrapper {
            font-family: Inter, sans-serif, 'Instrument Sans';
            height: 100%;
        }

        /* Custom button styles for Schedule-X */
        .sx__view-selection-item,
        .sx__date-picker__chevron-wrapper {
            border-radius: 8px;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .sx__view-selection-item {
            padding: 5px 12px;
            background-color: #F4F4F4;
            color: rgb(65, 65, 69);
            font-size: 14px;
            font-weight: 500;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .sx__view-selection-item--active {
            background-color: #2563eb !important;
            color: white !important;
            border-color: #2563eb !important;
        }

        .sx__view-selection-item:hover:not(.sx__view-selection-item--active) {
            background-color: #e5e7eb;
        }

        /* Ensure view selector is visible and clickable */
        .sx__view-selection {
            display: flex;
            gap: 4px;
            z-index: 10;
        }

        /* Fix calendar height */
        #calendar {
            min-height: 600px;
        }

        /* Dark mode support */
        .dark .sx__calendar-wrapper {
            background-color: rgb(38, 38, 38);
            color: white;
        }

        .dark .sx__month-grid-day {
            background-color: rgb(38, 38, 38);
            border-color: rgb(64, 64, 64);
        }

        .dark .sx__month-grid-day:hover {
            background-color: rgb(51, 51, 51);
        }

        .dark .sx__calendar-header {
            background-color: rgb(38, 38, 38);
            border-color: rgb(64, 64, 64);
        }

        /* Custom event tooltip */
        .booking-tooltip {
            position: absolute;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            min-width: 250px;
            display: none;
        }

        .dark .booking-tooltip {
            background: rgb(38, 38, 38);
            border-color: rgb(64, 64, 64);
            color: white;
        }

        /* Loading overlay */
        .calendar-loading {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }

        .dark .calendar-loading {
            background: rgba(0, 0, 0, 0.9);
        }

        .calendar-loading.hidden {
            display: none;
        }
        
        /* Smooth transitions for view switching */
        .view-transition {
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        }
        
        .view-hidden {
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
            position: absolute;
            width: 100%;
        }
        
        .view-visible {
            opacity: 1;
            transform: translateY(0);
            position: relative;
        }
    </style>

    <!-- Header Section -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Dashboard') }}</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('Monitor and manage all bookings here') }}</p>
    </div>    
    
    <div x-data="{ currentView: 'calendar' }" class="space-y-4 my-4">
        <!-- Radio Group Toggle - Desktop (hidden on mobile) -->
        <div class="hidden sm:flex justify-end">
            <flux:radio.group variant="segmented" x-model="currentView" class="">
                <flux:radio value="stats" icon="chart-bar-square">{{ __('Stats') }}</flux:radio>
                <flux:radio value="calendar" icon="calendar-days">{{ __('Calendar') }}</flux:radio>
            </flux:radio.group>
        </div>

        <!-- Radio Group Toggle - Mobile (hidden on desktop) -->
        <div class="sm:hidden">
            <flux:radio.group variant="segmented" x-model="currentView" class="w-full">
                <flux:radio value="stats" icon="chart-bar-square">{{ __('Stats') }}</flux:radio>
                <flux:radio value="calendar" icon="calendar-days">{{ __('Calendar') }}</flux:radio>
            </flux:radio.group>
        </div>

        <!-- Custom Booking Tooltip -->
        <div id="bookingTooltip" class="booking-tooltip">
            <!-- Content will be populated by JavaScript -->
        </div>

        <!-- Stats View -->
        <div x-show="currentView === 'stats'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             class="flex h-full w-full flex-1 flex-col gap-3 rounded-xl">
            
            <!-- Stats Cards - First Row -->
            <div class="grid auto-rows-min gap-3 md:grid-cols-3">
                <!-- Total Bookings Card -->
                <div class="relative overflow-hidden rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="p-4 flex flex-col justify-betweenh-auto">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Total Bookings</h3>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1" id="totalBookings">{{ App\Models\Booking::count() }}</p>
                        </div>
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            All time
                        </div>
                    </div>
                </div>

                <!-- Pending Approvals Card -->
                <div class="relative overflow-hidden rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="p-4 flex flex-col justify-betweenh-auto">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Pending Bookings</h3>
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1" id="pendingBookings">{{ App\Models\Booking::pending()->count() }}</p>
                        </div>
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Awaiting approval
                        </div>
                    </div>
                </div>

                <!-- Today's Bookings Card -->
                <div class="relative overflow-hidden rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="p-4 flex flex-col justify-betweenh-auto">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Today's Bookings</h3>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1" id="todayBookings">{{ App\Models\Booking::whereDate('start_time', today())->count() }}</p>
                        </div>
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Active today
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards - Second Row -->
            <div class="grid auto-rows-min gap-3 md:grid-cols-3">
                <!-- This Week's Bookings Card -->
                <div class="relative overflow-hidden rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="p-4 flex flex-col justify-betweenh-auto">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">This Week</h3>
                            <div class="flex items-baseline gap-2 mt-1">
                                <p class="text-2xl font-bold text-teal-600 dark:text-teal-400" id="weekBookings">
                                    {{ App\Models\Booking::whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()])->count() }}
                                </p>
                                @php
                                    $thisWeek = App\Models\Booking::whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()])->count();
                                    $lastWeek = App\Models\Booking::whereBetween('start_time', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
                                    $percentChange = $lastWeek > 0 ? round((($thisWeek - $lastWeek) / $lastWeek) * 100) : 0;
                                @endphp
                                @if($percentChange != 0)
                                    <span class="text-xs font-medium {{ $percentChange > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $percentChange > 0 ? '↑' : '↓' }} {{ abs($percentChange) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            vs last week
                        </div>
                    </div>
                </div>

                <!-- Utilization Rate Card -->
                <div class="relative overflow-hidden rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="p-4 flex flex-col justify-betweenh-auto">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Utilization Rate</h3>
                            @php
                                // Count total assets from all three models
                                $totalVehicles = App\Models\Vehicle::count();
                                $totalMeetingRooms = App\Models\MeetingRoom::count();
                                $totalItAssets = App\Models\ItAsset::count();
                                $totalAssets = $totalVehicles + $totalMeetingRooms + $totalItAssets;
                                
                                // Count distinct assets that have approved/done bookings today
                                $bookedVehicles = App\Models\Booking::whereDate('start_time', '<=', today())
                                    ->whereDate('end_time', '>=', today())
                                    ->where('asset_type', 'App\Models\Vehicle')
                                    ->whereIn('status', ['approved', 'done'])
                                    ->distinct('asset_id')
                                    ->count('asset_id');
                                    
                                $bookedMeetingRooms = App\Models\Booking::whereDate('start_time', '<=', today())
                                    ->whereDate('end_time', '>=', today())
                                    ->where('asset_type', 'App\Models\MeetingRoom')
                                    ->whereIn('status', ['approved', 'done'])
                                    ->distinct('asset_id')
                                    ->count('asset_id');
                                    
                                $bookedItAssets = App\Models\Booking::whereDate('start_time', '<=', today())
                                    ->whereDate('end_time', '>=', today())
                                    ->where('asset_type', 'App\Models\ItAsset')
                                    ->whereIn('status', ['approved', 'done'])
                                    ->distinct('asset_id')
                                    ->count('asset_id');
                                    
                                $bookedToday = $bookedVehicles + $bookedMeetingRooms + $bookedItAssets;
                                
                                $utilizationRate = $totalAssets > 0 ? round(($bookedToday / $totalAssets) * 100) : 0;
                            @endphp
                            <div class="mt-1 flex items-center justify-between">
                                <div class="flex items-baseline gap-1">
                                    <p class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ $utilizationRate }}%</p>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $bookedToday }}/{{ $totalAssets }}</span>
                                </div>
                                <!-- Mini progress bar -->
                                <div class="w-auto bg-gray-200 rounded-full h-1.5 dark:bg-gray-700">
                                    <div class="bg-purple-600 h-1.5 rounded-full dark:bg-purple-400 transition-all duration-300" style="width: {{ $utilizationRate }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Assets in use today
                        </div>
                    </div>
                </div>

                <!-- Most Booked Asset Card -->
                <div class="relative overflow-hidden rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="p-4 flex flex-col justify-betweenh-auto">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Most Booked</h3>
                            @php
                                $mostBooked = App\Models\Booking::select('asset_id', 'asset_type', DB::raw('count(*) as total'))
                                    ->whereBetween('start_time', [now()->startOfMonth(), now()->endOfMonth()])
                                    ->groupBy('asset_id', 'asset_type')
                                    ->orderBy('total', 'desc')
                                    ->first();
                                
                                $assetName = 'No bookings';
                                $bookingCount = 0;
                                
                                if ($mostBooked && $mostBooked->asset) {
                                    $assetName = $mostBooked->asset->name ?? $mostBooked->asset->model ?? 'Unknown';
                                    $bookingCount = $mostBooked->total;
                                }
                            @endphp
                            <div class="mt-1">
                                <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400 truncate" title="{{ $assetName }}">
                                    {{ Str::limit($assetName, 25) }}
                                </p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $bookingCount }} <span class="text-xs font-normal text-gray-600 dark:text-gray-400">bookings</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                            This month
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Trends Chart -->
            <div class="mt-6">
                <div class="relative overflow-hidden rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="p-6" x-data="chartController()">
                        <!-- Header with Month Selector -->
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Booking Trends</h3>
                            
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Month:</span>
                                <flux:select 
                                    variant="listbox" 
                                    searchable
                                    x-model="selectedMonth" 
                                    @change="updateChart()" 
                                    class="w-48"
                                    placeholder="Choose month...">
                                    @foreach(range(1, 12) as $month)
                                        <flux:select.option value="{{ $month }}">
                                            {{ \Carbon\Carbon::create()->month($month)->format('F Y') }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </div>
                        
                        <!-- Loading State -->
                        <div x-show="loading" class="h-64 flex items-center justify-center">
                            <div class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-300">Loading chart...</span>
                            </div>
                        </div>

                        <!-- Chart -->
                        <div x-show="!loading" class="h-64">
                            <flux:chart x-bind:value="chartData" class="h-full">
                                <flux:chart.svg>
                                    <flux:chart.line field="bookings" class="text-pink-500" />
                                    <flux:chart.line field="approved" class="text-sky-500" />
                                    <flux:chart.axis axis="x" field="date">
                                        <flux:chart.axis.tick />
                                    </flux:chart.axis>
                                    <flux:chart.axis axis="y">
                                        <flux:chart.axis.tick />
                                    </flux:chart.axis>
                                </flux:chart.svg>
                            </flux:chart>
                        </div>
                        
                        <!-- Chart Legend -->
                        <div class="flex justify-center gap-6 mt-4 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="text-gray-600 dark:text-gray-400">Total Bookings</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-gray-600 dark:text-gray-400">Approved Bookings</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                   
        </div>

        <!-- Calendar View -->
        <div x-show="currentView === 'calendar'" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             class="relative flex-1 overflow-hidden rounded-lg border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800 min-h-96">
            
            <div class="p-4 h-full flex flex-col">
                <!-- Calendar Header -->
                <div class="flex flex-col gap-4 mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Booking Calendar</h2>
                    
                    <!-- Desktop Controls (hidden on mobile) -->
                    <div class="hidden sm:flex flex-wrap gap-2 justify-end">
                        @can('book.create')                        
                            <flux:button size="sm" href="{{ route('bookings.create') }}" icon="plus">New Booking</flux:button>                        
                        @endcan
                        
                        <flux:dropdown>
                            <flux:button size="sm" variant="filled" id="filterStatusButton" icon:trailing="chevron-down">All Bookings</flux:button>
                            <flux:menu id="filterStatus">
                                <flux:menu.item data-value="all">All Bookings</flux:menu.item>
                                <flux:menu.item data-value="pending">Pending</flux:menu.item>
                                <flux:menu.item data-value="approved">Approved</flux:menu.item>
                                @if($isAdminRole)
                                    <flux:menu.item data-value="rejected">Rejected</flux:menu.item>
                                    <flux:menu.item data-value="cancelled">Cancelled</flux:menu.item>
                                @endif
                                <flux:menu.item data-value="done">Done</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                        <flux:dropdown>
                            <flux:button size="sm" variant="filled" id="filterAssetButton" icon:trailing="chevron-down">All Assets</flux:button>
                            <flux:menu id="filterAsset">
                                <flux:menu.item data-value="all">All Assets</flux:menu.item>
                                <flux:menu.item data-value="MeetingRoom">Meeting Rooms</flux:menu.item>
                                <flux:menu.item data-value="Vehicle">Vehicles</flux:menu.item>
                                <flux:menu.item data-value="ItAsset">IT Assets</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    <!-- Mobile Controls (hidden on desktop) -->
                    <div class="sm:hidden space-y-3">
                        <!-- New Booking Button - Full width on mobile -->
                        @can('book.create')
                        <flux:button href="{{ route('bookings.create') }}" icon="plus" class="w-full">
                            New Booking
                        </flux:button>
                        @endcan
                        
                        <!-- Filter Controls Grid -->
                        <div class="grid grid-cols-1 gap-2">
                            <flux:dropdown>
                                <flux:button variant="filled" id="filterStatusButtonMobile" icon:trailing="chevron-down" class="w-full justify-between">
                                    All Bookings
                                </flux:button>
                                <flux:menu id="filterStatusMobile">
                                    <flux:menu.item data-value="all">All Bookings</flux:menu.item>
                                    <flux:menu.item data-value="pending">Pending</flux:menu.item>
                                    <flux:menu.item data-value="approved">Approved</flux:menu.item>
                                    @if($isAdminRole)
                                        <flux:menu.item data-value="rejected">Rejected</flux:menu.item>
                                        <flux:menu.item data-value="cancelled">Cancelled</flux:menu.item>
                                    @endif
                                    <flux:menu.item data-value="done">Done</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                            
                            <flux:dropdown>
                                <flux:button variant="filled" id="filterAssetButtonMobile" icon:trailing="chevron-down" class="w-full justify-between">
                                    All Assets
                                </flux:button>
                                <flux:menu id="filterAssetMobile">
                                    <flux:menu.item data-value="all">All Assets</flux:menu.item>
                                    <flux:menu.item data-value="MeetingRoom">Meeting Rooms</flux:menu.item>
                                    <flux:menu.item data-value="Vehicle">Vehicles</flux:menu.item>
                                    <flux:menu.item data-value="ItAsset">IT Assets</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </div>
                </div>

                <!-- Calendar Container -->
                <div id="calendar" class="flex-1 relative" wire:ignore>
                    <div class="calendar-loading">
                        <div class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Loading calendar...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FluxUI Booking Details Modal -->
    <flux:modal name="booking-details" class="md:w-[800px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" id="modalTitle">Booking Details</flux:heading>
                <flux:text class="mt-2">View booking information and details.</flux:text>
            </div>

            <div id="modalContent" class="space-y-4">
                <!-- Content will be populated by JavaScript -->
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Close</flux:button>
                </flux:modal.close>
                
                @can('book.view')
                    <flux:button id="viewDetailsBtn" href="#" class="hidden">
                        View Details
                    </flux:button>
                @endcan
                
                @if($isAdminRole)
                    <flux:button id="editBookingBtn" href="#" variant="primary" class="hidden">
                        Edit Booking
                    </flux:button>
                @endif
            </div>
        </div>
    </flux:modal>

    <!-- Include the compiled Schedule-X JavaScript -->
    @vite(['resources/js/schedule-x-calendar.js'])
    
    <!-- Alpine.js initialization for calendar -->
    <div x-data="calendarInit()" x-init="initCalendar" style="display: none;"></div>
    
    <!-- Inline script for permission-based features and initialization -->
    <script>
        // Pass user role information to JavaScript
        window.isAdminRole = {{ $isAdminRole ? 'true' : 'false' }};
        window.userRole = '{{ $userRole }}';
        window.allowedStatuses = window.isAdminRole 
            ? ['pending', 'approved', 'rejected', 'cancelled', 'done']
            : ['pending', 'approved', 'done'];    
        
        function calendarInit() {
            return {
                initCalendar() {
                    // Reset flags when component initializes
                    window.calendarInitializing = false;
                    window.calendarInitialized = false;
                    window.calendarInstance = null;
                    
                    // Try multiple times to initialize the calendar
                    let attempts = 0;
                    const maxAttempts = 10;
                    
                    const tryInit = () => {
                        attempts++;
                        console.log(`Calendar init attempt ${attempts}/${maxAttempts}`);
                        
                        // Check if we're still on the dashboard page
                        const calendarEl = document.getElementById('calendar');
                        if (!calendarEl) {
                            console.log('Calendar element not found, stopping attempts');
                            return;
                        }
                        
                        if (typeof window.initializeScheduleXCalendar === 'function') {
                            // Check if calendar is not already initialized or initializing
                            if (!window.calendarInstance && !window.calendarInitializing) {
                                console.log('Calendar element found, initializing...');
                                window.initializeScheduleXCalendar();
                                return; // Success, stop trying
                            } else if (window.calendarInstance) {
                                console.log('Calendar already initialized');
                                return; // Already initialized, stop trying
                            }
                        }
                        
                        // Try again if we haven't reached max attempts
                        if (attempts < maxAttempts && !window.calendarInstance) {
                            setTimeout(tryInit, 500);
                        } else if (attempts >= maxAttempts) {
                            console.error('Failed to initialize calendar after max attempts');
                            // Show error in calendar element
                            if (calendarEl && !window.calendarInstance) {
                                calendarEl.innerHTML = '<div class="flex items-center justify-center h-64 text-gray-600">Unable to load calendar. Please refresh the page.</div>';
                            }
                        }
                    };
                    
                    // Start trying after a short delay
                    this.$nextTick(() => {
                        setTimeout(tryInit, 100);
                    });
                }
            }
        }
        
        // Force initialization using Alpine's x-init which works well with Livewire
        document.addEventListener('alpine:init', () => {
            console.log('Alpine initialized, preparing calendar...');
        });
        
        // Also try when the page becomes visible (in case it was backgrounded)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && typeof window.initializeScheduleXCalendar === 'function') {
                const calendarEl = document.getElementById('calendar');
                if (calendarEl && !window.calendarInstance && !window.calendarInitializing) {
                    console.log('Page became visible, checking calendar...');
                    setTimeout(() => window.initializeScheduleXCalendar(), 100);
                }
            }
        });
        
        // Add permission-based visibility for modal buttons
        window.addEventListener('booking-modal-opened', function(e) {
            const viewDetailsBtn = document.getElementById('viewDetailsBtn');
            const editBookingBtn = document.getElementById('editBookingBtn');
            
            @can('book.view')
            if (viewDetailsBtn) viewDetailsBtn.classList.remove('hidden');
            @endcan
            
            @can('book.edit')
            if (editBookingBtn) editBookingBtn.classList.remove('hidden');
            @endcan
        });

        // Chart controller functionality
        function chartController() {
            return {
                selectedMonth: {!! now()->month !!},
                loading: false,
                chartData: [],
                
                init() {
                    this.loadInitialData();
                },
                
                loadInitialData() {
                    // Load current month data
                    this.updateChart();
                },
                
                async updateChart() {
                    this.loading = true;
                    
                    try {
                        // Fetch real data from the server
                        const response = await fetch(`/dashboard/chart-data?month=${this.selectedMonth}`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            this.chartData = data;
                        } else {
                            // Fallback to initial data generation if API fails
                            this.generateFallbackData();
                        }
                    } catch (error) {
                        console.log('Failed to fetch chart data, using fallback');
                        this.generateFallbackData();
                    }
                    
                    this.loading = false;
                },
                
                generateFallbackData() {
                    // Fallback data generation
                    const data = [];
                    const year = new Date().getFullYear();
                    const daysInMonth = new Date(year, this.selectedMonth, 0).getDate();
                    
                    for (let day = 1; day <= daysInMonth; day++) {
                        const date = new Date(year, this.selectedMonth - 1, day);
                        data.push({
                            date: date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
                            bookings: 0, // No data fallback
                            approved: 0
                        });
                    }
                    
                    this.chartData = data;
                }
            }
        }
    </script>
    
    @push('scripts')
    <script>
        // Final initialization attempt after everything is loaded
        window.addEventListener('load', function() {
            console.log('Window fully loaded, final calendar init attempt...');
            setTimeout(function() {
                if (typeof window.initializeScheduleXCalendar === 'function') {
                    const calendarEl = document.getElementById('calendar');
                    if (calendarEl && !window.calendarInstance) {
                        console.log('Final attempt: Initializing calendar...');
                        window.initializeScheduleXCalendar();
                    }
                }
            }, 1000);
        });
    </script>
    @endpush
</x-layouts.app>