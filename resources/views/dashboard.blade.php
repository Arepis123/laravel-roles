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
    </style>

    
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Stats Cards - First Row -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <!-- Total Bookings Card -->
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                <div class="absolute inset-0 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Bookings</h3>
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2" id="totalBookings">{{ App\Models\Booking::count() }}</p>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        All time
                    </div>
                </div>
            </div>

            <!-- Pending Approvals Card -->
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                <div class="absolute inset-0 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pending Bookings</h3>
                        <p class="text-3xl font-bold text-orange-600 dark:text-orange-400 mt-2" id="pendingBookings">{{ App\Models\Booking::pending()->count() }}</p>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Awaiting approval
                    </div>
                </div>
            </div>

            <!-- Today's Bookings Card -->
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                <div class="absolute inset-0 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Today's Bookings</h3>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2" id="todayBookings">{{ App\Models\Booking::whereDate('start_time', today())->count() }}</p>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Active today
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards - Second Row -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <!-- This Week's Bookings Card -->
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                <div class="absolute inset-0 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">This Week</h3>
                        <div class="flex items-baseline gap-2 mt-2">
                            <p class="text-3xl font-bold text-teal-600 dark:text-teal-400" id="weekBookings">
                                {{ App\Models\Booking::whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()])->count() }}
                            </p>
                            @php
                                $thisWeek = App\Models\Booking::whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()])->count();
                                $lastWeek = App\Models\Booking::whereBetween('start_time', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
                                $percentChange = $lastWeek > 0 ? round((($thisWeek - $lastWeek) / $lastWeek) * 100) : 0;
                            @endphp
                            @if($percentChange != 0)
                                <span class="text-sm font-medium {{ $percentChange > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $percentChange > 0 ? '↑' : '↓' }} {{ abs($percentChange) }}%
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        vs last week
                    </div>
                </div>
            </div>

            <!-- Utilization Rate Card -->
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                <div class="absolute inset-0 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Utilization Rate</h3>
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
                        <div class="mt-2">
                            <div class="flex items-baseline gap-2">
                                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $utilizationRate }}%</p>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $bookedToday }}/{{ $totalAssets }}</span>
                            </div>
                            <!-- Progress bar -->
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                <div class="bg-purple-600 h-2 rounded-full dark:bg-purple-400 transition-all duration-300" style="width: {{ $utilizationRate }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Assets in use today
                    </div>
                </div>
            </div>

            <!-- Most Booked Asset Card -->
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                <div class="absolute inset-0 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Most Booked</h3>
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
                        <div class="mt-2">
                            <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400 truncate" title="{{ $assetName }}">
                                {{ Str::limit($assetName, 20) }}
                            </p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ $bookingCount }} <span class="text-base font-normal text-gray-600 dark:text-gray-400">bookings</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                        This month
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="p-4 h-full flex flex-col">
                <!-- Calendar Header -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Booking Calendar</h2>
                    <div class="flex gap-2">
                        @can('book.create')                        
                            <flux:button size="sm" href="{{ route('bookings.create') }}" icon="plus">New Booking</flux:button>                        
                        @endcan
                        
                        @php
                            $userRole = auth()->user()->getRoleNames()->first();
                            $isAdminRole = in_array($userRole, ['Super Admin', 'Admin']);
                        @endphp
                        
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
                </div>

                <!-- Calendar Container -->
                <div id="calendar" class="flex-1 relative" wire:ignore>
                    <div class="calendar-loading">
                        <div class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-gray-300">Loading calendar...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Booking Tooltip -->
    <div id="bookingTooltip" class="booking-tooltip">
        <!-- Content will be populated by JavaScript -->
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
                
                @can('book.edit')
                    <flux:button id="editBookingBtn" href="#" variant="primary" class="hidden">
                        Edit Booking
                    </flux:button>
                @endcan
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
        window.allowedStatuses = window.isAdminRole 
            ? ['pending', 'approved', 'rejected', 'cancelled', 'done']
            : ['pending', 'approved', 'done'];
        
        console.log('User admin status:', window.isAdminRole);
        console.log('Allowed statuses:', window.allowedStatuses);
        
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