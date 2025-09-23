<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Asset Management</h1>
            <p class="text-gray-600">Manage meeting rooms, vehicles, and IT equipment</p>
        </div>
        <flux:button href="{{ route('assets.create') }}" variant="primary" icon="plus">
            Add New Asset
        </flux:button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <flux:input 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search assets..."
                    icon="search"
                />
            </div>
            <div>
                <flux:select wire:model.live="typeFilter">
                    <flux:select.option value="">All Types</flux:select.option>
                    @foreach($assetTypes as $key => $value)
                        <flux:select.option value="{{ $key }}">{{ $value }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div>
                <flux:select wire:model.live="statusFilter">
                    <flux:select.option value="">All Statuses</flux:select.option>
                    @foreach($assetStatuses as $key => $value)
                        <flux:select.option value="{{ $key }}">{{ $value }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div>
                <flux:select wire:model.live="bookableFilter">
                    <flux:select.option value="">All Assets</flux:select.option>
                    <flux:select.option value="1">Bookable Only</flux:select.option>
                    <flux:select.option value="0">Non-Bookable</flux:select.option>
                </flux:select>
            </div>
        </div>
        
        <div class="flex justify-between items-center">
            <flux:button wire:click="clearFilters" variant="ghost" size="sm">
                Clear Filters
            </flux:button>
            
            @if(count($selectedAssets) > 0)
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">{{ count($selectedAssets) }} selected</span>
                    <flux:button 
                        wire:click="bulkDelete" 
                        wire:confirm="Are you sure you want to delete the selected assets?"
                        variant="danger" 
                        size="sm"
                    >
                        Delete Selected
                    </flux:button>
                </div>
            @endif
        </div>
    </div>

    <!-- Assets Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <flux:checkbox 
                                wire:model.live="selectAll"
                                :checked="count($selectedAssets) === $assets->count() && $assets->count() > 0"
                            />
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Asset
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Active Bookings
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <flux:checkbox 
                                    wire:model.live="selectedAssets" 
                                    value="{{ $asset->id }}"
                                />
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($asset->image)
                                        <img src="{{ Storage::url($asset->image) }}" 
                                             alt="{{ $asset->name }}" 
                                             class="h-10 w-10 rounded-lg object-cover mr-3">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                            <flux:icon name="cube" class="h-5 w-5 text-gray-400" />
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $asset->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $asset->asset_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge variant="outline">
                                    {{ $asset->type_name }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge 
                                    variant="{{ match($asset->status) {
                                        'available' => 'success',
                                        'in_use' => 'info',
                                        'maintenance' => 'warning',
                                        'damaged', 'retired' => 'danger',
                                        default => 'secondary'
                                    } }}"
                                >
                                    {{ $asset->status_name }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $asset->location ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $asset->active_bookings_count }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <flux:button 
                                        href="{{ route('assets.show', $asset->id) }}" 
                                        variant="ghost" 
                                        size="sm"
                                        icon="eye"
                                    >
                                        View
                                    </flux:button>
                                    <flux:button 
                                        href="{{ route('assets.edit', $asset->id) }}" 
                                        variant="ghost" 
                                        size="sm"
                                        icon="pencil"
                                    >
                                        Edit
                                    </flux:button>
                                    <flux:button 
                                        wire:click="deleteAsset({{ $asset->id }})" 
                                        wire:confirm="Are you sure you want to delete this asset?"
                                        variant="ghost" 
                                        size="sm"
                                        icon="trash"
                                        class="text-red-600 hover:text-red-700"
                                    >
                                        Delete
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <flux:icon name="cube" class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                                    <h3 class="text-lg font-medium mb-2">No assets found</h3>
                                    <p class="text-sm">Get started by creating your first asset.</p>
                                    <flux:button 
                                        href="{{ route('assets.create') }}" 
                                        variant="primary" 
                                        class="mt-4"
                                    >
                                        Add New Asset
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($assets->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $assets->links() }}
            </div>
        @endif
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <flux:icon name="cube" class="h-6 w-6 text-blue-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Assets</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assets->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <flux:icon name="check-circle" class="h-6 w-6 text-green-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Available</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $assets->where('status', 'available')->count() }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <flux:icon name="clock" class="h-6 w-6 text-yellow-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">In Use</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $assets->where('status', 'in_use')->count() }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <flux:icon name="wrench" class="h-6 w-6 text-orange-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Maintenance</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $assets->where('status', 'maintenance')->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>