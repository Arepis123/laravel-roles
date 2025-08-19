<div class="max-w-6xl mx-auto space-y-6">
    {{-- Header Section --}}
    <div class="flex items-center justify-between border-b pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Booking Details</h1>
            <p class="text-sm text-gray-600 dark:text-white">Booking ID: #{{ $booking->id }}</p>
        </div>
        
        {{-- Status Badge --}}
        <div class="flex items-center space-x-0">
            <!-- <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $this->statusColor }}">
                {{ ucfirst($status) }}
            </span> -->
            <div class="flex flex-col gap-1">
                @if ($status == 'pending')
                    <flux:badge color="yellow" class="w-fit">
                        <flux:icon name="clock" class="w-4 h-4 mr-1" />
                        {{ ucfirst($status) }}
                    </flux:badge>
                @elseif ($status == 'approved')     
                    <flux:badge color="sky" class="w-fit">
                        <flux:icon name="check" class="w-4 h-4 mr-1" />
                        {{ ucfirst($status) }}
                    </flux:badge>  
                @elseif ($status == 'rejected')     
                    <flux:badge color="red" class="w-fit">
                        <flux:icon name="x-mark" class="w-4 h-4 mr-1" />
                        {{ ucfirst($status) }}
                    </flux:badge>                                                                                                
                @elseif ($status == 'cancelled')
                    <flux:badge color="zinc" class="w-fit">
                        <flux:icon name="arrow-turn-down-left" class="w-4 h-4 mr-1" />
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

            {{-- Time & Purpose --}}
            {{-- <div class="border rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Booking Details</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:heading>Start Time</flux:heading>
                            <flux:input wire:model="start_time" type="datetime-local" disabled/>
                        </flux:field>
                        <flux:field>
                            <flux:heading>End Time</flux:heading>
                            <flux:input wire:model="end_time" type="datetime-local" disabled/>
                        </flux:field>
                    </div>

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
            </div> --}}

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
                                    @if(auth()->user()->hasRole(['Admin', 'Super Admin']))
                                    <flux:menu.radio 
                                        :checked="$status == 'pending'" 
                                        wire:click="changeStatus('pending')"
                                    >
                                        Pending
                                    </flux:menu.radio>
                                    <flux:menu.radio 
                                        :checked="$status == 'approved'" 
                                        wire:click="changeStatus('approved')"
                                    >
                                        Approved
                                    </flux:menu.radio>
                                    <flux:menu.radio 
                                        :checked="$status == 'rejected'" 
                                        wire:click="changeStatus('rejected')"
                                    >
                                        Rejected
                                    </flux:menu.radio>
                                    @endif
                                    <flux:menu.radio 
                                        :checked="$status == 'cancelled'" 
                                        wire:click="changeStatus('cancelled')"
                                    >
                                        Cancelled
                                    </flux:menu.radio>                                    
                                    <flux:menu.radio 
                                        :checked="$status == 'done'" 
                                        wire:click="changeStatus('done')"
                                    >
                                        Done
                                    </flux:menu.radio>
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
                    <flux:button href="{{ route('bookings.index') }}" class="w-full" icon="chevron-left">                                                      
                        Back to Bookings
                    </flux:button>                    
                    
                    @if(auth()->user()->hasRole(['Super Admin']))                       
                        <flux:button href="{{ route('bookings.edit', $booking) }}" class="w-full" icon="pencil-square">                                                      
                            Edit Booking
                        </flux:button>                          
                    @endif

                    @if(auth()->id() === $booking->booked_by && $status !== 'cancelled' && $status !== 'rejected')                       
                        <flux:button href="{{ route('bookings.edit.user', $booking) }}" class="w-full" icon="pencil-square">                                                      
                            Edit Booking
                        </flux:button>                          
                    @endif                    

                    @if($status === 'approved' && auth()->user()->hasRole(['Admin', 'Super Admin']))
                        <!-- <button wire:click="changeStatus('done')" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark as Done
                        </button> -->
                        <flux:button wire:click="changeStatus('done')" class="w-full" icon="check">                                                      
                            Mark as Done
                        </flux:button>                         
                    @endif

                    @if(in_array($status, ['pending', 'approved']) && (auth()->id() === $booking->booked_by || auth()->user()->hasRole(['Admin', 'Super Admin'])))
                        <!-- <button wire:click="changeStatus('cancelled')" 
                                onclick="return confirm('Are you sure you want to cancel this booking?')"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel Booking
                        </button> -->
                        <flux:button wire:click="changeStatus('cancelled')" class="w-full" icon="x-mark">                                                      
                            Cancel Booking
                        </flux:button>                           
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>