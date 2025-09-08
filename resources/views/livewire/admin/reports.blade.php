<div>
    <!-- Page Header -->
    <div class="relative mb-6 w-full">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Reports') }}</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('Generate and download reports for assets, vehicles, bookings, and users') }}</p>
        <flux:separator variant="subtle" class="my-4" />
    </div>

    <!-- Custom Toast Notifications -->
    <div x-data="{ 
        showToast: false, 
        toastType: 'success', 
        toastMessage: '',
        showToastNotification(type, message) {
            this.toastType = type;
            this.toastMessage = message;
            this.showToast = true;
            setTimeout(() => this.showToast = false, type === 'error' ? 6000 : 4000);
        }
    }"
         x-on:report-generated.window="showToastNotification('success', $event.detail.message)"
         x-on:report-error.window="showToastNotification('error', $event.detail.message)"
         x-on:report-deleted.window="showToastNotification('success', $event.detail.message)"
         x-on:report-emailed.window="showToastNotification('success', $event.detail.message)"
        
        <!-- Toast Container -->
        <div x-show="showToast" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed top-4 right-4 z-50 max-w-sm w-full">
            
            <!-- Success Toast -->
            <div x-show="toastType === 'success'" 
                 class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <flux:icon.check-circle class="h-5 w-5 text-green-400" />
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-green-800">Success!</p>
                        <p class="mt-1 text-sm text-green-700 break-words" x-text="toastMessage"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="showToast = false" class="inline-flex text-green-400 hover:text-green-600 focus:outline-none">
                            <flux:icon.x-mark class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Error Toast -->
            <div x-show="toastType === 'error'" 
                 class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <flux:icon.x-circle class="h-5 w-5 text-red-400" />
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-red-800">Error!</p>
                        <p class="mt-1 text-sm text-red-700 break-words whitespace-pre-wrap" x-text="toastMessage"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="showToast = false" class="inline-flex text-red-400 hover:text-red-600 focus:outline-none">
                            <flux:icon.x-mark class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Generation Card -->
    <div class="border border-gray-200 rounded-lg p-6 space-y-6 mb-6">
        <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
            <div class="p-1.5 sm:p-2 bg-blue-100 rounded-lg flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <flux:heading size="lg" class="my-0 py-0">Generate New Report</flux:heading>
                <p class="text-sm text-gray-400">{{ __('Create downloadable reports with custom filters') }}</p>     
            </div>
        </div>        

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Report Type -->
            <div class="space-y-2">
                <flux:field>
                    <flux:label>Report Type</flux:label>
                    <flux:select wire:model.live="reportType" placeholder="Select report type">
                        <flux:select.option value="assets">Assets Report</flux:select.option>
                        <flux:select.option value="vehicles">Vehicles Report</flux:select.option>
                        <flux:select.option value="bookings">Bookings Report</flux:select.option>
                        <flux:select.option value="users">Users Report</flux:select.option>
                    </flux:select>
                </flux:field>
            </div>

            <!-- Report Format -->
            <div class="space-y-2">
                <flux:field>
                    <flux:label>Format</flux:label>
                    <flux:select wire:model="reportFormat" placeholder="Select format">
                        <flux:select.option value="excel">Excel (.xlsx)</flux:select.option>
                        <flux:select.option value="csv">CSV (.csv)</flux:select.option>
                        <flux:select.option value="pdf">PDF (.pdf)</flux:select.option>
                        <flux:select.option value="json">JSON (.json)</flux:select.option>
                        <flux:select.option value="xml">XML (.xml)</flux:select.option>
                        <flux:select.option value="html">HTML (.html)</flux:select.option>
                        <flux:select.option value="txt">Text (.txt)</flux:select.option>
                    </flux:select>
                </flux:field>
            </div>

            <!-- Generate Button -->
            <div class="flex items-end">
                <flux:button 
                    wire:click="generateReport" 
                    wire:loading.attr="disabled"
                    wire:target="generateReport"                    
                    variant="primary" 
                    class="w-full">
                    <span wire:loading.remove wire:target="generateReport">Generate Report</span>
                    <span wire:loading wire:target="generateReport">
                        <flux:icon.loading class="animate-spin h-4 w-4 mr-2 inline-block" />
                        <span>{{ _('Generating...') }}</span>
                    </span>
                </flux:button>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="border-t pt-4">
            <flux:button wire:click="toggleFilters" variant="filled" class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800">
                <flux:icon.funnel class="inline-block h-4 w-4 transform transition-transform duration-200 {{ $showFilters ? 'rotate-180' : '' }}" />
                <span>{{ $showFilters ? 'Hide' : 'Show' }} Filters</span>
            </flux:button>

            @if($showFilters)
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-800 dark:border-gray-200 rounded-lg border">
                    <!-- Date Range -->
                    <flux:field>
                        <flux:label>Date From</flux:label>
                        <flux:input type="date" wire:model="dateFrom" />
                    </flux:field>
                    
                    <flux:field>
                        <flux:label>Date To</flux:label>
                        <flux:input type="date" wire:model="dateTo" />
                    </flux:field>

                    <!-- Status Filter -->
                    <flux:field>
                        <flux:label>Status</flux:label>
                        <flux:select wire:model="status" placeholder="All Status">
                            @if($reportType === 'assets')
                                <flux:select.option value="available">Available</flux:select.option>
                                <flux:select.option value="booked">Booked</flux:select.option>
                                <flux:select.option value="maintenance">Maintenance</flux:select.option>
                            @elseif($reportType === 'vehicles')
                                <flux:select.option value="available">Available</flux:select.option>
                                <flux:select.option value="booked">Booked</flux:select.option>
                                <flux:select.option value="maintenance">Maintenance</flux:select.option>
                                <flux:select.option value="out_of_service">Out of Service</flux:select.option>
                            @elseif($reportType === 'bookings')
                                <flux:select.option value="pending">Pending</flux:select.option>
                                <flux:select.option value="approved">Approved</flux:select.option>
                                <flux:select.option value="rejected">Rejected</flux:select.option>
                                <flux:select.option value="completed">Completed</flux:select.option>
                                <flux:select.option value="cancelled">Cancelled</flux:select.option>
                            @elseif($reportType === 'users')
                                <flux:select.option value="active">Active</flux:select.option>
                                <flux:select.option value="inactive">Inactive</flux:select.option>
                            @endif
                        </flux:select>
                    </flux:field>

                    <!-- Category Filter (Assets only) -->
                    @if($reportType === 'assets' && count($categories) > 0)
                        <flux:field>
                            <flux:label>Category</flux:label>
                            <flux:select wire:model="categoryId" placeholder="All Categories">
                                @foreach($categories as $category)
                                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    @endif

                    <!-- Role Filter (Users only) -->
                    @if($reportType === 'users')
                        <flux:field>
                            <flux:label>Role</flux:label>
                            <flux:select wire:model="role" placeholder="All Roles">
                                <flux:select.option value="admin">Admin</flux:select.option>
                                <flux:select.option value="user">User</flux:select.option>
                            </flux:select>
                        </flux:field>
                    @endif

                    <!-- Booking Date Range (Bookings and Vehicles) -->
                    @if($reportType === 'bookings' || $reportType === 'vehicles')
                        <flux:field>
                            <flux:label>Booking From</flux:label>
                            <flux:input type="date" wire:model="bookingDateFrom" />
                        </flux:field>
                        
                        <flux:field>
                            <flux:label>Booking To</flux:label>
                            <flux:input type="date" wire:model="bookingDateTo" />
                        </flux:field>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Report History -->
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-amber-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <flux:heading size="lg" class="my-0 py-0">Report History</flux:heading>
                    <p class="text-sm text-gray-400">{{ __('Previous generated reports') }}</p>     
                </div>
            </div>             
        </div>

        <div class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:border-neutral-700">
                    <thead class="bg-gray-50 dark:bg-neutral-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:text-neutral-400" wire:click="sortBy('report_type')">
                                <div class="flex items-center space-x-1 ">
                                    <span>Report Type</span>
                                    <button wire:click="sortBy('report_type')" class="hover:bg-gray-100 p-1 rounded">
                                        <flux:icon name="chevron-up-down" class="w-3 h-3" />
                                    </button>                                    
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:text-neutral-400" wire:click="sortBy('report_format')">
                                <div class="flex items-center space-x-1">
                                    <span>Format</span>
                                    <button wire:click="sortBy('report_format')" class="hover:bg-gray-100 p-1 rounded">
                                        <flux:icon name="chevron-up-down" class="w-3 h-3" />
                                    </button> 
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:text-neutral-400" wire:click="sortBy('record_count')">
                                <div class="flex items-center space-x-1">
                                    <span>Records</span>
                                    <button wire:click="sortBy('record_count')" class="hover:bg-gray-100 p-1 rounded">
                                        <flux:icon name="chevron-up-down" class="w-3 h-3" />
                                    </button>                                     
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:text-neutral-400" wire:click="sortBy('generated_by')">
                                <div class="flex items-center space-x-1">
                                    <span>Generated By</span>
                                    <button wire:click="sortBy('generated_by')" class="hover:bg-gray-100 p-1 rounded">
                                        <flux:icon name="chevron-up-down" class="w-3 h-3" />
                                    </button>                                     
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:text-neutral-400" wire:click="sortBy('generated_at')">
                                <div class="flex items-center space-x-1">
                                    <span>Generated At</span>
                                    <button wire:click="sortBy('generated_at')" class="hover:bg-gray-100 p-1 rounded">
                                        <flux:icon name="chevron-up-down" class="w-3 h-3" />
                                    </button>                                     
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                        @forelse($reportLogs as $report)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors duration-200 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:badge color="{{ $report->report_type == 'assets' ? 'green' : ($report->report_type == 'vehicles' ? 'orange' : ($report->report_type == 'bookings' ? 'blue' : 'fuchsia')) }}">
                                        {{ ucfirst($report->report_type) }}
                                    </flux:badge> 
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900 dark:text-neutral-200 uppercase">
                                        {{ $report->report_format }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-neutral-200 font-medium">
                                        {{ number_format($report->record_count) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <flux:avatar size="xs" color="auto" name="{{ $report->generatedBy->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $report->generatedBy->name) : 'N/A' }}" />                                        
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-neutral-200">
                                                {{ $report->generatedBy->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $report->generatedBy->name) : 'N/A' }}                                                
                                            </div>
                                            <div class="text-xs text-gray-500">                                            
                                                {{ $report->generatedBy?->email ?? 'N/A' }}
                                            </div>                                                
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-neutral-200">
                                        {{ \Carbon\Carbon::parse($report->generated_at)->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($report->generated_at)->format('h:i A') }}
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center">
                                        <flux:tooltip content="View Report">
                                            <a href="{{ url('/reports/view/' . $report->id) }}" target="_blank" rel="noopener noreferrer">
                                                <flux:button size="sm" variant="ghost">
                                                    <flux:icon name="eye" class="w-4 h-4"/>
                                                </flux:button>
                                            </a>                                      
                                        </flux:tooltip>

                                        <flux:tooltip content="Download">
                                            <flux:button size="sm" wire:click="downloadReport({{ $report->id }})" variant="ghost">
                                                <flux:icon name="arrow-down-tray" class="w-4 h-4"/>
                                            </flux:button>                                        
                                        </flux:tooltip>

                                        <flux:tooltip content="Sent to Email">
                                            <flux:button size="sm" wire:click="emailReport({{ $report->id }})" variant="ghost">
                                                <flux:icon name="envelope"  class="w-4 h-4"/>
                                            </flux:button>                                           
                                        </flux:tooltip>

                                        @can('report.delete')
                                        <flux:tooltip content="Delete">
                                            <flux:button size="sm" wire:click="deleteReport({{ $report->id }})" variant="ghost">
                                                <flux:icon name="trash"  class="w-4 h-4"/>
                                            </flux:button>
                                        </flux:tooltip>
                                        @endcan                                    
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-500 mb-2">No reports generated yet</h3>
                                        <p class="text-gray-400">Generate your first report using the form above</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($reportLogs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reportLogs->links() }}
            </div>
        @endif
    </div>
</div>