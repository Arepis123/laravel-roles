<div class="max-w-6xl mx-auto space-y-6">
    {{-- Header Section --}}
    <div class="flex items-center justify-between border-b pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Booking Details</h1>
            <p class="text-sm text-gray-600 dark:text-white">Booking ID: #{{ $booking->id }}</p>
        </div>
        
        {{-- Status Badge --}}
        <div class="flex items-center space-x-0">          
            <div class="flex flex-col gap-1">
                @if ($status == 'pending')
                    <flux:badge color="yellow" class="w-fit">
                        <flux:icon name="clock" class="w-4 h-4 mr-1" />
                        {{ ucfirst($status) }}
                    </flux:badge>
                @elseif ($status == 'approved')     
                    <flux:badge color="sky" class="w-fit">
                        <flux:icon name="square-check" class="w-4 h-4 mr-1" />
                        {{ ucfirst($status) }}
                    </flux:badge>  
                @elseif ($status == 'rejected')     
                    <flux:badge color="red" class="w-fit">
                        <flux:icon name="circle-x" class="w-4 h-4 mr-1" />
                        {{ ucfirst($status) }}
                    </flux:badge>                                                                                                
                @elseif ($status == 'cancelled')
                    <flux:badge color="zinc" class="w-fit">
                        <flux:icon name="ban" class="w-4 h-4 mr-1" />
                        {{ ucfirst($status) }}
                    </flux:badge> 
                @elseif ($status == 'done')
                    <flux:badge color="green" class="w-fit">
                        <flux:icon name="check-circle" class="w-4 h-4 mr-1" />
                        {{ ucfirst($status) }}
                    </flux:badge>
                @endif
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
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

    @if (session()->has('info'))
        <div x-data="{ visible: true }" x-show="visible" x-collapse>
            <div x-show="visible" x-transition>
                <flux:callout icon="information-circle" variant="sky" heading="{{ session('info') }}">                  
                    <x-slot name="controls">
                        <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                    </x-slot>
                </flux:callout>
            </div>
        </div>     
    @endif

    {{-- Status History Section --}}
    @if($showStatusHistory && $this->statusChangesCount > 0)
        <div class="border rounded-lg overflow-hidden">
            <div class="px-4 py-1 sm:px-6 sm:py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Status Timeline</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="relative pl-6 border-l-2 border-gray-200 dark:border-gray-700">
                    @foreach($this->statusHistory as $index => $change)
                        <div class="mb-8 last:mb-0">
                            <span @class([
                                'absolute -left-[11px] flex items-center justify-center w-5 h-5 rounded-full ring-4 ring-white dark:ring-gray-900',
                                'bg-gray-300 dark:bg-gray-900' => $index === 0,
                                'bg-gray-200 dark:bg-gray-700' => $index > 0,
                            ])>
                                <x-flux::icon name="check-circle" @class([
                                    'w-3 h-3',
                                    'text-primary-600 dark:text-primary-400' => $index === 0,
                                    'text-gray-600 dark:text-gray-400' => $index > 0,
                                ]) />
                            </span>
                            <div class="ml-2">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $change['reason'] ?? 'Status Updated' }}</p>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($change['changed_at'])->format('M d, Y, h:i A') }}</span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                    Status changed from 
                                    <span class="font-medium">{{ ucfirst($change['previous_status']) }}</span> to 
                                    <span class="font-medium">{{ ucfirst($change['status']) }}</span> 
                                    by {{ $change['changed_by_name'] ?? 'System' }}.
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Display Done Details if available --}}
    @if($booking->hasDoneDetails() && $status === 'done')
        <div class="border rounded-lg p-6 bg-green-50 dark:bg-green-900/20">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Completion Details</h3>
            
            @if($asset_type === 'vehicle')
                @php
                    $vehicleData = $this->vehicleCompletionData;
                @endphp
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Odometer Reading:</span>
                        <span class="font-medium">{{ number_format($vehicleData['odometer_reading'] ?? 0) }} km</span>
                    </div>
                    
                    {{-- Display Fuel Level --}}
                    @if(isset($vehicleData['fuel_level']))
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Fuel Level:</span>
                            <span class="font-medium">{{ $vehicleData['fuel_level'] }}/8</span>
                        </div>
                        
                        {{-- Visual fuel level indicator --}}
                        <div class="mt-2">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Empty</span>
                                <span>Full</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                @php
                                    $fuelPercentage = ($vehicleData['fuel_level'] / 8) * 100;
                                    $fuelColor = match(true) {
                                        $vehicleData['fuel_level'] <= 2 => 'bg-red-500',
                                        $vehicleData['fuel_level'] <= 4 => 'bg-yellow-500',
                                        $vehicleData['fuel_level'] <= 6 => 'bg-orange-500',
                                        default => 'bg-green-500'
                                    };
                                @endphp
                                <div class="{{ $fuelColor }} h-2 rounded-full transition-all duration-300" style="width: {{ $fuelPercentage }}%"></div>
                            </div>
                        </div>
                    @endif
                    
                    @if($vehicleData['fuel_filled'] ?? false)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center mb-3">
                                <span class="font-semibold text-gray-900 dark:text-white">Fuel Fill-ups</span>
                                <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                    {{ $vehicleData['fuel_logs']->count() }} fill-up{{ $vehicleData['fuel_logs']->count() > 1 ? 's' : '' }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                @foreach($vehicleData['fuel_logs'] as $index => $log)
                                    <div class="p-3 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Fill-up #{{ $index + 1 }}</span>
                                            <div class="flex items-center gap-4 text-sm">
                                                <div>
                                                    <span class="text-gray-600 dark:text-gray-400">Cost:</span>
                                                    <span class="font-medium ml-1">RM {{ number_format($log->fuel_cost, 2) }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                                                    <span class="font-medium ml-1">{{ number_format($log->fuel_amount, 1) }} L</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Total Summary --}}
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-blue-900 dark:text-blue-200">Total:</span>
                                        <div class="text-right">
                                            <div class="font-bold text-blue-900 dark:text-blue-200">
                                                RM {{ number_format($vehicleData['total_fuel_cost'], 2) }}
                                            </div>
                                            <div class="text-sm text-blue-700 dark:text-blue-300">
                                                {{ number_format($vehicleData['total_fuel_amount'], 1) }} L
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Fuel Filled:</span>
                            <span class="font-medium">No fuel fill-ups</span>
                        </div>
                    @endif
                    
                    {{-- Parking Information --}}
                    @if($booking->parking_level)
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-300">Parking Level:</span>
                                <span class="font-medium">Level B{{ $booking->parking_level }}</span>
                            </div>
                            @if($booking->parking_level == 1 && $booking->is_reserved_slot)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Reserved Slot:</span>
                                    <span class="font-medium text-blue-600 dark:text-blue-400">Yes</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <div class="text-sm">
                    <p class="text-gray-600 dark:text-gray-300 mb-2">Remarks:</p>
                    <p class="font-medium">{{ $booking->done_details['remarks'] ?? 'No remarks provided' }}</p>
                </div>
            @endif
            
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Completed by {{ $booking->done_details['completed_by_name'] ?? 'Unknown' }} 
                    on {{ \Carbon\Carbon::parse($booking->done_details['completed_at'] ?? now())->format('M d, Y, h:i A') }}
                </div>
            </div>
        </div>
    @endif

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Booking Details Section --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Asset Information --}}
            <div class="border rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Booking Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:heading>Type</flux:heading>
                        <flux:select wire:model.live="asset_type" placeholder="Select booking type" disabled>
                            @foreach ($this->assetTypeOptions as $option)
                                <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>            
                    </flux:field>
                            
                    <flux:field>
                        <flux:heading>Asset</flux:heading>
                        <flux:select wire:model="asset_id" placeholder="Select asset" disabled>
                            @foreach ($this->assetOptions as $asset)
                                <flux:select.option value="{{ $asset->id }}">{{ $asset->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    {{-- Plate Number for Vehicles --}}
                    @if($asset_type === 'vehicle' && $booking->asset && $booking->asset->plate_number)
                        <flux:field>
                            <flux:heading>Plate Number</flux:heading>
                            <flux:input value="{{ $booking->asset->plate_number }}" type="text" disabled/>
                        </flux:field>
                    @endif

                    {{-- Asset Tag for IT Assets --}}
                    @if($asset_type === 'it_asset' && $booking->asset && $booking->asset->asset_tag)
                        <flux:field>
                            <flux:heading>Asset Tag</flux:heading>
                            <flux:input value="{{ $booking->asset->asset_tag }}" type="text" disabled/>
                        </flux:field>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <flux:field>
                        <flux:heading>Start Time</flux:heading>
                        <flux:input wire:model="start_time" type="datetime-local" disabled/>
                    </flux:field>
                    <flux:field>
                        <flux:heading>End Time</flux:heading>
                        <flux:input wire:model="end_time" type="datetime-local" disabled/>
                    </flux:field>
                </div>                  
                  
                    
                <div class="grid gap-4 mt-3">
                    @if($capacity)
                        <flux:field>
                            <flux:heading>Capacity</flux:heading>
                            <flux:input wire:model="capacity" type="number" disabled/>
                        </flux:field>
                    @endif     
                    
                    <flux:field>
                        <flux:heading>Purpose</flux:heading>
                        <flux:textarea wire:model="purpose" rows="3" disabled/>
                    </flux:field>   
                </div>                                            
            </div>

            {{-- Passengers Section for Vehicles --}}
            @if($asset_type === 'vehicle' && $booking->passengers && count($booking->passengers) > 0)
                <div class="rounded-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">                    
                    <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-neutral-700">           
                        <flux:icon name="users" class="w-4 h-4 inline text-gray-500 dark:text-white me-1" />
                        <div class="text-left text-xs font-medium text-gray-500 dark:text-white tracking-wider uppercase inline">Passengers ({{ count($booking->passengers) }})</div>
                    </div>                                          
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($booking->passengers as $passengerId)
                                @php
                                    $passenger = \App\Models\User::find($passengerId);
                                @endphp
                                
                                @if($passenger)
                                    <div class="flex items-center p-2 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                        <div class="flex-shrink-0">                                            
                                            <flux:avatar size="xs" color="auto" name="{{ preg_replace('/\s+(BIN|BINTI|BT)\b.*/i', '', $passenger->name) }}" />                                        
                                        </div>
                                        <div class="ml-3 min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $passenger->name }}
                                            </p>
                                            @if($passenger->email)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                    {{ $passenger->email }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center p-2 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                                        <div class="flex-shrink-0">
                                            <flux:icon name="exclamation-triangle" class="w-5 h-5 text-red-500" />
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-red-900 dark:text-red-400">
                                                User not found (ID: {{ $passengerId }})
                                            </p>
                                            <p class="text-xs text-red-600 dark:text-red-500">
                                                This user may have been deleted
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        
                        {{-- Summary Info --}}
                        <flux:callout icon="information-circle" color="purple" class="mt-3" >
                            <flux:callout.heading class="flex gap-2 @max-md:flex-col items-start">Total Capacity: <flux:text>{{ $capacity ?? 'Not specified' }} ({{ $booking->user->name }} + {{ count($booking->passengers) }} passenger{{ count($booking->passengers) === 1 ? '' : 's' }})</flux:text></flux:callout.heading>                            
                        </flux:callout>                        
                    </div>

                </div>
            @elseif($asset_type === 'vehicle' && (!$booking->passengers || count($booking->passengers) === 0))
                <div class="border rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <flux:icon name="user-group" class="w-5 h-5 inline mr-2" />
                        Passengers
                    </h2>
                    
                    <div class="text-center py-6">
                        <flux:icon name="user-minus" class="w-8 h-8 text-gray-400 mx-auto mb-3" />
                        <p class="text-gray-500 dark:text-gray-400">No passengers selected for this vehicle booking</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Only the driver ({{ $booking->user->name }}) will be using the vehicle</p>
                    </div>
                </div>
            @endif

            {{-- Additional Services --}}
            @if(!empty($additional_booking))
                <div class="border rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Additional Services</h2>
                    
                    <flux:checkbox.group wire:model.live="additional_booking" label="">
                        <flux:checkbox 
                            label="Refreshment" 
                            value="refreshment" 
                            description="Meals such as breakfast, lunch, or snacks can be arranged before or during the session."
                            disabled
                        />

                        @if (in_array('refreshment', $additional_booking) && $refreshment_details)
                            <div class="ml-6 mb-4">
                                <flux:textarea wire:model.live="refreshment_details" placeholder="Refreshment details" disabled/>
                            </div>
                        @endif

                        <flux:checkbox 
                            label="Smart Monitor" 
                            value="smart_monitor" 
                            description="A smart monitor will be set up in the room before the meeting starts."
                            disabled
                        />

                        <flux:checkbox 
                            label="Laptop" 
                            value="laptop" 
                            description="A laptop will be prepared and set up for use during your session."
                            disabled
                        />

                        <flux:checkbox 
                            label="Technical Support" 
                            value="technical" 
                            description="IT will help in giving technical support." 
                            disabled
                        />                                                             

                        <flux:checkbox label="Email & Other Setup" value="email" description="IT technician will help setup email in the Outlook and other things requested by user." disabled/>
                    </flux:checkbox.group>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status Management --}}
            @if($this->canChangeStatus)
                <div class="border rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Manage Status</h3>
                    
                    <div class="space-y-4">
                        <flux:dropdown>
                            <flux:button icon="chevron-down" variant="outline" class="w-full">
                                Change Status
                            </flux:button>                                           
                            <flux:menu>
                                <flux:menu.radio.group position="bottom" align="center">     
                                    @if($status === 'approved' || $status === 'pending')                              
                                    <flux:menu.radio 
                                        :checked="$status == 'cancelled'" 
                                        wire:click="changeStatus('cancelled')"
                                    >
                                        Cancelled
                                    </flux:menu.radio>
                                    @endif
                                    @if($status === 'approved')                                    
                                    <flux:menu.radio 
                                        :checked="$status == 'done'" 
                                        wire:click="changeStatus('done')"
                                    >
                                        Done
                                    </flux:menu.radio>
                                    @endif
                                </flux:menu.radio.group>                               
                            </flux:menu>
                        </flux:dropdown>

                        @if($this->statusChangesCount > 0)
                            <div class="py-2">
                                <flux:button wire:click="toggleStatusHistory" class="w-full" icon="clock">                                                      
                                    View History
                                </flux:button>
                            </div>                            
                        @endif
                    </div>
                </div>
            @endif

            {{-- Booking Information --}}
            <div class="border rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Booking Information</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Booked by:</span>
                        <span class="font-medium text-end">{{ $booking->user->name }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Created:</span>
                        <span class="font-medium">{{ $booking->created_at->diffForHumans() }}</span>
                    </div>
                    
                    @if($booking->created_at != $booking->updated_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Last updated:</span>
                            <span class="font-medium">{{ $booking->updated_at->diffForHumans() }}</span>
                        </div>
                    @endif                    

                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Duration:</span>
                        @php
                            $start = \Carbon\Carbon::parse($start_time);
                            $end = \Carbon\Carbon::parse($end_time);

                            // Get precise diff
                            $duration = $start->diff($end)->format('%h hours %i minutes');
                        @endphp                        
                        <span class="font-medium">{{ $duration }}</span>
                    </div>

                    @if($booking->isUpcoming())
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Starts in:</span>
                            <span class="font-medium text-green-600">{{ \Carbon\Carbon::parse($start_time)->diffForHumans() }}</span>
                        </div>
                    @elseif($booking->isActive())
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Ends in:</span>
                            <span class="font-medium text-orange-600">{{ \Carbon\Carbon::parse($end_time)->diffForHumans() }}</span>
                        </div>
                    @elseif($booking->isPast())
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-300">Ended:</span>
                            <span class="font-medium text-gray-500">{{ \Carbon\Carbon::parse($end_time)->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="border rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                
                <div class="space-y-3">
                    <flux:button href="{{ route('bookings.index.user', ['page' => $returnPage, 'highlightId' => $booking->id]) }}" class="w-full" icon="chevron-left">
                        Back to Bookings
                    </flux:button>

                    @if(auth()->id() === $booking->booked_by && $status == 'pending')
                        <flux:button href="{{ route('bookings.edit.user', ['booking' => $booking->id, 'page' => $returnPage]) }}" class="w-full" icon="pencil-square">
                            Edit Booking
                        </flux:button>
                    @endif                    

                    @if($status === 'approved' && auth()->id() === $booking->booked_by)                       
                        <flux:button wire:click="changeStatus('done')" class="w-full" icon="check">                                                      
                            Mark as Done
                        </flux:button>                         
                    @endif

                    @if(in_array($status, ['pending', 'approved']) && auth()->id() === $booking->booked_by)                       
                        <flux:button wire:click="changeStatus('cancelled')" class="w-full" icon="x-mark">                                                      
                            Cancel Booking
                        </flux:button>                           
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Done Modal for Meeting Room and IT Assets --}}
    @if($asset_type === 'meeting_room' || $asset_type === 'it_asset')
        <flux:modal wire:model="showDoneModal" class="max-w-lg">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">Complete Booking</flux:heading>
                    <flux:subheading>Please provide remarks for completing this {{ $asset_type === 'meeting_room' ? 'meeting room' : 'IT asset' }} booking.</flux:subheading>
                </div>

                <flux:separator />

                <flux:field>
                    <flux:label>Remarks (Optional)</flux:label>
                    <flux:textarea
                        wire:model="doneRemarks"
                        rows="4"
                        placeholder="Enter your remarks about the booking completion (optional)..."
                    />
                    @error('doneRemarks')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:separator />

                <div class="flex gap-3 justify-end">
                    <flux:button variant="ghost" wire:click="closeDoneModal">
                        Cancel
                    </flux:button>
                    <flux:button 
                        variant="primary" 
                        wire:click="confirmMarkAsDone"
                    >
                        Complete Booking
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    {{-- Done Modal for Vehicle with Confetti --}}
    @if($asset_type === 'vehicle')
        <flux:modal wire:model="showDoneModal" class="max-w-lg">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">Complete Vehicle Booking</flux:heading>
                    <flux:subheading>Please provide the vehicle return details.</flux:subheading>
                </div>

                <flux:separator />

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Current Odometer Reading (km)</flux:label>
                        <flux:input 
                            wire:model="currentOdometer" 
                            type="number" 
                            placeholder="Enter current odometer reading"
                            min="0"
                        />
                        @error('currentOdometer')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    {{-- Fuel Level Slider --}}
                    <flux:field>
                        <flux:label>Fuel Level</flux:label>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span>Empty (1)</span>
                                <span>Current Level: {{ $fuelLevel }}/8</span>
                                <span>Full (8)</span>
                            </div>
                            <input 
                                type="range" 
                                wire:model.live="fuelLevel" 
                                min="1" 
                                max="8" 
                                step="1"
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 slider"
                                style="background: linear-gradient(to right, #ef4444 0%, #f97316 25%, #eab308 50%, #22c55e 75%, #16a34a 100%);"
                            />
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                @for($i = 1; $i <= 8; $i++)
                                    <span class="{{ $fuelLevel == $i ? 'font-bold text-blue-600 dark:text-blue-400' : '' }}">{{ $i }}</span>
                                @endfor
                            </div>
                        </div>
                        @error('fuelLevel')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>

                    {{-- Fuel Fill-ups Section --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            {{-- <flux:label>Fuel Fill-ups During Trip</flux:label> --}}
                            <flux:button
                                size="sm"
                                wire:click="addFuelEntry"
                                icon="plus"
                                variant="filled"
                            >
                                Add Fill-up
                            </flux:button>
                        </div>

                        @if(count($fuelEntries) === 0)
                            <div class="text-center py-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                                <p class="text-sm text-gray-500 dark:text-gray-400">No fuel fill-ups added</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Click "Add Fill-up" to record fuel purchases</p>
                            </div>
                        @else
                            @foreach($fuelEntries as $index => $entry)
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between mb-3">
                                        <flux:heading size="sm">Fill-up #{{ $index + 1 }}</flux:heading>
                                        <flux:button
                                            size="xs"
                                            wire:click="removeFuelEntry({{ $index }})"
                                            icon="x-mark"
                                            variant="ghost"
                                            class="text-red-600 hover:text-red-800"
                                        />
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <flux:field>
                                            <flux:label>Fuel Cost (RM)</flux:label>
                                            <flux:input
                                                wire:model="fuelEntries.{{ $index }}.fuel_cost"
                                                type="number"
                                                placeholder="0.00"
                                                min="0"
                                                step="0.01"
                                            />
                                            @error("fuelEntries.{$index}.fuel_cost")
                                                <flux:error>{{ $message }}</flux:error>
                                            @enderror
                                        </flux:field>

                                        <flux:field>
                                            <flux:label>Fuel Amount (Liters)</flux:label>
                                            <flux:input
                                                wire:model="fuelEntries.{{ $index }}.fuel_amount"
                                                type="number"
                                                placeholder="0.0"
                                                min="0"
                                                step="0.1"
                                            />
                                            @error("fuelEntries.{$index}.fuel_amount")
                                                <flux:error>{{ $message }}</flux:error>
                                            @enderror
                                        </flux:field>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Summary --}}
                            @if(count($fuelEntries) > 0)
                                @php
                                    $totalCost = collect($fuelEntries)->sum(function($entry) {
                                        return is_numeric($entry['fuel_cost']) ? floatval($entry['fuel_cost']) : 0;
                                    });
                                    $totalLiters = collect($fuelEntries)->sum(function($entry) {
                                        return is_numeric($entry['fuel_amount']) ? floatval($entry['fuel_amount']) : 0;
                                    });
                                @endphp
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-medium text-blue-900 dark:text-blue-200">Total:</span>
                                        <div class="text-right">
                                            <div class="font-semibold text-blue-900 dark:text-blue-200">
                                                RM {{ number_format($totalCost, 2) }}
                                            </div>
                                            <div class="text-xs text-blue-700 dark:text-blue-300">
                                                {{ number_format($totalLiters, 1) }} L
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Parking Location Section --}}
                    @if($this->isParkingRequired())
                        <flux:separator />
                        
                        <div class="space-y-4">
                            <flux:field>
                                <flux:label>Parking Level</flux:label>
                                <flux:select wire:model.live="parkingLevel" variant="listbox">
                                <flux:select.option value="" selected>Select parking level</flux:select.option>
                                    @foreach($this->getParkingLevels() as $level)
                                        <flux:select.option value="{{ $level }}">Level B{{ $level }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('parkingLevel')
                                    <flux:error>{{ $message }}</flux:error>
                                @enderror
                            </flux:field>

                            @if($parkingLevel == 1)
                                <flux:field>
                                    <flux:checkbox 
                                        wire:model="isReservedSlot" 
                                        label="Parked in reserved parking slot"
                                        description="Check this if you parked in a reserved parking slot on Level 1"
                                    />
                                </flux:field>
                            @endif
                        </div>
                    @endif
                </div>

                <flux:separator />

                <div class="flex gap-3 justify-end">
                    <flux:button variant="ghost" wire:click="closeDoneModal">
                        Cancel
                    </flux:button>
                    <flux:button 
                        variant="primary" 
                        wire:click="confirmMarkAsDone"
                        x-on:booking-completed.window="
                            console.log('Confetti event triggered!');
                            confetti({
                                particleCount: 150,
                                spread: 170,
                                origin: { y: 0.6 }
                            });
                            
                            confetti({
                                particleCount: 50,
                                angle: 60,
                                spread: 55,
                                origin: { x: 0 }
                            });
                            
                            confetti({
                                particleCount: 50,
                                angle: 120,
                                spread: 55,
                                origin: { x: 1 }
                            });
                            
                            setTimeout(function() {
                                confetti({
                                    particleCount: 100,
                                    spread: 90,
                                    origin: { y: 0.4 }
                                });
                            }, 300);
                        "
                    >
                        Complete Booking
                    </flux:button>
                </div>
            </div>
        </flux:modal>

        {{-- Custom CSS for fuel level slider --}}
        <style>
            .slider::-webkit-slider-thumb {
                appearance: none;
                height: 20px;
                width: 20px;
                border-radius: 50%;
                background: #3b82f6;
                cursor: pointer;
                border: 2px solid #ffffff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            .slider::-moz-range-thumb {
                height: 20px;
                width: 20px;
                border-radius: 50%;
                background: #3b82f6;
                cursor: pointer;
                border: 2px solid #ffffff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            .slider::-webkit-slider-track {
                height: 8px;
                border-radius: 4px;
            }

            .slider::-moz-range-track {
                height: 8px;
                border-radius: 4px;
            }
        </style>
    @endif

    {{-- Include Canvas Confetti Library for all booking types --}}
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

    {{-- Global confetti event listener for all booking completions --}}
    <div x-data="{}" x-on:booking-completed.window="
        console.log('Confetti event triggered for booking completion!');
        confetti({
            particleCount: 150,
            spread: 170,
            origin: { y: 0.6 }
        });
        
        confetti({
            particleCount: 50,
            angle: 60,
            spread: 55,
            origin: { x: 0 }
        });
        
        confetti({
            particleCount: 50,
            angle: 120,
            spread: 55,
            origin: { x: 1 }
        });
        
        setTimeout(function() {
            confetti({
                particleCount: 100,
                spread: 90,
                origin: { y: 0.4 }
            });
        }, 300);
    "></div>

</div>