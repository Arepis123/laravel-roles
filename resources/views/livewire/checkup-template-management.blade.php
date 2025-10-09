<div class="relative mb-6 w-full">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Checkup Template Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Create and manage checkup templates for different vehicle types and inspection schedules</p>
            </div>
            <flux:modal.trigger name="template-form">
                <div class="flex justify-between items-center">
                    <flux:button variant="primary" wire:click="showAddForm" icon="plus" class="w-full sm:w-auto">
                        Add Template
                    </flux:button>
                </div>
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Templates</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_templates']) }}</flux:text>
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
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Active Templates</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['active_templates']) }}</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Default Templates</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['default_templates']) }}</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Inactive Templates</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['inactive_templates']) }}</flux:text>
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
                        Filters
                    </span>
                </flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-4 mx-3">
                        <flux:field>
                            <flux:label>Vehicle Type</flux:label>
                            <flux:select variant="listbox" wire:model.live="filterVehicleType" placeholder="All Vehicle Types">
                                <flux:select.option value="car">Car</flux:select.option>
                                <flux:select.option value="motorcycle">Motorcycle</flux:select.option>
                                <flux:select.option value="van">Van</flux:select.option>
                                <flux:select.option value="truck">Truck</flux:select.option>
                                <flux:select.option value="all">All Types</flux:select.option>
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>Checkup Type</flux:label>
                            <flux:select variant="listbox" wire:model.live="filterCheckupType" placeholder="All Checkup Types">
                                <flux:select.option value="pre_trip">Pre-Trip</flux:select.option>
                                <flux:select.option value="post_trip">Post-Trip</flux:select.option>
                                <flux:select.option value="weekly">Weekly</flux:select.option>
                                <flux:select.option value="monthly">Monthly</flux:select.option>
                                <flux:select.option value="annual">Annual</flux:select.option>
                                <flux:select.option value="all">All Types</flux:select.option>
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>Active Status</flux:label>
                            <flux:select variant="listbox" wire:model.live="filterActive" placeholder="All Statuses">
                                <flux:select.option value="1">Active</flux:select.option>
                                <flux:select.option value="0">Inactive</flux:select.option>
                            </flux:select>
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </div>

    <!-- Templates Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th wire:click="sortBy('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Name</span>
                                @if($sortField === 'name')
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vehicle Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Checkup Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Checks Count</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Default</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($templates as $template)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $template->name }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                    {{ $template->description ?? 'No description' }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $vehicleTypeColor = match($template->vehicle_type) {
                                        'car' => 'blue',
                                        'motorcycle' => 'orange',
                                        'van' => 'purple',
                                        'truck' => 'red',
                                        'all' => 'zinc',
                                        default => 'zinc'
                                    };
                                @endphp
                                <flux:badge size="sm" color="{{ $vehicleTypeColor }}">{{ ucfirst($template->vehicle_type) }}</flux:badge>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $checkupTypeColor = match($template->checkup_type) {
                                        'pre_trip' => 'lime',
                                        'post_trip' => 'cyan',
                                        'weekly' => 'yellow',
                                        'monthly' => 'purple',
                                        'annual' => 'indigo',
                                        'all' => 'zinc',
                                        default => 'zinc'
                                    };
                                @endphp
                                <flux:badge size="sm" color="{{ $checkupTypeColor }}">{{ ucfirst(str_replace('_', ' ', $template->checkup_type)) }}</flux:badge>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @php
                                    $checksCount = is_array($template->applicable_checks) ? count($template->applicable_checks) : 0;
                                @endphp
                                <span class="font-medium">{{ $checksCount }}</span> checks
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($template->is_active)
                                    <flux:badge size="sm" color="green">Active</flux:badge>
                                @else
                                    <flux:badge size="sm" color="red">Inactive</flux:badge>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($template->is_default)
                                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @can('vehicle.edit')
                                    <flux:button size="xs" wire:click="editTemplate({{ $template->id }})" variant="ghost" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </flux:button>

                                    <flux:button size="xs" wire:click="toggleActive({{ $template->id }})" variant="ghost" title="Toggle Active Status">
                                        @if($template->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </flux:button>

                                    <flux:button size="xs" wire:click="toggleDefault({{ $template->id }})" variant="ghost" title="Toggle Default">
                                        @if($template->is_default)
                                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                        @endif
                                    </flux:button>
                                    @endcan

                                    @can('vehicle.delete')
                                    <flux:button size="xs" wire:click="deleteTemplate({{ $template->id }})" wire:confirm="Are you sure you want to delete this template?" variant="ghost" title="Delete">
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
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <h3 class="font-medium text-gray-900 dark:text-white mb-1">No templates found</h3>
                                    <p class="text-gray-500 dark:text-gray-400">Get started by creating your first checkup template.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($templates->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $templates->links() }}
            </div>
        @endif
    </div>

    <!-- FluxUI Modal -->
    <flux:modal name="template-form" class="max-w-6xl" variant="flyout">
        <form wire:submit="saveTemplate">
            <flux:heading size="lg" class="mb-6">
                {{ $editingTemplate ? 'Edit Checkup Template' : 'Add New Checkup Template' }}
            </flux:heading>

            <!-- Basic Information Section -->
            <div class="mb-6">
                <flux:heading size="base" class="mb-4 pb-2 border-b border-gray-200 dark:border-zinc-700">Basic Information</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <flux:field>
                        <flux:label>Template Name <span class="text-red-500 ms-1">*</span></flux:label>
                        <flux:input wire:model="name" placeholder="e.g., Pre-Trip Car Inspection" maxlength="255" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Vehicle Type <span class="text-red-500 ms-1">*</span></flux:label>
                        <flux:select wire:model="vehicle_type" placeholder="Select Vehicle Type">
                            <flux:select.option value="car">Car</flux:select.option>
                            <flux:select.option value="motorcycle">Motorcycle</flux:select.option>
                            <flux:select.option value="van">Van</flux:select.option>
                            <flux:select.option value="truck">Truck</flux:select.option>
                            <flux:select.option value="all">All Vehicle Types</flux:select.option>
                        </flux:select>
                        <flux:error name="vehicle_type" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <flux:field>
                        <flux:label>Checkup Type <span class="text-red-500 ms-1">*</span></flux:label>
                        <flux:select wire:model="checkup_type" placeholder="Select Checkup Type">
                            <flux:select.option value="pre_trip">Pre-Trip Inspection</flux:select.option>
                            <flux:select.option value="post_trip">Post-Trip Inspection</flux:select.option>
                            <flux:select.option value="weekly">Weekly Checkup</flux:select.option>
                            <flux:select.option value="monthly">Monthly Checkup</flux:select.option>
                            <flux:select.option value="annual">Annual Inspection</flux:select.option>
                            <flux:select.option value="all">All Checkup Types</flux:select.option>
                        </flux:select>
                        <flux:error name="checkup_type" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Description</flux:label>
                    <flux:textarea wire:model="description" placeholder="Brief description of this template..." rows="3" maxlength="500" />
                    <flux:error name="description" />
                </flux:field>
            </div>

            <!-- Applicable Checks Section -->
            <div class="mb-6">
                <flux:heading size="base" class="mb-4 pb-2 border-b border-gray-200 dark:border-zinc-700">Applicable Checks</flux:heading>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select which checks should be included in this template</p>

                <!-- Exterior Checks -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg">
                    <flux:heading size="sm" class="mb-3 text-blue-600 dark:text-blue-400">Exterior Checks</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <flux:checkbox wire:model="applicable_checks" value="exterior_body_condition" label="Body Condition (No damage, dents, or rust)" />
                        <flux:checkbox wire:model="applicable_checks" value="exterior_lights" label="Lights (Headlights, taillights, indicators)" />
                        <flux:checkbox wire:model="applicable_checks" value="exterior_mirrors" label="Mirrors (Clean and properly adjusted)" />
                        <flux:checkbox wire:model="applicable_checks" value="exterior_windshield" label="Windshield (No cracks or damage)" />
                        <flux:checkbox wire:model="applicable_checks" value="exterior_tires" label="Tires (Proper pressure, tread depth, no damage)" />
                    </div>
                </div>

                <!-- Interior Checks -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg">
                    <flux:heading size="sm" class="mb-3 text-green-600 dark:text-green-400">Interior Checks</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <flux:checkbox wire:model="applicable_checks" value="interior_seats_seatbelts" label="Seats & Seatbelts (Clean, functional, no tears)" />
                        <flux:checkbox wire:model="applicable_checks" value="interior_dashboard" label="Dashboard (All gauges working)" />
                        <flux:checkbox wire:model="applicable_checks" value="interior_horn" label="Horn (Working properly)" />
                        <flux:checkbox wire:model="applicable_checks" value="interior_wipers" label="Wipers (Functional, blades in good condition)" />
                        <flux:checkbox wire:model="applicable_checks" value="interior_ac" label="Air Conditioning (Working properly)" />
                        <flux:checkbox wire:model="applicable_checks" value="interior_cleanliness" label="Cleanliness (Interior clean and tidy)" />
                    </div>
                </div>

                <!-- Engine Checks -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg">
                    <flux:heading size="sm" class="mb-3 text-orange-600 dark:text-orange-400">Engine & Fluids Checks</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <flux:checkbox wire:model="applicable_checks" value="engine_oil" label="Engine Oil (Proper level, no leaks)" />
                        <flux:checkbox wire:model="applicable_checks" value="engine_coolant" label="Coolant (Proper level, no leaks)" />
                        <flux:checkbox wire:model="applicable_checks" value="engine_brake_fluid" label="Brake Fluid (Proper level)" />
                        <flux:checkbox wire:model="applicable_checks" value="engine_battery" label="Battery (Clean terminals, secure)" />
                        <flux:checkbox wire:model="applicable_checks" value="engine_washer_fluid" label="Windshield Washer Fluid (Proper level)" />
                    </div>
                </div>

                <!-- Functional Checks -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-zinc-800 rounded-lg">
                    <flux:heading size="sm" class="mb-3 text-purple-600 dark:text-purple-400">Functional Tests</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <flux:checkbox wire:model="applicable_checks" value="functional_brakes" label="Brakes (Responsive, no unusual noise)" />
                        <flux:checkbox wire:model="applicable_checks" value="functional_steering" label="Steering (Smooth, no play)" />
                        <flux:checkbox wire:model="applicable_checks" value="functional_transmission" label="Transmission (Smooth shifting)" />
                        <flux:checkbox wire:model="applicable_checks" value="functional_emergency_kit" label="Emergency Kit (Present and complete)" />
                    </div>
                </div>

                <flux:error name="applicable_checks" />
            </div>

            <!-- Template Settings Section -->
            <div class="mb-6">
                <flux:heading size="base" class="mb-4 pb-2 border-b border-gray-200 dark:border-zinc-700">Template Settings</flux:heading>

                <div class="space-y-3">
                    <flux:checkbox wire:model="is_default" label="Set as Default Template" description="This template will be pre-selected for matching vehicle and checkup types" />
                    <flux:checkbox wire:model="is_active" label="Active Template" description="Only active templates are available for selection" />
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-zinc-700">
                <flux:button variant="ghost" wire:click="cancelForm">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editingTemplate ? 'Update Template' : 'Save Template' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    @script
    <script>
        $wire.on('open-modal', () => {
            $flux.modal('template-form').show();
        });

        $wire.on('close-modal', () => {
            $flux.modal('template-form').close();
        });
    </script>
    @endscript
</div>
