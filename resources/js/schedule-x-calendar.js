// resources/js/schedule-x-calendar.js

import { createCalendar, viewMonthGrid, viewWeek, viewDay, viewMonthAgenda } from '@schedule-x/calendar';
import { createEventsServicePlugin } from '@schedule-x/events-service';
import { createCurrentTimePlugin } from '@schedule-x/current-time'
import { createDragAndDropPlugin } from '@schedule-x/drag-and-drop';
import { createEventModalPlugin } from '@schedule-x/event-modal';
import '@schedule-x/theme-default/dist/index.css';

// Store calendar instance globally
let calendarInstance = null;
let refreshInterval = null;
let eventsService = null;

// Make calendar instance accessible globally for debugging
window.calendarInstance = null;

// Store filter states
window.currentStatusFilter = window.currentStatusFilter || 'all';
window.currentAssetFilter = window.currentAssetFilter || 'all';

async function fetchEvents() {
    const loadingEl = document.querySelector('.calendar-loading');
    if (loadingEl) loadingEl.classList.remove('hidden');

    try {
        let url = '/api/calendar-bookings?';
        const params = [];
        
        if (window.currentStatusFilter !== 'all') {
            params.push(`status=${window.currentStatusFilter}`);
        }
        
        if (window.currentAssetFilter !== 'all') {
            params.push(`asset_type=${window.currentAssetFilter}`);
        }
        
        url += params.join('&');

        const response = await fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const events = await response.json();
        
        // Filter events based on user role
        const filteredEvents = events.filter(event => {
            const eventStatus = event.extendedProps?.status || event.status;
            
            // If user is admin, show all events
            if (window.isAdminRole) {
                return true;
            }
            
            // For non-admin users, only show allowed statuses
            const allowedStatuses = window.allowedStatuses || ['pending', 'approved', 'done'];
            return allowedStatuses.includes(eventStatus);
        });
        
        console.log(`Filtered ${events.length} events to ${filteredEvents.length} based on role`);
        
        // Transform events to Schedule-X format
        return filteredEvents.map(event => {
            // Parse and reformat dates to Schedule-X compatible format
            const formatDateTime = (dateString) => {
                if (!dateString) return '';
                
                // Parse the date string (remove microseconds if present)
                const cleanDateString = dateString.replace(/\.\d{6}Z$/, 'Z');
                const date = new Date(cleanDateString);
                
                // Check if date is valid
                if (isNaN(date.getTime())) {
                    console.error('Invalid date:', dateString);
                    return '';
                }
                
                // Format as 'YYYY-MM-DD HH:mm'
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                
                return `${year}-${month}-${day} ${hours}:${minutes}`;
            };
            
            // Get the asset type label for the title
            const getAssetTypeLabel = (assetType) => {
                if (!assetType) return 'Booking';
                
                // Handle both short names and full class names
                const type = assetType.includes('\\') 
                    ? assetType.split('\\').pop().toLowerCase() 
                    : assetType.toLowerCase();
                
                switch(type) {
                    case 'meetingroom':
                    case 'meeting_room':
                        return 'Meeting Room';
                    case 'vehicle':
                        return 'Vehicle';
                    case 'itasset':
                    case 'it_asset':
                        return 'IT Asset';
                    default:
                        return 'Booking';
                }
            };
            
            // Get calendar ID based on asset type for color coding
            const getCalendarId = (assetType) => {
                if (!assetType) return 'default';
                
                // Handle both short names and full class names
                const type = assetType.includes('\\') 
                    ? assetType.split('\\').pop().toLowerCase() 
                    : assetType.toLowerCase();
                
                switch(type) {
                    case 'meetingroom':
                    case 'meeting_room':
                        return 'meeting-room';
                    case 'vehicle':
                        return 'vehicle';
                    case 'itasset':
                    case 'it_asset':
                        return 'it-asset';
                    default:
                        return 'default';
                }
            };
            
            const assetTypeLabel = getAssetTypeLabel(event.extendedProps?.assetType || event.assetType);
            const assetName = event.title.replace(/^Asset\s*:\s*/i, '').trim();
            const eventTitle = `${assetTypeLabel}: ${assetName}`;
            const calendarId = getCalendarId(event.extendedProps?.assetType || event.assetType);
            
            return {
                id: event.id.toString(),
                title: eventTitle,
                start: formatDateTime(event.start),
                end: formatDateTime(event.end),
                calendarId: calendarId, // This determines the color
                // Store additional data for modal
                _customData: {
                    status: event.extendedProps?.status || event.status,
                    assetType: event.extendedProps?.assetType || event.assetType,
                    assetTypeLabel: assetTypeLabel,
                    bookedBy: event.extendedProps?.bookedBy || event.bookedBy,
                    purpose: event.extendedProps?.purpose || event.purpose,
                    timeRange: event.extendedProps?.timeRange || event.timeRange,
                    capacity: event.extendedProps?.capacity || event.capacity,
                    refreshmentDetails: event.extendedProps?.refreshmentDetails || event.refreshmentDetails,
                    additionalBooking: event.extendedProps?.additionalBooking || event.additionalBooking,
                    originalStart: event.start,
                    originalEnd: event.end,
                    originalTitle: event.title
                }
            };
        });
    } catch (error) {
        console.error('Failed to fetch events:', error);
        return [];
    } finally {
        if (loadingEl) loadingEl.classList.add('hidden');
    }
}

