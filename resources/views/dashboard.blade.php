<x-layouts.app :title="__('Dashboard')">
    <!-- FullCalendar CSS -->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.18/main.min.css' rel='stylesheet' />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Apply to all FullCalendar buttons */
        .fc .fc-button {
            border-radius: 8px; /* change to 9999px for fully rounded pill shape */
            padding: 5px 12px;
            border: 1px solid #ccc;
            background-color: #F4F4F4;
            color:rgb(65, 65, 69);
            font-size: 14px;
            font-family: Inter, sans-serif, 'Instrument Sans';
            font-weight: 500;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        /* Active/pressed state */
        .fc .fc-button:active {
            background-color: #d6d8db;
        }

        /* Optional: style your custom button specifically */
        .fc .fc-myCustomButton-button {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .fc .fc-myCustomButton-button:hover {
            background-color: #1d4ed8;
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
                        <flux:dropdown>
                            <flux:button size="sm" variant="filled" id="filterStatusButton" icon:trailing="chevron-down">All Bookings</flux:button>
                            <flux:menu id="filterStatus">
                                <flux:menu.item data-value="all">All Bookings</flux:menu.item>
                                <flux:menu.item data-value="pending">Pending</flux:menu.item>
                                <flux:menu.item data-value="approved">Approved</flux:menu.item>
                                <flux:menu.item data-value="rejected">Rejected</flux:menu.item>
                                <flux:menu.item data-value="cancelled">Cancelled</flux:menu.item>
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
                <div id="calendar" class="flex-1"></div>
            </div>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-neutral-800 rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Booking Details</h3>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="modalContent" class="space-y-4">
                <!-- Content will be populated by JavaScript -->
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button id="closeModalBtn" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 dark:text-gray-400 dark:border-neutral-600 dark:hover:bg-neutral-700">
                    Close
                </button>
                <a id="viewDetailsBtn" href="#" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 hidden">
                    View Details
                </a>
                <a id="editBookingBtn" href="#" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 hidden">
                    Edit Booking
                </a>
            </div>
        </div>
    </div>

    <!-- FullCalendar JS -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.18/index.global.js'></script>
    
    <script>
        // Store calendar instance globally so we can destroy it if needed
        let calendarInstance = null;
        let refreshInterval = null;

        function initializeCalendar() {
            // Destroy existing calendar if it exists
            if (calendarInstance) {
                calendarInstance.destroy();
                calendarInstance = null;
            }

            // Clear existing interval
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }

            // Initialize calendar
            const calendarEl = document.getElementById('calendar');
            
            // Check if calendar element exists
            if (!calendarEl) {
                return;
            }

            // Build events URL with current filters
            let eventsUrl = '/api/calendar-bookings?';
            const params = [];
            if (currentStatusFilter !== 'all') {
                params.push(`status=${currentStatusFilter}`);
            }
            if (currentAssetFilter !== 'all') {
                params.push(`asset_type=${currentAssetFilter}`);
            }
            eventsUrl += params.join('&');

            calendarInstance = new FullCalendar.Calendar(calendarEl, {
                customButtons: {
                    myCustomButton: {
                        text: 'custom!',
                        click: function() {
                            alert('clicked the custom button!');
                        }
                    }
                },                
                initialView: 'dayGridMonth',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today myCustomButton',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                views: {
                    dayGridMonth: { buttonText: 'Month' },
                    timeGridWeek: { buttonText: 'Week' },
                    timeGridDay: { buttonText: 'Day' },
                    listMonth: { buttonText: 'List' }
                },                
                events: eventsUrl,
                eventClick: function(info) {
                    openBookingModal(info.event);
                },
                eventDidMount: function(info) {
                    // Add tooltip with booking details
                    info.el.title = `${info.event.title}\nStatus: ${info.event.extendedProps.status}\nTime: ${info.event.extendedProps.timeRange}`;
                },
                loading: function(bool) {
                    if (bool) {
                        showLoading();
                    } else {
                        hideLoading();
                    }
                },
                eventDisplay: 'block',
                displayEventTime: true,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }
            });

            calendarInstance.render();

            // Setup event listeners
            setupEventListeners();

            // Refresh calendar every 2 minutes
            refreshInterval = setInterval(function() {
                if (calendarInstance) {
                    calendarInstance.refetchEvents();
                }
            }, 120000); // 2 minutes
        }

        // Store filter states globally to persist across reinitializations
        let currentStatusFilter = window.currentStatusFilter || 'all';
        let currentAssetFilter = window.currentAssetFilter || 'all';

        // Track if event listeners are already attached
        let listenersAttached = false;

        function setupEventListeners() {
            // Only attach modal listeners once
            if (!listenersAttached) {
                // Modal close listeners
                document.addEventListener('click', function(e) {
                    if (e.target.id === 'closeModal' || e.target.closest('#closeModal')) {
                        closeBookingModal();
                    }
                    if (e.target.id === 'closeModalBtn') {
                        closeBookingModal();
                    }
                });

                // Use event delegation for Flux dropdown menus
                document.addEventListener('click', function(e) {
                    // Status filter
                    if (e.target.closest('#filterStatus [data-value]')) {
                        const menuItem = e.target.closest('[data-value]');
                        if (menuItem) {
                            currentStatusFilter = menuItem.dataset.value;
                            window.currentStatusFilter = currentStatusFilter;
                            const button = document.getElementById('filterStatusButton');
                            if (button) {
                                // Update button text content only, not the entire button
                                const textNode = Array.from(button.childNodes).find(node => node.nodeType === 3);
                                if (textNode) {
                                    textNode.textContent = menuItem.textContent;
                                } else {
                                    // If no text node, update the first part before any icons
                                    button.childNodes[0].textContent = menuItem.textContent;
                                }
                            }
                            applyFilters();
                        }
                    }

                    // Asset filter
                    if (e.target.closest('#filterAsset [data-value]')) {
                        const menuItem = e.target.closest('[data-value]');
                        if (menuItem) {
                            currentAssetFilter = menuItem.dataset.value;
                            window.currentAssetFilter = currentAssetFilter;
                            const button = document.getElementById('filterAssetButton');
                            if (button) {
                                // Update button text content only, not the entire button
                                const textNode = Array.from(button.childNodes).find(node => node.nodeType === 3);
                                if (textNode) {
                                    textNode.textContent = menuItem.textContent;
                                } else {
                                    // If no text node, update the first part before any icons
                                    button.childNodes[0].textContent = menuItem.textContent;
                                }
                            }
                            applyFilters();
                        }
                    }
                });

                listenersAttached = true;
            }

            // Restore filter button texts after navigation
            const statusButton = document.getElementById('filterStatusButton');
            const assetButton = document.getElementById('filterAssetButton');
            
            if (statusButton && currentStatusFilter !== 'all') {
                const statusText = document.querySelector(`#filterStatus [data-value="${currentStatusFilter}"]`)?.textContent;
                if (statusText) {
                    const textNode = Array.from(statusButton.childNodes).find(node => node.nodeType === 3);
                    if (textNode) {
                        textNode.textContent = statusText;
                    } else {
                        statusButton.childNodes[0].textContent = statusText;
                    }
                }
            }
            
            if (assetButton && currentAssetFilter !== 'all') {
                const assetText = document.querySelector(`#filterAsset [data-value="${currentAssetFilter}"]`)?.textContent;
                if (assetText) {
                    const textNode = Array.from(assetButton.childNodes).find(node => node.nodeType === 3);
                    if (textNode) {
                        textNode.textContent = assetText;
                    } else {
                        assetButton.childNodes[0].textContent = assetText;
                    }
                }
            }
        }

        // Apply filters function (moved outside setupEventListeners for global access)
        function applyFilters() {
            if (!calendarInstance) return;
            
            let url = '/api/calendar-bookings?';
            const params = [];
            
            if (currentStatusFilter !== 'all') {
                params.push(`status=${currentStatusFilter}`);
            }
            
            if (currentAssetFilter !== 'all') {
                params.push(`asset_type=${currentAssetFilter}`);
            }
            
            url += params.join('&');
            
            calendarInstance.removeAllEventSources();
            calendarInstance.addEventSource(url);
        }

        // Modal functions (global scope)
        function openBookingModal(event) {
            const modal = document.getElementById('bookingModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalContent = document.getElementById('modalContent');
            const viewDetailsBtn = document.getElementById('viewDetailsBtn');
            const editBookingBtn = document.getElementById('editBookingBtn');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            const props = event.extendedProps;
            modalTitle.textContent = `Booking #${event.id}`;
            
            // Build modal content
            const statusBadge = `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${getStatusClasses(props.status)}">${props.status.charAt(0).toUpperCase() + props.status.slice(1)}</span>`;
            const assetTypeBadge = `<span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">${props.assetTypeLabel}</span>`;
            
            modalContent.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <div class="mt-1">${statusBadge}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asset Type</label>
                        <div class="mt-1">${assetTypeBadge}</div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Booked By</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">${props.bookedBy || 'N/A'}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">${props.timeRange}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purpose</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">${props.purpose || 'No purpose specified'}</p>
                    </div>
                    
                    ${props.capacity ? `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Capacity</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">${props.capacity} people</p>
                    </div>
                    ` : ''}
                    
                    ${props.refreshmentDetails ? `
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Refreshments</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">${props.refreshmentDetails}</p>
                    </div>
                    ` : ''}
                    
                    ${props.additionalBooking && Object.keys(props.additionalBooking).length > 0 ? `
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Services</label>
                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                            ${Object.entries(props.additionalBooking).map(([key, value]) => 
                                `<div><strong>${key}:</strong> ${value}</div>`
                            ).join('')}
                        </div>
                    </div>
                    ` : ''}
                </div>
            `;
            
            // Show/hide action buttons based on permissions
            viewDetailsBtn.href = `/bookings/${event.id}`;
            editBookingBtn.href = `/bookings/${event.id}/edit`;
            
            @can('book.view')
            viewDetailsBtn.classList.remove('hidden');
            @endcan
            
            @can('book.edit')
            editBookingBtn.classList.remove('hidden');
            @endcan
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            const viewDetailsBtn = document.getElementById('viewDetailsBtn');
            const editBookingBtn = document.getElementById('editBookingBtn');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
            viewDetailsBtn.classList.add('hidden');
            editBookingBtn.classList.add('hidden');
        }

        // Helper functions
        function getStatusClasses(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800',
                'cancelled': 'bg-gray-100 text-gray-800',
                'done': 'bg-blue-100 text-blue-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        }

        function showLoading() {
            console.log('Loading calendar events...');
        }

        function hideLoading() {
            console.log('Calendar events loaded.');
        }

        // Initialize on DOMContentLoaded (for initial page load)
        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();
        });

        // Initialize on Livewire navigation (for SPA navigation)
        document.addEventListener('livewire:navigated', function() {
            // Small delay to ensure DOM is ready
            setTimeout(function() {
                initializeCalendar();
            }, 100);
        });

        // Alternative: Listen for Livewire page loaded event
        if (window.Livewire) {
            Livewire.hook('message.processed', (message, component) => {
                // Check if calendar element exists but calendar is not initialized
                const calendarEl = document.getElementById('calendar');
                if (calendarEl && !calendarInstance) {
                    initializeCalendar();
                }
            });
        }

        // Clean up on page unload
        document.addEventListener('livewire:navigating', function() {
            if (calendarInstance) {
                calendarInstance.destroy();
                calendarInstance = null;
            }
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
            // Don't reset listenersAttached here - keep event delegation active
        });
    </script>
</x-layouts.app>