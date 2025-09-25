<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Asset Management</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">Manage all your assets from one centralized location</p>
        <flux:separator variant="subtle" class="my-4" />
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-6">
        <!-- Meeting Rooms Card -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 cursor-pointer hover:shadow-md hover:scale-[1.02] hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200"
                   wire:click="openStatsModal('meeting_rooms')">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Meeting Rooms</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['meeting_rooms'] }}</flux:text>
                </div>
            </div>
        </flux:card>

        <!-- Vehicles Card -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 cursor-pointer hover:shadow-md hover:scale-[1.02] hover:border-green-300 dark:hover:border-green-600 transition-all duration-200"
                   wire:click="openStatsModal('vehicles')">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/>
                        <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="7" cy="17" r="2"/><path d="M9 17h6"/>
                        <circle stroke-linecap="round" stroke-linejoin="round" stroke-width="2" cx="17" cy="17" r="2"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Vehicles</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['vehicles'] }}</flux:text>
                </div>
            </div>
        </flux:card>

        <!-- IT Assets Card -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 cursor-pointer hover:shadow-md hover:scale-[1.02] hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-200"
                   wire:click="openStatsModal('it_assets')">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">IT Assets</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['it_assets'] }}</flux:text>
                </div>
            </div>
        </flux:card>

        <!-- Available Assets Card -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 cursor-pointer hover:shadow-md hover:scale-[1.02] hover:border-green-300 dark:hover:border-green-600 transition-all duration-200"
                   wire:click="openStatsModal('available_assets')">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Available Assets</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['available_assets'] }}</flux:text>
                </div>
            </div>
        </flux:card>

        <!-- Active Bookings Card -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 cursor-pointer hover:shadow-md hover:scale-[1.02] hover:border-orange-300 dark:hover:border-orange-600 transition-all duration-200"
                   wire:click="openStatsModal('active_bookings')">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-orange-100 dark:bg-orange-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Active Bookings</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['active_bookings'] }}</flux:text>
                </div>
            </div>
        </flux:card>
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
                <flux:button variant="primary">
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

    <!-- Assets Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('No') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Asset') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Type') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Access') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Status') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('QR Code') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Bookings') }}
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $loop->iteration }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white mt-1 block">{{ $asset['name'] }}</span>
                                    @if(!empty($asset['model']->plate_number))
                                        <span class="text-sm text-gray-500 font-normal">{{ Str::limit($asset['model']->plate_number, 50) }}</span>
                                    @else
                                        <span class="text-sm text-gray-500 font-normal">{{ Str::limit($asset['model']->notes, 50) }}</span>
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
                                @if($asset['type'] === 'vehicle')
                                    @php
                                        $hasPositions = !empty($asset['model']->allowed_positions);
                                        $hasUsers = !empty($asset['model']->allowed_users);
                                        $allowedUsers = $hasUsers ? \App\Models\User::whereIn('id', $asset['model']->allowed_users)->get() : collect();
                                    @endphp
                                    
                                    <div class="space-y-2">
                                        @if($hasPositions)
                                            <div>                                               
                                                <span class="text-sm text-gray-900 dark:text-white">{{ implode(', ', $asset['model']->allowed_positions) }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($hasUsers)
                                            <div class="flex items-center gap-2">
                                                <flux:avatar.group>
                                                    @foreach($allowedUsers->take(3) as $user)
                                                        <flux:avatar size="xs" tooltip="{{ $user->name }}" name="{{ $user->name }}" />
                                                    @endforeach
                                                    @if($allowedUsers->count() > 3)
                                                        <flux:avatar size="xs" tooltip="{{ $allowedUsers->count() - 3 }} more users">{{ $allowedUsers->count() - 3 }}+</flux:avatar>
                                                    @endif
                                                </flux:avatar.group>
                                            </div>
                                        @endif
                                        
                                        @if(!$hasPositions && !$hasUsers)
                                            <span class="text-sm text-gray-900 dark:text-white">All Users</span>
                                        @endif
                                    </div>
                                @else                                    
                                    <span class="text-sm text-gray-900 dark:text-white">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($asset['status'] === 'Available')
                                    <flux:text class="text-lime-500">{{ $asset['status'] }}</flux:text>
                                @elseif($asset['status'] === 'In Use')
                                    <flux:text color="red">{{ $asset['status'] }}</flux:text>
                                @else
                                    <flux:text>{{ $asset['status'] }}</flux:text>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <flux:button size="sm" wire:click="showQrCode('{{ $asset['type'] }}', {{ $asset['id'] }})" variant="ghost" tooltip="View QR Code">
                                        <flux:icon.qr-code name="qr-code" class="w-4 h-4" />
                                    </flux:button>                                    
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $asset['bookings_count'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <flux:button.group>
                                    @can('asset.edit')
                                    <flux:button size="sm" wire:click="editAsset('{{ $asset['type'] }}', {{ $asset['id'] }})" variant="ghost">
                                        <flux:icon name="pencil" class="w-4 h-4" />
                                    </flux:button>
                                    @endcan        
                                    @can('asset.delete')
                                    <flux:button size="sm" wire:click="confirmDelete('{{ $asset['type'] }}', {{ $asset['id'] }}, '{{ $asset['name'] }}')" variant="ghost">
                                        <flux:icon name="trash" class="w-4 h-4" />
                                    </flux:button>
                                    @endcan                                                                                                                                      
                                </flux:button.group>                               
                            </td>                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">No assets found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model="showDeleteModal" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Asset?</flux:heading>

                <flux:text class="mt-2">
                    <p>You're about to delete <strong>{{ $assetToDelete['name'] ?? '' }}</strong>.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:button wire:click="cancelDelete" variant="ghost">Cancel</flux:button>

                <flux:button wire:click="confirmDeleteAsset" variant="danger">Delete Asset</flux:button>
            </div>
        </div>
    </flux:modal>

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
                        <flux:description>Select which positions can book this vehicle. Users in these positions will have access.</flux:description>
                        
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

                <div class="my-2 mx-4">
                    <flux:separator text="and/or"/>
                </div>

                <!-- User-Specific Access Section -->
                <div class="mt-6">
                    <flux:field>
                        <flux:label>Additional User Access</flux:label>
                        <flux:description>Select specific users who can also book this vehicle (in addition to position-based access above).</flux:description>
                        
                        <div class="mt-3">
                            <flux:select wire:model="allowed_users" placeholder="Select users..." multiple variant="listbox">
                                @foreach($this->getAvailableUsers() as $user)
                                    <flux:select.option value="{{ $user->id }}">{{ $user->name }} ({{ $user->position }})</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                        <flux:error name="allowed_users" />
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


    <!-- QR Code Modal -->
    <flux:modal wire:model="showQrModal" class="w-full max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Asset QR Code</flux:heading>
                <flux:subheading>Scan this QR code to complete bookings for this asset</flux:subheading>
            </div>

            @if($selectedAssetForQr)
                <div class="text-center">
                    <div class="mb-4">
                        <flux:heading size="sm" class="text-gray-900 dark:text-white">
                            {{ $selectedAssetForQr->getAssetDisplayName() }}
                        </flux:heading>
                        <flux:subheading class="text-gray-600 dark:text-gray-400">
                            {{ class_basename($selectedAssetForQr) }}
                        </flux:subheading>
                    </div>

                    @if($selectedAssetForQr->getQrCodeIdentifier())
                        <div class="bg-white p-4 rounded-lg border border-gray-200 dark:border-gray-700 inline-block mb-4">
                            {!! $selectedAssetForQr->getQrCodeSvg(200) !!}
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-4 space-y-1">
                            <p>QR Code ID: {{ $selectedAssetForQr->getQrCodeIdentifier() }}</p>
                            <p>Users can scan this code to mark their bookings as complete</p>
                        </div>

                        <div class="flex justify-center gap-2 mb-4">
                            <flux:button size="sm" variant="primary" wire:click="redirectToQrManagement">
                                {{-- <flux:icon name="eye" class="w-4 h-4 mr-1" /> --}}
                                View More
                            </flux:button>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <flux:icon name="qr-code" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                            <p class="text-gray-500 dark:text-gray-400 mb-4">No QR code generated yet</p>
                            <flux:button wire:click="generateQrCode('{{ class_basename($selectedAssetForQr) }}', {{ $selectedAssetForQr->id }})" variant="primary">
                                <flux:icon name="plus" class="w-4 h-4 mr-1" />
                                Generate QR Code
                            </flux:button>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex justify-end">
            <flux:button wire:click="closeQrModal" variant="ghost">
                Close
            </flux:button>
        </div>
    </flux:modal>
</div>

@push('scripts')
@endpush