async function initializeCalendar() {
    console.log('initializeCalendar called');
    
    // Prevent multiple simultaneous initializations
    if (window.calendarInitializing) {
        console.log('Calendar already initializing, skipping...');
        return;
    }
    
    // Check if already initialized
    if (calendarInstance || window.calendarInstance) {
        console.log('Calendar already initialized, skipping...');
        return;
    }
    
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.log('Calendar element not found');
        return;
    }
    
    // Mark as initializing
    window.calendarInitializing = true;
    
    try {
        // Show loading indicator
        const loadingHTML = '<div class="calendar-loading"><div class="flex items-center gap-2"><svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="text-gray-600 dark:text-gray-300">Loading calendar...</span></div></div>';
        
        // Only set loading HTML if calendar is empty
        if (!calendarEl.querySelector('.sx__calendar-wrapper')) {
            calendarEl.innerHTML = loadingHTML;
        }
        
        // Clear existing interval
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }

        // Fetch initial events
        console.log('Fetching events...');
        const events = await fetchEvents();
        console.log(`Fetched ${events.length} events`);

        // Create events service plugin
        const eventsServicePlugin = createEventsServicePlugin();

        // Create calendar configuration
        const calendar = createCalendar({
            locale: 'en-US',
            firstDayOfWeek: 1, // Monday
            defaultView: viewMonthGrid.name,
            views: [viewMonthGrid, viewWeek, viewDay, viewMonthAgenda],
            events: events,
            // Define calendars for different asset types with colors
            calendars: {
                'meeting-room': {
                    colorName: 'meeting-room',
                    lightColors: {
                        main: '#3b82f6',      // Blue-500
                        container: '#dbeafe', // Blue-100
                        onContainer: '#1e40af' // Blue-800
                    },
                    darkColors: {
                        main: '#60a5fa',      // Blue-400
                        container: '#1e3a8a', // Blue-900
                        onContainer: '#bfdbfe' // Blue-200
                    }
                },
                'vehicle': {
                    colorName: 'vehicle',
                    lightColors: {
                        main: '#10b981',      // Emerald-500
                        container: '#d1fae5', // Emerald-100
                        onContainer: '#064e3b' // Emerald-900
                    },
                    darkColors: {
                        main: '#34d399',      // Emerald-400
                        container: '#064e3b', // Emerald-900
                        onContainer: '#a7f3d0' // Emerald-200
                    }
                },
                'it-asset': {
                    colorName: 'it-asset',
                    lightColors: {
                        main: '#ed3bff',      // Violet-500
                        container: '#fae7ff', // Violet-100
                        onContainer: '#731078' // Violet-900
                    },
                    darkColors: {
                        main: '#a78bfa',      // Violet-400
                        container: '#4c1d95', // Violet-900
                        onContainer: '#c4b5fd' // Violet-300
                    }
                },
                'default': {
                    colorName: 'default',
                    lightColors: {
                        main: '#6b7280',      // Gray-500
                        container: '#f3f4f6', // Gray-100
                        onContainer: '#111827' // Gray-900
                    },
                    darkColors: {
                        main: '#9ca3af',      // Gray-400
                        container: '#1f2937', // Gray-800
                        onContainer: '#d1d5db' // Gray-300
                    }
                }
            },
            plugins: [
                eventsServicePlugin,
                createCurrentTimePlugin({
                    fullWeekWidth: true,
                }),
                // Optionally add drag and drop if you want that functionality
                // createDragAndDropPlugin(),
            ],
            // Calendar callbacks
            callbacks: {
                onEventClick: (calendarEvent) => {
                    openBookingModal(calendarEvent);
                },
                onSelectedDateUpdate: (date) => {
                    console.log('Selected date:', date);
                },
                onRangeUpdate: (range) => {
                    console.log('Range updated:', range);
                }
            },
            // Customize the calendar appearance
            weekOptions: {
                gridHeight: 600,
                nDays: 7,
                eventWidth: 95,
                timeAxisFormatOptions: {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                }
            },
            dayGridOptions: {
                nDays: 1,
                timeAxisFormatOptions: {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                }
            },
            monthGridOptions: {
                nEventsPerDay: 3
            },
            // Set the time range for day/week views
            dayBoundaries: {
                start: '00:00',
                end: '24:00'
            }
        });

        // Clear the container before rendering
        calendarEl.innerHTML = '';
        
        // Render the calendar
        console.log('Rendering calendar...');
        calendar.render(calendarEl);
        
        // Store references
        calendarInstance = calendar;
        window.calendarInstance = calendar; // Make globally accessible
        eventsService = eventsServicePlugin;
        
        console.log('Calendar initialized successfully');

        // Setup event listeners
        setupEventListeners();

        // Refresh calendar every 2 minutes
        refreshInterval = setInterval(async () => {
            if (eventsService) {
                const newEvents = await fetchEvents();
                eventsService.set(newEvents);
            }
        }, 120000); // 2 minutes
        
        // Mark initialization as complete
        window.calendarInitialized = true;
        
    } catch (error) {
        console.error('Error initializing calendar:', error);
        // Show error message in calendar container
        if (calendarEl) {
            calendarEl.innerHTML = '<div class="flex items-center justify-center h-64 text-red-600">Error loading calendar. Please refresh the page.</div>';
        }
    } finally {
        // Clear initializing flag
        window.calendarInitializing = false;
    }
}

