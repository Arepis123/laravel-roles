<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Asset Management</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">Manage all your assets from one centralized location</p>
        <flux:separator variant="subtle" class="my-4" />
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4 lg:gap-5 mb-4 sm:mb-6">
        <!-- Meeting Rooms Card -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4 cursor-pointer hover:shadow-xs hover:scale-102 transition-all"
             wire:click="$set('selectedStatType', 'meeting_rooms')" 
             wire:click="loadStatsModalData" 
             wire:click="$set('showStatsModal', true)">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-blue-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Meeting Rooms</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900 dark:text-blue-400">{{ $stats['meeting_rooms'] }}</p>
                </div>
            </div>
        </div>

        <!-- Vehicles Card -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4 cursor-pointer hover:shadow-xs hover:scale-102 transition-all"
             wire:click="openStatsModal('vehicles')">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-green-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Vehicles</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900 dark:text-green-400">{{ $stats['vehicles'] }}</p>
                </div>
            </div>
        </div>

        <!-- IT Assets Card -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4 cursor-pointer hover:shadow-xs hover:scale-102 transition-all"
             wire:click="openStatsModal('it_assets')">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-fuchsia-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-fuchsia-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">IT Assets</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900 dark:text-fuchsia-400">{{ $stats['it_assets'] }}</p>
                </div>
            </div>
        </div>

        <!-- Available Assets Card -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4 cursor-pointer hover:shadow-xs hover:scale-102 transition-all"
             wire:click="openStatsModal('available_assets')">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-lime-200 dark:bg-lime-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Available Assets</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900 dark:text-emerald-400">{{ $stats['available_assets'] }}</p>
                </div>
            </div>
        </div>

        <!-- Active Bookings Card -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4 cursor-pointer hover:shadow-xs hover:scale-102 transition-all"
             wire:click="openStatsModal('active_bookings')">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-amber-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Active Bookings</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900 dark:text-red-400">{{ $stats['active_bookings'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div x-data="{ visible: true }" x-show="visible" x-collapse>
            <div x-show="visible" x-transition>
                <flux:callout icon="check-circle" variant="success" heading="{{ session('success') }}">                  
                    <x-slot name="controls">
                        <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                    </x-slot>
                </flux:callout>
            </div>
        </div>  
    @endif

    @if (session()->has('error'))
        <div x-data="{ visible: true }" x-show="visible" x-collapse>
            <div x-show="visible" x-transition>
                <flux:callout icon="x-circle" variant="danger" heading="{{ session('error') }}">                  
                    <x-slot name="controls">
                        <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                    </x-slot>
                </flux:callout>
            </div>
        </div>   
    @endif

    <!-- Filters and Actions -->
    <div class="border border-gray-200 rounded-xl p-4 dark:bg-neutral-800 dark:border-neutral-700 overflow-hidden my-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                <!-- Search -->
                <div class="flex-1">
                    <flux:input wire:model.live.debounce.300ms="search" variant="filled" icon="magnifying-glass" placeholder="Search assets..." class="w-full"/>                    
                </div>

                <!-- Filter -->
                <div>
                    <flux:dropdown>
                        <flux:button variant="filled" icon-trailing="chevron-down">
                            @if($selectedAssetType === 'all') All Assets
                            @elseif($selectedAssetType === 'meeting_rooms') Meeting Rooms
                            @elseif($selectedAssetType === 'vehicles') Vehicles
                            @elseif($selectedAssetType === 'it_assets') IT Assets
                            @else
                                All Assets
                            @endif
                        </flux:button>
                        <flux:menu>
                            <flux:menu.item wire:click="$set('selectedAssetType', 'all')">All Assets</flux:menu.item>
                            <flux:menu.item wire:click="$set('selectedAssetType', 'meeting_rooms')">Meeting Rooms</flux:menu.item>
                            <flux:menu.item wire:click="$set('selectedAssetType', 'vehicles')">Vehicles</flux:menu.item>
                            <flux:menu.item wire:click="$set('selectedAssetType', 'it_assets')">IT Assets</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>                   
                </div>
            </div>

            <!-- Add Asset Dropdown -->
            @can('asset.create')
            <flux:dropdown>
                <flux:button variant="filled" icon-trailing="chevron-down" class="bg-blue-600 hover:bg-blue-700">
                    <flux:icon.plus class="size-4" />
                    Add Asset
                </flux:button>
                <flux:menu>
                    <flux:menu.item wire:click="createAsset('meeting_room')" icon="building-office">
                        Add Meeting Room
                    </flux:menu.item>
                    <flux:menu.item wire:click="createAsset('vehicle')" icon="car">
                        Add Vehicle
                    </flux:menu.item>
                    <flux:menu.item wire:click="createAsset('it_asset')" icon="computer-desktop">
                        Add IT Asset
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
            @endcan
        </div>
    </div>

    <div class="border border-gray-200 rounded-xl shadow-2xs overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            <div class="flex items-center gap-1">
                                {{ __('No') }}
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            <div class="flex items-center gap-1">
                                {{ __('Asset') }}
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            <div class="flex items-center gap-1">
                                {{ __('Type') }}
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            <div class="flex items-center gap-1">
                                {{ __('Status') }}
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            <div class="flex items-center gap-1">
                                {{ __('Bookings') }}
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            <div class="flex items-center gap-1">
                                {{ __('Actions') }}
                            </div>
                        </th>                      
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-neutral-200">
                                    {{ $loop->iteration }}
                                </div>                                
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-neutral-200">
                                    {{ $asset['name'] }}
                                    @if(!empty($asset['model']->plate_number))
                                        <div class="text-xs text-gray-500 block">{{ Str::limit($asset['model']->plate_number, 50) }}</div>                             
                                    @else
                                        <div class="text-xs text-gray-500 block">{{ Str::limit($asset['model']->notes, 50) }}</div>
                                    @endif
                                    
                                    @if($asset['type'] === 'vehicle' && !empty($asset['model']->allowed_positions))
                                        <div class="text-xs text-blue-600 block mt-1">
                                            Restricted to: {{ implode(', ', $asset['model']->allowed_positions) }}
                                        </div>
                                    @elseif($asset['type'] === 'vehicle')
                                        <div class="text-xs text-green-600 block mt-1">
                                            Available to all positions
                                        </div>
                                    @endif                                    
                                </div>                                
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge size="sm" icon="{{ $asset['type'] === 'vehicle' ? 'car' : ($asset['type'] === 'meeting_room' ? 'building-office' : 'computer-desktop') }}" color="{{ $asset['type'] === 'vehicle' ? 'green' : ($asset['type'] === 'meeting_room' ? 'blue' : 'fuchsia') }}">
                                    @if($asset['type'] === 'vehicle')
                                        {{ __('Vehicle') }}
                                    @elseif($asset['type'] === 'meeting_room')
                                        {{ __('Meeting Room') }}
                                    @else
                                        {{ __('IT Asset') }}
                                    @endif
                                </flux:badge>                                   
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge size="sm">
                                    {{ $asset['status'] }}
                                </flux:badge>                               
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-medium text-gray-900 dark:text-neutral-200">
                                    {{ $asset['bookings_count'] }}
                                </div>                                
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <flux:button.group>
                                    @can('asset.edit')
                                    <flux:button size="sm" wire:click="editAsset('{{ $asset['type'] }}', {{ $asset['id'] }})" variant="ghost">
                                        <flux:icon name="pencil" class="w-4 h-4" />
                                    </flux:button>
                                    @endcan        
                                    @can('asset.delete')
                                    <flux:button size="sm" wire:click="deleteAsset('{{ $asset['type'] }}', {{ $asset['id'] }})" variant="ghost">
                                        <flux:icon name="trash" class="w-4 h-4" />
                                    </flux:button>
                                    @endcan                                                                                                                                      
                                </flux:button.group>                               
                            </td>                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="car" class="w-8 h-8 text-gray-400" />
                                    <div class="text-gray-500">No assets found</div>                                    
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>    

    <!-- Asset Modal -->
    <flux:modal wire:model="showModal" class="space-y-6">
        <div>
            <flux:heading size="lg">
                {{ $editingAsset ? 'Edit' : 'Add' }} 
                @if($assetType === 'meeting_room') Meeting Room
                @elseif($assetType === 'vehicle') Vehicle
                @else IT Asset
                @endif
            </flux:heading>
            <flux:subheading>
                @if($editingAsset)
                    Update the details for this asset.
                @else
                    Fill in the details to create a new asset.
                @endif
            </flux:subheading>
        </div>

        <form wire:submit.prevent="saveAsset" class="space-y-6">
            @if($assetType === 'meeting_room')
                <!-- Meeting Room Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Room Name</flux:label>
                        <flux:input wire:model="meeting_room_name" type="text" placeholder="Enter room name" />
                        <flux:error name="meeting_room_name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Location</flux:label>
                        <flux:input wire:model="meeting_room_location" type="text" placeholder="Enter location" />
                        <flux:error name="meeting_room_location" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Capacity</flux:label>
                        <flux:input type="number" wire:model="meeting_room_capacity" placeholder="Enter capacity" min="1" />
                        <flux:error name="meeting_room_capacity" />
                    </flux:field>

                    <flux:field variant="inline">
                        <flux:checkbox wire:model="has_projector" />
                        <flux:label>Has Projector / TV</flux:label>
                    </flux:field>                    
                </div>

            @elseif($assetType === 'vehicle')
                <!-- Vehicle Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Model</flux:label>
                        <flux:input wire:model="vehicle_model" type="text" placeholder="Enter vehicle model" />
                        <flux:error name="vehicle_model" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Plate Number</flux:label>
                        <flux:input wire:model="plate_number" type="text" placeholder="Enter plate number" />
                        <flux:error name="plate_number" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Passenger Capacity</flux:label>
                        <flux:input type="number" wire:model="vehicle_capacity" placeholder="Enter capacity" min="1" />
                        <flux:error name="vehicle_capacity" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Driver Name</flux:label>
                        <flux:input wire:model="driver_name" type="text" placeholder="Enter driver name (optional)" />
                        <flux:error name="driver_name" />
                    </flux:field>
                </div>

                <!-- Position Restrictions Section -->
                <div class="mt-6">
                    <flux:field>
                        <flux:label>Position Access Restrictions</flux:label>
                        <flux:description>Select which positions can book this vehicle. Leave empty to allow all positions.</flux:description>
                        
                        <div class="mt-3 grid grid-cols-2 gap-4">
                            @foreach($this->getAvailablePositions() as $position)
                                <flux:field variant="inline">
                                    <flux:checkbox wire:model="allowed_positions" value="{{ $position }}" />
                                    <flux:label>{{ $position }}</flux:label>
                                </flux:field>
                            @endforeach
                        </div>
                        <flux:error name="allowed_positions" />
                    </flux:field>
                </div>

                <!-- Parking Requirements Section -->
                <div class="mt-6">
                    <flux:field variant="inline">
                        <flux:checkbox wire:model="parking_required" />
                        <flux:label>Require parking location after booking completion</flux:label>
                        <flux:description>When enabled, users will be asked to select their parking level when they mark this vehicle booking as done.</flux:description>
                    </flux:field>
                    <flux:error name="parking_required" />
                </div>

            @else
                <!-- IT Asset Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Asset Name</flux:label>
                        <flux:input wire:model="it_asset_name" type="text" placeholder="Enter asset name" />
                        <flux:error name="it_asset_name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Asset Tag</flux:label>
                        <flux:input wire:model="asset_tag" type="text" placeholder="Enter asset tag" />
                        <flux:error name="asset_tag" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Location</flux:label>
                        <flux:input wire:model="it_asset_location" type="text" placeholder="Enter location" />
                        <flux:error name="it_asset_location" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Specifications</flux:label>
                        <flux:textarea wire:model="specs" placeholder="Enter specifications" rows="3" />
                        <flux:error name="specs" />
                    </flux:field>
                </div>
            @endif

            <!-- Notes field for all asset types -->
            <flux:field>
                <flux:label>Notes</flux:label>
                @if($assetType === 'meeting_room')
                    <flux:textarea wire:model="meeting_room_notes" placeholder="Enter additional notes" rows="3" />
                    <flux:error name="meeting_room_notes" />
                @elseif($assetType === 'vehicle')
                    <flux:textarea wire:model="vehicle_notes" placeholder="Enter additional notes" rows="3" />
                    <flux:error name="vehicle_notes" />
                @else
                    <flux:textarea wire:model="it_asset_notes" placeholder="Enter additional notes" rows="3" />
                    <flux:error name="it_asset_notes" />
                @endif
            </flux:field>

            <!-- Modal Actions -->
            <div class="flex space-x-3 justify-end">
                <flux:button type="button" wire:click="closeModal" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editingAsset ? 'Update' : 'Create' }} Asset
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Stats Modal -->
    <flux:modal wire:model="showStatsModal" class="space-y-6" variant="flyout" position="right">
        <div>
            <flux:heading size="lg">
                @if($selectedStatType === 'meeting_rooms') Meeting Rooms
                @elseif($selectedStatType === 'vehicles') Vehicles
                @elseif($selectedStatType === 'it_assets') IT Assets
                @elseif($selectedStatType === 'available_assets') Available Assets
                @elseif($selectedStatType === 'active_bookings') Active Bookings
                @endif
            </flux:heading>
            <flux:subheading>
                @if($selectedStatType === 'active_bookings')
                    Assets currently being used
                @else
                    List of all {{ $selectedStatType === 'available_assets' ? 'available' : '' }} assets
                @endif
            </flux:subheading>
        </div>

        <div class="h-full overflow-y-auto">
            @if($selectedStatType === 'active_bookings')
                <!-- Active Bookings List -->
                @if(!empty($statsModalData) && count($statsModalData) > 0)
                    <div class="space-y-3">
                        @foreach($statsModalData as $booking)
                            <div class="bg-gray-100 dark:bg-neutral-700 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        @php
                                            $start = \Carbon\Carbon::parse($booking['start_time']);
                                            $end = \Carbon\Carbon::parse($booking['end_time']);
                                            $totalMinutes = $start->diffInMinutes($end);
                                            $totalHours = floor($totalMinutes / 60);
                                            $days = (int) $start->diffInDays($end);
                                            
                                            // Enhanced date display logic
                                            if ($days > 1) {
                                                // Multi-day booking
                                                if ($start->month === $end->month) {
                                                    // Same month: "Aug 14 - 17, 2025"
                                                    $dateDisplay = $start->format('M j') . ' - ' . $end->format('j, Y');
                                                } else {
                                                    // Different months: "Aug 29 - Sept 2, 2025"
                                                    $dateDisplay = $start->format('M j') . ' - ' . $end->format('M j, Y');
                                                }
                                            } else {
                                                // Same day booking
                                                $dateDisplay = $start->format('M d, Y');
                                            }
                                            
                                            // Duration calculation
                                            if ($totalMinutes < 60) {
                                                $durationText = $totalMinutes . 'm';
                                            } elseif ($totalHours < 24) {
                                                $hours = $totalHours;
                                                $minutes = $totalMinutes % 60;
                                                $durationText = $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
                                            } else {
                                                $hours = $totalHours % 24;
                                                $durationText = $days . 'd' . ($hours > 0 ? ' ' . $hours . 'h' : '');
                                            }
                                        @endphp                                        
                                        <h4 class="font-semibold text-gray-900 dark:text-white">
                                            {{ $booking['asset_name'] }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ $booking['asset_type_label'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $dateDisplay }}
                                        </p>
                                        @if($days === 0)
                                            {{-- Single day booking - show time range --}}
                                            <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $start->format('h:i A') }} - {{ $end->format('h:i A') }} ({{ $durationText }})
                                                </p>                                                 
                                            </div>
                                        @else
                                            {{-- Multi-day booking - show start and end times --}}
                                            <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $start->format('h:i A') }} → {{ $end->format('h:i A') }}
                                                </p>                                                
                                            </div>
                                        @endif                                                                          
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Booked by: {{ $booking['booked_by'] }}
                                        </p>
                                    </div>
                                    <flux:badge size="sm" color="red">In Use</flux:badge>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <flux:icon name="clock" class="w-8 h-8 text-gray-400 mx-auto mb-3" />
                        <p class="text-gray-500">No active bookings at the moment</p>
                    </div>
                @endif
            @else
                <!-- Assets List -->
                @if(!empty($statsModalData) && count($statsModalData) > 0)
                    <div class="space-y-3">
                        @foreach($statsModalData as $asset)
                            <div class="bg-gray-50 dark:bg-neutral-700 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">
                                            {{ $asset['name'] }}
                                        </h4>
                                        @if($asset['type'] === 'vehicle' && !empty($asset['model']->plate_number))
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                Plate: {{ $asset['model']->plate_number }}
                                            </p>
                                        @endif
                                        @if($asset['type'] === 'meeting_room' && !empty($asset['model']->location))
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                Location: {{ $asset['model']->location }}
                                            </p>
                                        @endif
                                        @if($asset['type'] === 'it_asset' && !empty($asset['model']->asset_tag))
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                Tag: {{ $asset['model']->asset_tag }}
                                            </p>
                                        @endif
                                        <!-- Latest Booking Information -->
                                        @if(!empty($asset['latest_booking']))
                                            <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded border-l-4 border-blue-400">
                                                <p class="text-xs text-blue-800 dark:text-blue-300 font-medium">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    Last used by: {{ $asset['latest_booking']['user_name'] }}
                                                </p>
                                                <p class="text-xs text-blue-600 dark:text-blue-400">
                                                    @if($asset['latest_booking']['start_time'] === $asset['latest_booking']['end_time'])
                                                        {{ $asset['latest_booking']['start_time'] }} • {{ ucfirst($asset['latest_booking']['status']) }}
                                                    @else
                                                        {{ $asset['latest_booking']['start_time'] }} - {{ $asset['latest_booking']['end_time'] }} • {{ ucfirst($asset['latest_booking']['status']) }}
                                                    @endif
                                                </p>
                                                @if(!empty($asset['latest_booking']['purpose']))
                                                    <p class="text-xs text-blue-600 dark:text-blue-400 truncate">
                                                        {{ Str::limit($asset['latest_booking']['purpose'], 40) }}
                                                    </p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                    </svg>
                                                    No past usage found
                                                </p>
                                            </div>
                                        @endif                                        
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Total bookings: {{ $asset['bookings_count'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <flux:icon name="inbox" class="w-8 h-8 text-gray-400 mx-auto mb-3" />
                        <p class="text-gray-500">No assets found</p>
                    </div>
                @endif
            @endif
        </div>

        <div class="flex justify-end">
            <flux:button wire:click="closeStatsModal" variant="ghost">
                Close
            </flux:button>
        </div>
    </flux:modal>
</div>

@push('scripts')
@endpush