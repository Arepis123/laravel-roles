<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Asset Management</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">Manage all your assets from one centralized location</p>
        <flux:separator variant="subtle" class="my-4" />
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4 lg:gap-5 mb-6 sm:mb-8">
        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4">
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

        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4">
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

        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4">
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

        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-yellow-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 truncate">Total Bookings</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900 dark:text-yellow-400">{{ $stats['total_bookings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-red-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filters and Actions -->
    <div class="border border-gray-200 rounded-xl p-4 dark:bg-neutral-800 dark:border-neutral-700 overflow-hidden mb-4">
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
                                </div>                                
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge size="sm" icon="{{ $asset['type'] === 'vehicle' ? 'car' : ($asset['type'] === 'meeting_room' ? 'building-office' : 'computer-desktop') }}" color="{{ $asset['type'] === 'Vehicle' ? 'green' : ($asset['type'] === 'meeting_room' ? 'blue' : 'fuchsia') }}">
                                    {{ $asset['type'] }}
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

    <!-- FluxUI Modal -->
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
                        <flux:input wire:model="name" placeholder="Enter room name" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Location</flux:label>
                        <flux:input wire:model="location" placeholder="Enter location" />
                        <flux:error name="location" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Capacity</flux:label>
                        <flux:input type="number" wire:model="capacity" placeholder="Enter capacity" min="1" />
                        <flux:error name="capacity" />
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
                        <flux:input wire:model="model" placeholder="Enter vehicle model" />
                        <flux:error name="model" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Plate Number</flux:label>
                        <flux:input wire:model="plate_number" type="text" placeholder="Enter plate number" />
                        <flux:error name="plate_number" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Passenger Capacity</flux:label>
                        <flux:input type="number" wire:model="capacity" placeholder="Enter capacity" min="1" />
                        <flux:error name="capacity" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Driver Name</flux:label>
                        <flux:input wire:model="driver_name" placeholder="Enter driver name" />
                        <flux:error name="driver_name" />
                    </flux:field>
                </div>

            @else
                <!-- IT Asset Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Asset Name</flux:label>
                        <flux:input wire:model="name" placeholder="Enter asset name" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Asset Tag</flux:label>
                        <flux:input wire:model="asset_tag" placeholder="Enter asset tag" />
                        <flux:error name="asset_tag" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Location</flux:label>
                        <flux:input wire:model="location" placeholder="Enter location" />
                        <flux:error name="location" />
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
                <flux:textarea wire:model="notes" placeholder="Enter additional notes" rows="3" />
                <flux:error name="notes" />
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
</div>

@push('scripts')
@endpush