function setupEventListeners() {
    // Filter listeners
    document.querySelectorAll('#filterStatus [data-value]').forEach(item => {
        item.addEventListener('click', async (e) => {
            const value = e.currentTarget.dataset.value;
            
            // Check if this status is allowed for the current user
            if (value !== 'all' && !window.isAdminRole) {
                const allowedStatuses = window.allowedStatuses || ['pending', 'approved', 'done'];
                if (!allowedStatuses.includes(value)) {
                    console.warn(`Status "${value}" not allowed for non-admin users`);
                    return;
                }
            }
            
            window.currentStatusFilter = value;
            
            // Update button text
            const button = document.getElementById('filterStatusButton');
            if (button) {
                const textNode = Array.from(button.childNodes).find(node => node.nodeType === 3);
                if (textNode) {
                    textNode.textContent = e.currentTarget.textContent;
                } else {
                    button.childNodes[0].textContent = e.currentTarget.textContent;
                }
            }
            
            // Refresh events
            if (eventsService) {
                const newEvents = await fetchEvents();
                eventsService.set(newEvents);
            }
        });
    });

    document.querySelectorAll('#filterAsset [data-value]').forEach(item => {
        item.addEventListener('click', async (e) => {
            const value = e.currentTarget.dataset.value;
            window.currentAssetFilter = value;
            
            // Update button text
            const button = document.getElementById('filterAssetButton');
            if (button) {
                const textNode = Array.from(button.childNodes).find(node => node.nodeType === 3);
                if (textNode) {
                    textNode.textContent = e.currentTarget.textContent;
                } else {
                    button.childNodes[0].textContent = e.currentTarget.textContent;
                }
            }
            
            // Refresh events
            if (eventsService) {
                const newEvents = await fetchEvents();
                eventsService.set(newEvents);
            }
        });
    });

    // Restore filter button texts
    restoreFilterButtons();
}

