<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Asset Management</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">Manage all your assets from one centralized location</p>
        <flux:separator variant="subtle" class="my-4" />
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4 lg:gap-5 mb-6 sm:mb-8">
        <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-blue-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Meeting Rooms</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900">{{ $stats['meeting_rooms'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-green-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Vehicles</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900">{{ $stats['vehicles'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-fuchsia-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-fuchsia-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">IT Assets</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900">{{ $stats['it_assets'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-yellow-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Bookings</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900">{{ $stats['total_bookings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 sm:p-4 lg:p-6">
            <div class="flex items-center space-x-2 sm:space-x-3 lg:space-x-4">
                <div class="p-1.5 sm:p-2 bg-red-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Active Bookings</p>
                    <p class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900">{{ $stats['active_bookings'] }}</p>
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
                    <flux:menu.item wire:click="createAsset('vehicle')" icon="truck">
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
    <div class="border border-gray-200 rounded-xl shadow-2xs p-4 dark:bg-neutral-800 dark:border-neutral-700 overflow-hidden">
        <div class="min-w-full overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assets as $asset)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $asset['name'] }}</div>
                                @if(!empty($asset['model']->plate_number))
                                    <div class="text-sm text-gray-500">{{ Str::limit($asset['model']->plate_number, 50) }}</div>                             
                                @else
                                    <div class="text-sm text-gray-500">{{ Str::limit($asset['model']->notes, 50) }}</div>
                                @endif                               
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($asset['type'] === 'meeting_room') bg-blue-100 text-blue-800
                                    @elseif($asset['type'] === 'vehicle') bg-green-100 text-green-800
                                    @else bg-fuchsia-100 text-fuchsia-800
                                    @endif
                                ">
                                    {{ $asset['type_label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $asset['details'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($asset['status'] === 'Available') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif
                                ">
                                    {{ $asset['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                                {{ $asset['bookings_count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                @can('asset.edit')                                
                                    <flux:button size="sm" wire:click="editAsset('{{ $asset['type'] }}', {{ $asset['id'] }})">Edit</flux:button>
                                @endcan
                                
                                @can('asset.delete')
                                    <flux:button size="sm" variant="danger" wire:click="deleteAsset('{{ $asset['type'] }}', {{ $asset['id'] }})">Delete</flux:button>
                                @endcan
                                
                                @cannot('asset.edit')
                                @cannot('asset.delete')
                                <span class="text-gray-400 text-sm">No actions available</span>
                                @endcannot
                                @endcannot
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No assets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $editingAsset ? 'Edit' : 'Add' }} 
                        @if($assetType === 'meeting_room') Meeting Room
                        @elseif($assetType === 'vehicle') Vehicle
                        @else IT Asset
                        @endif
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveAsset">
                    @if($assetType === 'meeting_room')
                        <!-- Meeting Room Form -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Room Name</label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    wire:model="name" 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required
                                >
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                                <input 
                                    type="text" 
                                    id="location" 
                                    wire:model="location" 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                @error('location') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
                                <input 
                                    type="number" 
                                    id="capacity" 
                                    wire:model="capacity" 
                                    min="1"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                @error('capacity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="has_projector" 
                                    wire:model="has_projector"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="has_projector" class="ml-2 block text-sm text-gray-900">Has Projector</label>
                            </div>
                        </div>

                    @elseif($assetType === 'vehicle')
                        <!-- Vehicle Form -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                                <input 
                                    type="text" 
                                    id="model" 
                                    wire:model="model" 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required
                                >
                                @error('model') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="plate_number" class="block text-sm font-medium text-gray-700">Plate Number</label>
                                <input 
                                    type="text" 
                                    id="plate_number" 
                                    wire:model="plate_number" 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required
                                >
                                @error('plate_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="capacity" class="block text-sm font-medium text-gray-700">Passenger Capacity</label>
                                <input 
                                    type="number" 
                                    id="capacity" 
                                    wire:model="capacity" 
                                    min="1"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                @error('capacity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="driver_name" class="block text-sm font-medium text-gray-700">Driver Name</label>
                                <input 
                                    type="text" 
                                    id="driver_name" 
                                    wire:model="driver_name" 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                @error('driver_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                    @else
                        <!-- IT Asset Form -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Asset Name</label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    wire:model="name" 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    required
                                >
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="asset_tag" class="block text-sm font-medium text-gray-700">Asset Tag</label>
                                <input 
                                    type="text" 
                                    id="asset_tag" 
                                    wire:model="asset_tag" 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                @error('asset_tag') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                                <input 
                                    type="text" 
                                    id="location" 
                                    wire:model="location" 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                @error('location') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="specs" class="block text-sm font-medium text-gray-700">Specifications</label>
                                <textarea 
                                    id="specs" 
                                    wire:model="specs" 
                                    rows="3"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                ></textarea>
                                @error('specs') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Notes field for all asset types -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea 
                            id="notes" 
                            wire:model="notes" 
                            rows="3"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        ></textarea>
                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex justify-end space-x-3">
                        <button 
                            type="button" 
                            wire:click="closeModal" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            {{ $editingAsset ? 'Update' : 'Create' }} Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
@endpush