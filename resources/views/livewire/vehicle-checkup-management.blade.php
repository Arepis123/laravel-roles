<div class="relative mb-6 w-full">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Vehicle Checkup Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Track and manage vehicle safety checkups and inspections</p>
            </div>
            <flux:modal.trigger name="checkup-form">
                <div class="flex justify-between items-center">
                    <flux:button variant="primary" wire:click="showAddForm" icon="plus" class="w-full sm:w-auto">
                        Add Checkup
                    </flux:button>
                </div>
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-6">
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Checkups</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_checkups']) }}</flux:text>
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
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Approved</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['approved']) }}</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">With Notes</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['approved_with_notes']) }}</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Rejected</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['rejected']) }}</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-orange-100 dark:bg-orange-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Need Maintenance</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['needs_maintenance']) }}</flux:text>
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
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 pt-4 mx-3">
                        <flux:field>
                            <flux:label>Vehicle</flux:label>
                            <flux:select variant="listbox" wire:model.live="filterVehicle" placeholder="All Vehicles">
                                @foreach($vehicles as $vehicle)
                                    <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->model }} ({{ $vehicle->plate_number }})</flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>Checkup Type</flux:label>
                            <flux:select variant="listbox" wire:model.live="filterCheckupType" placeholder="All Types">
                                <flux:select.option value="pre_trip">Pre-Trip</flux:select.option>
                                <flux:select.option value="post_trip">Post-Trip</flux:select.option>
                                <flux:select.option value="weekly">Weekly</flux:select.option>
                                <flux:select.option value="monthly">Monthly</flux:select.option>
                                <flux:select.option value="annual">Annual</flux:select.option>
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>Status</flux:label>
                            <flux:select variant="listbox" wire:model.live="filterStatus" placeholder="All Statuses">
                                <flux:select.option value="approved">Approved</flux:select.option>
                                <flux:select.option value="approved_with_notes">Approved with Notes</flux:select.option>
                                <flux:select.option value="rejected">Rejected</flux:select.option>
                                <flux:select.option value="needs_maintenance">Needs Maintenance</flux:select.option>
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

                    <!-- Export Buttons -->
                    <div class="flex gap-3 pt-4 mx-3">
                        <flux:button variant="filled" size="sm" wire:click="exportCheckupData('excel')" icon="document-arrow-down" class="bg-green-600 hover:bg-green-700">
                            Export Excel
                        </flux:button>
                        <flux:button variant="filled" size="sm" wire:click="exportCheckupData('pdf')" icon="document-arrow-down" class="bg-red-600 hover:bg-red-700">
                            Export PDF
                        </flux:button>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </div>

    <!-- Checkups Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th wire:click="sortBy('checked_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Date</span>
                                @if($sortField === 'checked_at')
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Checkup Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Inspector</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Failed Checks</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($checkupLogs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $log->checked_at->format('M j, Y') }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-neutral-200">{{ $log->vehicle->model ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">({{ $log->vehicle->plate_number ?? 'N/A' }})</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeColor = match($log->checkup_type) {
                                        'pre_trip' => 'lime',
                                        'post_trip' => 'cyan',
                                        'weekly' => 'yellow',
                                        'monthly' => 'purple',
                                        'annual' => 'indigo',
                                        default => 'zinc'
                                    };
                                @endphp
                                <flux:badge size="sm" color="{{ $badgeColor }}">{{ ucfirst(str_replace('_', ' ', $log->checkup_type)) }}</flux:badge>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $log->user->name ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->overall_status === 'approved')
                                    <flux:badge size="sm" color="green">Approved</flux:badge>
                                @elseif($log->overall_status === 'approved_with_notes')
                                    <flux:badge size="sm" color="yellow">Approved with Notes</flux:badge>
                                @elseif($log->overall_status === 'rejected')
                                    <flux:badge size="sm" color="red">Rejected</flux:badge>
                                @elseif($log->overall_status === 'needs_maintenance')
                                    <flux:badge size="sm" color="orange">Needs Maintenance</flux:badge>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-white">
                                @php
                                    $failedCount = 0;
                                    $checkFields = [
                                        'exterior_body_condition', 'exterior_lights', 'exterior_mirrors', 'exterior_windshield', 'exterior_tires',
                                        'interior_seats_seatbelts', 'interior_dashboard', 'interior_horn', 'interior_wipers', 'interior_ac', 'interior_cleanliness',
                                        'engine_oil', 'engine_coolant', 'engine_brake_fluid', 'engine_battery',
                                        'functional_brakes', 'functional_steering', 'functional_transmission', 'functional_emergency_kit'
                                    ];
                                    foreach ($checkFields as $field) {
                                        if ($log->$field === false || $log->$field === 0) {
                                            $failedCount++;
                                        }
                                    }
                                @endphp
                                @if($failedCount > 0)
                                    <span class="text-red-600 dark:text-red-400 font-medium">{{ $failedCount }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium">
                                <div class="flex space-x-2 justify-center">
                                    @can('vehicle.edit')
                                    <flux:button size="xs" wire:click="editCheckup({{ $log->id }})" variant="ghost">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </flux:button>
                                    @endcan
                                    @can('vehicle.delete')
                                    <flux:button size="xs" wire:click="deleteCheckup({{ $log->id }})" wire:confirm="Are you sure you want to delete this checkup?" variant="ghost">
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
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    <h3 class="font-medium text-gray-900 dark:text-white mb-1">No checkup records</h3>
                                    <p class="text-gray-500 dark:text-gray-400">Get started by recording your first vehicle checkup.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($checkupLogs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $checkupLogs->links() }}
            </div>
        @endif
    </div>

    <!-- FluxUI Modal -->
    <flux:modal name="checkup-form" class="max-w-6xl" variant="flyout">
        <form wire:submit="saveCheckup">
            <flux:heading size="lg" class="mb-6">
                {{ $editingCheckup ? 'View/Edit Vehicle Checkup' : 'Add New Vehicle Checkup' }}
            </flux:heading>

            <!-- Basic Information Section -->
            <div class="mb-6">
                <flux:heading size="base" class="mb-4 pb-2 border-b border-gray-200 dark:border-zinc-700">Basic Information</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <flux:field>
                        <flux:label>Vehicle <span class="text-red-500 ms-1">*</span></flux:label>
                        <flux:select wire:model.live="vehicle_id" placeholder="Select Vehicle">
                            @foreach($vehicles as $vehicle)
                                <flux:select.option value="{{ $vehicle->id }}">{{ $vehicle->model }} ({{ $vehicle->plate_number }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="vehicle_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Checkup Type <span class="text-red-500 ms-1">*</span></flux:label>
                        <flux:select wire:model.live="checkup_type" placeholder="Select Type">
                            <flux:select.option value="pre_trip">Pre-Trip Inspection</flux:select.option>
                            <flux:select.option value="post_trip">Post-Trip Inspection</flux:select.option>
                            <flux:select.option value="weekly">Weekly Checkup</flux:select.option>
                            <flux:select.option value="monthly">Monthly Checkup</flux:select.option>
                            <flux:select.option value="annual">Annual Inspection</flux:select.option>
                        </flux:select>
                        <flux:error name="checkup_type" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 gap-4 mb-4">
                    <flux:field>
                        <flux:label>Checkup Template</flux:label>
                        <flux:select wire:model.live="template_id" placeholder="Select Template (Optional)">
                            @foreach($templates as $template)
                                <flux:select.option value="{{ $template->id }}">
                                    {{ $template->name }}
                                    @if($template->is_default)
                                        <span class="text-xs text-green-600">(Default)</span>
                                    @endif
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:description>
                            @if($currentTemplate)
                                Using template: <strong>{{ $currentTemplate->name }}</strong> -
                                {{ count($currentTemplate->applicable_checks) }} checks applicable
                            @else
                                No template selected. All checks will be shown.
                            @endif
                        </flux:description>
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Odometer Reading (km)</flux:label>
                        <flux:input type="number" wire:model="odometer_reading" placeholder="Current km reading" min="0" max="9999999" />
                        <flux:error name="odometer_reading" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Fuel Level</flux:label>
                        <flux:select wire:model="fuel_level" placeholder="Select Fuel Level">
                            <flux:select.option value="1">1 Bar (Empty)</flux:select.option>
                            <flux:select.option value="2">2 Bars</flux:select.option>
                            <flux:select.option value="3">3 Bars</flux:select.option>
                            <flux:select.option value="4">4 Bars</flux:select.option>
                            <flux:select.option value="5">5 Bars</flux:select.option>
                            <flux:select.option value="6">6 Bars</flux:select.option>
                            <flux:select.option value="7">7 Bars</flux:select.option>
                            <flux:select.option value="8">8 Bars (Full)</flux:select.option>
                        </flux:select>
                        <flux:error name="fuel_level" />
                    </flux:field>
                </div>
            </div>

            <!-- Exterior Checks Section -->
            @if($this->isCheckApplicable('exterior_body_condition') || $this->isCheckApplicable('exterior_lights') || $this->isCheckApplicable('exterior_mirrors') || $this->isCheckApplicable('exterior_windshield') || $this->isCheckApplicable('exterior_tires'))
            <div class="mb-6">
                <flux:heading size="base" class="mb-4 pb-2 border-b border-gray-200 dark:border-zinc-700">Exterior Checks</flux:heading>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->isCheckApplicable('exterior_body_condition'))
                        <div>
                            <flux:checkbox wire:model.live="exterior_body_condition" label="Body Condition (No damage, dents, or rust)" />
                            @if(!$exterior_body_condition)
                                <flux:textarea wire:model="exterior_body_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif

                        @if($this->isCheckApplicable('exterior_lights'))
                        <div>
                            <flux:checkbox wire:model.live="exterior_lights" label="Lights (Headlights, taillights, indicators working)" />
                            @if(!$exterior_lights)
                                <flux:textarea wire:model="exterior_lights_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->isCheckApplicable('exterior_mirrors'))
                        <div>
                            <flux:checkbox wire:model.live="exterior_mirrors" label="Mirrors (Clean and properly adjusted)" />
                            @if(!$exterior_mirrors)
                                <flux:textarea wire:model="exterior_mirrors_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif

                        @if($this->isCheckApplicable('exterior_windshield'))
                        <div>
                            <flux:checkbox wire:model.live="exterior_windshield" label="Windshield (No cracks or damage)" />
                            @if(!$exterior_windshield)
                                <flux:textarea wire:model="exterior_windshield_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->isCheckApplicable('exterior_tires'))
                        <div>
                            <flux:checkbox wire:model.live="exterior_tires" label="Tires (Proper pressure, tread depth, no damage)" />
                            @if(!$exterior_tires)
                                <flux:textarea wire:model="exterior_tires_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Interior Checks Section -->
            @if($this->isCheckApplicable('interior_seats_seatbelts') || $this->isCheckApplicable('interior_dashboard') || $this->isCheckApplicable('interior_horn') || $this->isCheckApplicable('interior_wipers') || $this->isCheckApplicable('interior_ac') || $this->isCheckApplicable('interior_cleanliness'))
            <div class="mb-6">
                <flux:heading size="base" class="mb-4 pb-2 border-b border-gray-200 dark:border-zinc-700">Interior Checks</flux:heading>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->isCheckApplicable('interior_seats_seatbelts'))
                        <div>
                            <flux:checkbox wire:model.live="interior_seats_seatbelts" label="Seats & Seatbelts (Clean, functional, no tears)" />
                            @if(!$interior_seats_seatbelts)
                                <flux:textarea wire:model="interior_seats_seatbelts_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif

                        @if($this->isCheckApplicable('interior_dashboard'))
                        <div>
                            <flux:checkbox wire:model.live="interior_dashboard" label="Dashboard (All gauges working)" />
                            @if(!$interior_dashboard)
                                <flux:textarea wire:model="interior_dashboard_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->isCheckApplicable('interior_horn'))
                        <div>
                            <flux:checkbox wire:model.live="interior_horn" label="Horn (Working properly)" />
                            @if(!$interior_horn)
                                <flux:textarea wire:model="interior_horn_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif

                        @if($this->isCheckApplicable('interior_wipers'))
                        <div>
                            <flux:checkbox wire:model.live="interior_wipers" label="Wipers (Functional, blades in good condition)" />
                            @if(!$interior_wipers)
                                <flux:textarea wire:model="interior_wipers_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->isCheckApplicable('interior_ac'))
                        <div>
                            <flux:checkbox wire:model.live="interior_ac" label="Air Conditioning (Working properly)" />
                            @if(!$interior_ac)
                                <flux:textarea wire:model="interior_ac_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif

                        @if($this->isCheckApplicable('interior_cleanliness'))
                        <div>
                            <flux:checkbox wire:model.live="interior_cleanliness" label="Cleanliness (Interior clean and tidy)" />
                            @if(!$interior_cleanliness)
                                <flux:textarea wire:model="interior_cleanliness_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Engine & Fluids Checks Section -->
            @if($this->isCheckApplicable('engine_oil') || $this->isCheckApplicable('engine_coolant') || $this->isCheckApplicable('engine_brake_fluid') || $this->isCheckApplicable('engine_battery') || $this->isCheckApplicable('engine_washer_fluid'))
            <div class="mb-6">
                <flux:heading size="base" class="mb-4 pb-2 border-b border-gray-200 dark:border-zinc-700">Engine & Fluids Checks</flux:heading>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->isCheckApplicable('engine_oil'))
                        <div>
                            <flux:checkbox wire:model.live="engine_oil" label="Engine Oil (Proper level, no leaks)" />
                            @if(!$engine_oil)
                                <flux:textarea wire:model="engine_oil_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif

                        @if($this->isCheckApplicable('engine_coolant'))
                        <div>
                            <flux:checkbox wire:model.live="engine_coolant" label="Coolant (Proper level, no leaks)" />
                            @if(!$engine_coolant)
                                <flux:textarea wire:model="engine_coolant_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->isCheckApplicable('engine_brake_fluid'))
                        <div>
                            <flux:checkbox wire:model.live="engine_brake_fluid" label="Brake Fluid (Proper level)" />
                            @if(!$engine_brake_fluid)
                                <flux:textarea wire:model="engine_brake_fluid_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif

                        @if($this->isCheckApplicable('engine_battery'))
                        <div>
                            <flux:checkbox wire:model.live="engine_battery" label="Battery (Clean terminals, secure)" />
                            @if(!$engine_battery)
                                <flux:textarea wire:model="engine_battery_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->isCheckApplicable('engine_washer_fluid'))
                        <div>
                            <flux:checkbox wire:model.live="engine_washer_fluid" label="Windshield Washer Fluid (Proper level)" />
                            @if(!$engine_washer_fluid)
                                <flux:textarea wire:model="engine_washer_fluid_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Functional Tests Section -->
            @if($this->isCheckApplicable('functional_brakes') || $this->isCheckApplicable('functional_steering') || $this->isCheckApplicable('functional_transmission') || $this->isCheckApplicable('functional_emergency_kit'))
            <div class="mb-6">
                <flux:heading size="base" class="mb-4 pb-2 border-b border-gray-200 dark:border-zinc-700">Functional Tests</flux:heading>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->isCheckApplicable('functional_brakes'))
                        <div>
                            <flux:checkbox wire:model.live="functional_brakes" label="Brakes (Responsive, no unusual noise)" />
                            @if(!$functional_brakes)
                                <flux:textarea wire:model="functional_brakes_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif

                        @if($this->isCheckApplicable('functional_steering'))
                        <div>
                            <flux:checkbox wire:model.live="functional_steering" label="Steering (Smooth, no play)" />
                            @if(!$functional_steering)
                                <flux:textarea wire:model="functional_steering_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($this->isCheckApplicable('functional_transmission'))
                        <div>
                            <flux:checkbox wire:model.live="functional_transmission" label="Transmission (Smooth shifting)" />
                            @if(!$functional_transmission)
                                <flux:textarea wire:model="functional_transmission_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif

                        @if($this->isCheckApplicable('functional_emergency_kit'))
                        <div>
                            <flux:checkbox wire:model.live="functional_emergency_kit" label="Emergency Kit (Present and complete)" />
                            @if(!$functional_emergency_kit)
                                <flux:textarea wire:model="functional_emergency_kit_notes" placeholder="Describe issues found..." rows="2" class="mt-2" />
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Overall Assessment Section -->
            <div class="mb-6">
                <flux:heading size="base" class="mb-4 pb-2 border-b border-gray-200 dark:border-zinc-700">Overall Assessment</flux:heading>

                <flux:field class="mb-4">
                    <flux:label>Overall Status <span class="text-red-500 ms-1">*</span></flux:label>
                    <flux:select wire:model="overall_status" placeholder="Select Status">
                        <flux:select.option value="approved">Approved - Vehicle is safe to operate</flux:select.option>
                        <flux:select.option value="approved_with_notes">Approved with Notes - Minor issues noted</flux:select.option>
                        <flux:select.option value="rejected">Rejected - Vehicle unsafe to operate</flux:select.option>
                        <flux:select.option value="needs_maintenance">Needs Maintenance - Schedule service required</flux:select.option>
                    </flux:select>
                    <flux:error name="overall_status" />
                </flux:field>

                <flux:field>
                    <flux:label>General Notes / Recommendations</flux:label>
                    <flux:textarea wire:model="general_notes" placeholder="Additional observations, recommendations, or comments..." rows="4" maxlength="1000" />
                    <flux:error name="general_notes" />
                </flux:field>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-zinc-700">
                <flux:button variant="ghost" wire:click="cancelForm">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editingCheckup ? 'Update Checkup' : 'Save Checkup' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    @script
    <script>
        $wire.on('open-modal', () => {
            $flux.modal('checkup-form').show();
        });

        $wire.on('close-modal', () => {
            $flux.modal('checkup-form').close();
        });

        $wire.on('checkup-export', (event) => {
            const data = event[0]; // Livewire passes data as first array element

            const params = new URLSearchParams({
                vehicle: data.vehicle || '',
                checkup_type: data.checkup_type || '',
                status: data.status || '',
                date_from: data.date_from || '',
                date_to: data.date_to || '',
                format: data.format || 'excel'
            });

            // Create export URL
            const exportUrl = `/vehicle-checkup/export?${params}`;

            // Download the file
            window.open(exportUrl, '_blank');

            // Show success message
            $flux.toast({
                title: 'Export Started',
                body: `${data.format.toUpperCase()} export is being generated...`,
                variant: 'success'
            });
        });
    </script>
    @endscript
</div>