function restoreFilterButtons() {
    const statusButton = document.getElementById('filterStatusButton');
    const assetButton = document.getElementById('filterAssetButton');
    
    if (statusButton && window.currentStatusFilter !== 'all') {
        const statusText = document.querySelector(`#filterStatus [data-value="${window.currentStatusFilter}"]`)?.textContent;
        if (statusText) {
            const textNode = Array.from(statusButton.childNodes).find(node => node.nodeType === 3);
            if (textNode) {
                textNode.textContent = statusText;
            } else {
                statusButton.childNodes[0].textContent = statusText;
            }
        }
    }
    
    if (assetButton && window.currentAssetFilter !== 'all') {
        const assetText = document.querySelector(`#filterAsset [data-value="${window.currentAssetFilter}"]`)?.textContent;
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

// Updated function to work with FluxUI modal
function openBookingModal(calendarEvent) {
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    const viewDetailsBtn = document.getElementById('viewDetailsBtn');
    const editBookingBtn = document.getElementById('editBookingBtn');

    // Dispatch custom event for permission handling
    window.dispatchEvent(new CustomEvent('booking-modal-opened', { 
        detail: { bookingId: calendarEvent.id } 
    }));
    
    const data = calendarEvent._customData || {};
    
    // Update modal title
    if (modalTitle) {
        modalTitle.textContent = `Booking #${calendarEvent.id}`;
    }
    
    // Build modal content
    const statusBadge = `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${getStatusClasses(data.status)}">${(data.status || 'pending').charAt(0).toUpperCase() + (data.status || 'pending').slice(1)}</span>`;
    const assetTypeBadge = `<span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">${data.assetTypeLabel || 'Unknown'}</span>`;
    
    if (modalContent) {
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asset Name</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">${data.originalTitle || 'N/A'}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Booked By</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">${data.bookedBy || 'N/A'}</p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">${data.timeRange || formatTimeRange(calendarEvent.start, calendarEvent.end)}</p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purpose</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">${data.purpose || 'No purpose specified'}</p>
                </div>
                
                ${data.capacity ? `
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Capacity</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">${data.capacity} people</p>
                </div>
                ` : ''}
                
                ${data.refreshmentDetails ? `
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Refreshments</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">${data.refreshmentDetails}</p>
                </div>
                ` : ''}
                
                ${data.additionalBooking && Object.keys(data.additionalBooking).length > 0 ? `
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Services</label>
                    <div class="mt-1 text-sm text-gray-900 dark:text-white">
                        ${Object.entries(data.additionalBooking).map(([key, value]) => 
                            `<div><strong>${key}:</strong> ${value}</div>`
                        ).join('')}
                    </div>
                </div>
                ` : ''}
            </div>
        `;
    }
    
    // Update action buttons
    if (viewDetailsBtn) {
        viewDetailsBtn.href = `/bookings/${calendarEvent.id}`;
        viewDetailsBtn.classList.remove('hidden');
    }
    
    if (editBookingBtn) {
        editBookingBtn.href = `/bookings/${calendarEvent.id}/edit`;
        editBookingBtn.classList.remove('hidden');
    }
    
    // Open the FluxUI modal using the global Flux object
    if (window.Flux) {
        window.Flux.modal('booking-details').show();
    } else {
        console.error('Flux is not available. Make sure FluxUI is properly loaded.');
    }
}

// This function is no longer needed as FluxUI handles modal closing
function closeBookingModal() {
    const viewDetailsBtn = document.getElementById('viewDetailsBtn');
    const editBookingBtn = document.getElementById('editBookingBtn');

    // Hide action buttons when modal closes
    if (viewDetailsBtn) viewDetailsBtn.classList.add('hidden');
    if (editBookingBtn) editBookingBtn.classList.add('hidden');
    
    // Close the FluxUI modal
    if (window.Flux) {
        window.Flux.modal('booking-details').close();
    }
}

function formatTimeRange(start, end) {
    const startDate = new Date(start);
    const endDate = new Date(end);
    
    const options = { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    };
    
    const startTime = startDate.toLocaleTimeString('en-US', options);
    const endTime = endDate.toLocaleTimeString('en-US', options);
    
    if (startDate.toDateString() === endDate.toDateString()) {
        return `${startTime} - ${endTime}`;
    } else {
        return `${startDate.toLocaleDateString()} ${startTime} - ${endDate.toLocaleDateString()} ${endTime}`;
    }
}

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

// Export functions to window for global access
window.initializeScheduleXCalendar = initializeCalendar;
window.openBookingModal = openBookingModal;
window.closeBookingModal = closeBookingModal;

// Initialize function that checks if calendar should be loaded
function tryInitializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    if (calendarEl && !calendarInstance && !window.calendarInstance && !window.calendarInitializing) {
        console.log('tryInitializeCalendar: Conditions met, initializing...');
        initializeCalendar();
    } else {
        console.log('tryInitializeCalendar: Conditions not met', {
            calendarEl: !!calendarEl,
            calendarInstance: !!calendarInstance,
            windowCalendarInstance: !!window.calendarInstance,
            initializing: !!window.calendarInitializing
        });
    }
}

// Initialize on DOMContentLoaded (for direct page load)
document.addEventListener('DOMContentLoaded', function() {
    tryInitializeCalendar();
});

// Initialize on Livewire navigation (for SPA navigation)
document.addEventListener('livewire:navigated', function() {
    console.log('Livewire navigated - checking for calendar...');
    // Small delay to ensure DOM is ready
    setTimeout(function() {
        tryInitializeCalendar();
    }, 100);
});

// Initialize on Livewire page loaded (for initial load after login)
document.addEventListener('livewire:load', function() {
    console.log('Livewire loaded - checking for calendar...');
    tryInitializeCalendar();
});

// Initialize on Livewire initialized (latest Livewire v3 event)
document.addEventListener('livewire:initialized', function() {
    console.log('Livewire initialized - checking for calendar...');
    setTimeout(function() {
        tryInitializeCalendar();
    }, 100);
});

// Listen for calendar-ready event from wire:init
window.addEventListener('calendar-ready', function() {
    console.log('Calendar ready event received...');
    setTimeout(function() {
        tryInitializeCalendar();
    }, 100);
});

// Clean up on page unload
document.addEventListener('livewire:navigating', function() {
    console.log('Livewire navigating away - cleaning up...');
    
    // Clear the refresh interval
    if (refreshInterval) {
        clearInterval(refreshInterval);
        refreshInterval = null;
    }
    
    // Clear calendar instance references
    if (calendarInstance) {
        calendarInstance = null;
    }
    if (window.calendarInstance) {
        window.calendarInstance = null;
    }
    
    // Reset initialization flags
    window.calendarInitializing = false;
    window.calendarInitialized = false;
    
    // Clear the calendar element if it exists
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        // Don't clear innerHTML here as it might cause issues
        console.log('Calendar element found during navigation cleanup');
    }
});

// Alternative Livewire hook for component updates
if (window.Livewire) {
    Livewire.hook('message.processed', (message, component) => {
        // Only initialize if calendar element exists but instance doesn't
        setTimeout(function() {
            tryInitializeCalendar();
        }, 50);
    });
    
    // Hook for when a component is initialized
    Livewire.hook('component.initialized', (component) => {
        setTimeout(function() {
            tryInitializeCalendar();
        }, 100);
    });
}

// Also try to initialize when the window fully loads (fallback)
window.addEventListener('load', function() {
    setTimeout(function() {
        tryInitializeCalendar();
    }, 200);
});