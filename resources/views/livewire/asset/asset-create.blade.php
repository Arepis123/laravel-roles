<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('assets.index') }}" class="hover:text-gray-900">Assets</a>
            <flux:icon name="chevron-right" class="h-4 w-4" />
            <span class="font-medium">Create Asset</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Asset</h1>
        <p class="text-gray-600">Add a new asset to your inventory</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <form wire:submit="save" class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <flux:input 
                                wire:model="name" 
                                label="Asset Name" 
                                placeholder="Enter asset name"
                                required
                            />
                        </div>
                        <div>
                            <flux:select wire:model.live="asset_type" label="Asset Type" required>
                                <flux:select.option value="">Select Type</flux:select.option>
                                @foreach($assetTypes as $key => $value)
                                    <flux:select.option value="{{ $key }}">{{ $value }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="flex space-x-2">
                            <flux:input 
                                wire:model="asset_code" 
                                label="Asset Code" 
                                placeholder="Auto-generated if empty"
                                class="flex-1"
                            />
                            <div class="flex items-end">
                                <flux:button 
                                    type="button"
                                    wire:click="generateAssetCode"
                                    variant="outline"
                                    icon="refresh-cw"
                                    size="sm"
                                >
                                    Generate
                                </flux:button>
                            </div>
                        </div>
                        <div>
                            <flux:select wire:model="status" label="Status" required>
                                @foreach($assetStatuses as $key => $value)
                                    <flux:select.option value="{{ $key }}">{{ $value }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div>
                            <flux:input 
                                wire:model="location" 
                                label="Location" 
                                placeholder="Asset location"
                            />
                        </div>
                        <div>
                            <flux:input 
                                wire:model="capacity" 
                                type="number"
                                label="Capacity" 
                                placeholder="Number of people/units"
                                min="1"
                            />
                        </div>
                        <div class="md:col-span-2">
                            <flux:textarea 
                                wire:model="description" 
                                label="Description" 
                                placeholder="Asset description"
                                rows="3"
                            />
                        </div>
                    </div>
                </div>

                <!-- Financial Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium mb-4">Financial Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <flux:input 
                                wire:model="purchase_date" 
                                type="date"
                                label="Purchase Date"
                            />
                        </div>
                        <div>
                            <flux:input 
                                wire:model="purchase_price" 
                                type="number"
                                step="0.01"
                                label="Purchase Price"
                                placeholder="0.00"
                                min="0"
                            />
                        </div>
                        <div>
                            <flux:input 
                                wire:model="depreciation_rate" 
                                type="number"
                                step="0.01"
                                label="Depreciation Rate (%/year)"
                                placeholder="0.00"
                                min="0"
                                max="100"
                            />
                        </div>
                    </div>
                </div>

                <!-- Maintenance Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium mb-4">Maintenance</h3>
                    <div>
                        <flux:input 
                            wire:model="maintenance_schedule" 
                            label="Maintenance Schedule" 
                            placeholder="e.g., Monthly, Quarterly, Annually"
                        />
                    </div>
                </div>

                <!-- Asset Image -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium mb-4">Asset Image</h3>
                    <div>
                        <flux:input 
                            wire:model="image" 
                            type="file"
                            label="Upload Image"
                            accept="image/*"
                        />
                        @if ($image)
                            <div class="mt-4">
                                <img src="{{ $image->temporaryUrl() }}" 
                                     alt="Preview" 
                                     class="h-32 w-32 object-cover rounded-lg">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Specifications -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium mb-4">Specifications</h3>
                    
                    @if(count($specifications) > 0)
                        <div class="space-y-2 mb-4">
                            @foreach($specifications as $key => $value)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <span class="font-medium">{{ $key }}:</span> {{ $value }}
                                    </div>
                                    <flux:button 
                                        type="button"
                                        wire:click="removeSpecification('{{ $key }}')"
                                        variant="ghost"
                                        size="sm"
                                        icon="x"
                                    />
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex space-x-2">
                        <flux:input 
                            wire:model="newSpecKey" 
                            placeholder="Specification name"
                            class="flex-1"
                        />
                        <flux:input 
                            wire:model="newSpecValue" 
                            placeholder="Value"
                            class="flex-1"
                        />
                        <flux:button 
                            type="button"
                            wire:click="addSpecification"
                            variant="outline"
                            icon="plus"
                        >
                            Add
                        </flux:button>
                    </div>
                </div>

                <!-- Booking Settings -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium mb-4">Booking Settings</h3>
                    
                    <div class="mb-4">
                        <flux:checkbox wire:model="is_bookable" label="This asset can be booked" />
                    </div>

                    @if($is_bookable)
                        <div class="space-y-4">
                            <h4 class="font-medium">Booking Rules</h4>
                            
                            @if(count($booking_rules) > 0)
                                <div class="space-y-2 mb-4">
                                    @foreach($booking_rules as $key => $value)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex-1">
                                                <span class="font-medium">{{ $key }}:</span> {{ $value }}
                                            </div>
                                            <flux:button 
                                                type="button"
                                                wire:click="removeBookingRule('{{ $key }}')"
                                                variant="ghost"
                                                size="sm"
                                                icon="x"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex space-x-2">
                                <flux:input 
                                    wire:model="newRuleKey" 
                                    placeholder="Rule name (e.g., Max Duration)"
                                    class="flex-1"
                                />
                                <flux:input 
                                    wire:model="newRuleValue" 
                                    placeholder="Rule value (e.g., 4 hours)"
                                    class="flex-1"
                                />
                                <flux:button 
                                    type="button"
                                    wire:click="addBookingRule"
                                    variant="outline"
                                    icon="plus"
                                >
                                    Add Rule
                                </flux:button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3">
                    <flux:button 
                        href="{{ route('assets.index') }}" 
                        variant="ghost"
                    >
                        Cancel
                    </flux:button>
                    <flux:button 
                        type="submit" 
                        variant="primary"
                        icon="check"
                    >
                        Create Asset
                    </flux:button>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Asset Type Info -->
            @if($asset_type)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium mb-4">{{ $assetTypes[$asset_type] }} Guidelines</h3>
                    <div class="text-sm text-gray-600 space-y-2">
                        @switch($asset_type)
                            @case('meeting_room')
                                <p>• Include room capacity</p>
                                <p>• Specify available equipment</p>
                                <p>• Add location/floor information</p>
                                <p>• Set booking time limits</p>
                                @break
                            @case('vehicle')
                                <p>• Include make, model, year</p>
                                <p>• Specify seating capacity</p>
                                <p>• Add license plate number</p>
                                <p>• Set maintenance schedule</p>
                                @break
                            @case('it_equipment')
                                <p>• Include brand and model</p>
                                <p>• Specify technical specs</p>
                                <p>• Add serial number</p>
                                <p>• Set usage restrictions</p>
                                @break
                        @endswitch
                    </div>
                </div>
            @endif

            <!-- Tips -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-blue-900 mb-2">Tips</h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Asset codes are auto-generated if not provided</li>
                    <li>• Upload a clear image for easy identification</li>
                    <li>• Set appropriate booking rules to manage usage</li>
                    <li>• Regular maintenance schedules help track upkeep</li>
                </ul>
            </div>
        </div>
    </div>
</div>