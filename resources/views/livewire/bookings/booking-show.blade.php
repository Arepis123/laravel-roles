<div class="max-w-5xl mx-auto p-6 space-y-6">
    {{-- Header Section --}}
    <div class="flex items-center justify-between border-b pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Booking Details</h1>
            <p class="text-sm text-gray-600">Booking ID: #{{ $booking->id }}</p>
        </div>
        
        {{-- Status Badge --}}
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $this->statusColor }}">
                {{ ucfirst($status) }}
            </span>
            
            @if($this->statusChangesCount > 0)
                <button 
                    wire:click="toggleStatusHistory" 
                    class="text-sm text-blue-600 hover:text-blue-800 flex items-center space-x-1"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ $this->statusChangesCount }} change{{ $this->statusChangesCount > 1 ? 's' : '' }}</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                {{ session('info') }}
            </div>
        </div>
    @endif

    {{-- Status History Section --}}
    @if($showStatusHistory && $this->statusChangesCount > 0)
        <div class="bg-gray-50 border rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-medium text-gray-900">Status History</h3>
                <button wire:click="toggleStatusHistory" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-3">
                @foreach($this->statusHistory as $index => $change)
                    <div class="flex items-start space-x-3 {{ $index === 0 ? 'pb-3 border-b' : '' }}">
                        <div class="flex-shrink-0">
                            @if($index === 0)
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            @else
                                <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ match($change['status']) {
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                        'done' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    } }}">
                                    {{ ucfirst($change['status']) }}
                                </span>
                                @if($index === 0)
                                    <span class="text-xs text-blue-600 font-medium">Current</span>
                                @endif
                            </div>
                            
                            <p class="text-sm text-gray-600 mt-1 dark:text-gray-400">{{ $change['reason'] ?? 'Status changed' }}</p>
                            
                            <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                                <span>By: {{ $change['changed_by_name'] ?? 'Unknown' }}</span>
                                <span>{{ \Carbon\Carbon::parse($change['changed_at'])->diffForHumans() }}</span>
                                <span>{{ \Carbon\Carbon::parse($change['changed_at'])->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Booking Details Section --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Asset Information --}}
            <div class="border rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Asset Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Type</flux:label>
                        <flux:select wire:model.live="asset_type" placeholder="Select booking type" disabled>
                            @foreach ($this->assetTypeOptions as $option)
                                <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                            @endforeach
                        </flux:select>            
                    </flux:field>
                            
                    <flux:field>
                        <flux:label>Asset</flux:label>
                        <flux:select wire:model="asset_id" placeholder="Select asset" disabled>
                            @foreach ($this->assetOptions as $asset)
                                <flux:select.option value="{{ $asset->id }}">{{ $asset->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>
            </div>

            {{-- Time & Purpose --}}
            <div class="border rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Booking Details</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Start Time</flux:label>
                            <flux:input wire:model="start_time" type="datetime-local" disabled/>
                        </flux:field>
                        <flux:field>
                            <flux:label>End Time</flux:label>
                            <flux:input wire:model="end_time" type="datetime-local" disabled/>
                        </flux:field>
                    </div>

                    @if($capacity)
                        <flux:field>
                            <flux:label>Capacity</flux:label>
                            <flux:input wire:model="capacity" type="number" disabled/>
                        </flux:field>
                    @endif

                    <flux:field>
                        <flux:label>Purpose</flux:label>
                        <flux:textarea wire:model="purpose" rows="3" disabled/>
                    </flux:field>
                </div>
            </div>

            {{-- Additional Services --}}
            @if(!empty($additional_booking))
                <div class="border rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Additional Services</h2>
                    
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
                    </flux:checkbox.group>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status Management --}}
            @if($this->canChangeStatus)
                <div class="border rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status Management</h3>
                    
                    <div class="space-y-4">
                        <flux:dropdown>
                            <flux:button icon="chevron-down" size="sm" variant="outline" class="w-full justify-between">
                                Change Status
                            </flux:button>                                           
                            <flux:menu class="w-full">
                                <flux:menu.submenu heading="Change status">
                                    <flux:menu.radio.group position="bottom" align="center">
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
                                </flux:menu.submenu>
                            </flux:menu>
                        </flux:dropdown>

                        @if($this->statusChangesCount > 0)
                            <button 
                                wire:click="toggleStatusHistory"
                                class="w-full text-sm text-blue-600 hover:text-blue-800 py-2 px-3 rounded border border-blue-200 hover:bg-blue-50 transition-colors"
                            >
                                {{ $showStatusHistory ? 'Hide' : 'View' }} Status History ({{ $this->statusChangesCount }})
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Booking Information --}}
            <div class="border rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Booking Information</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Booked by:</span>
                        <span class="font-medium">{{ $booking->user->name }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Created:</span>
                        <span class="font-medium">{{ $booking->created_at->diffForHumans() }}</span>
                    </div>
                    
                    @if($booking->created_at != $booking->updated_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last updated:</span>
                            <span class="font-medium">{{ $booking->updated_at->diffForHumans() }}</span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Duration:</span>
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($start_time)->diffInHours(\Carbon\Carbon::parse($end_time)) }}h 
                            {{ \Carbon\Carbon::parse($start_time)->diffInMinutes(\Carbon\Carbon::parse($end_time)) % 60 }}m
                        </span>
                    </div>

                    @if($booking->isUpcoming())
                        <div class="flex justify-between">
                            <span class="text-gray-600">Starts in:</span>
                            <span class="font-medium text-green-600">{{ \Carbon\Carbon::parse($start_time)->diffForHumans() }}</span>
                        </div>
                    @elseif($booking->isActive())
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ends in:</span>
                            <span class="font-medium text-orange-600">{{ \Carbon\Carbon::parse($end_time)->diffForHumans() }}</span>
                        </div>
                    @elseif($booking->isPast())
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ended:</span>
                            <span class="font-medium text-gray-500">{{ \Carbon\Carbon::parse($end_time)->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="border rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('bookings.index') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        Back to Bookings
                    </a>
                    
                    @if(auth()->id() === $booking->booked_by)
                        <a href="{{ route('bookings.edit', $booking) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Booking
                        </a>
                    @endif

                    @if($status === 'approved' && auth()->user()->hasRole('admin'))
                        <button wire:click="changeStatus('done')" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark as Done
                        </button>
                    @endif

                    @if(in_array($status, ['pending', 'approved']) && (auth()->id() === $booking->booked_by || auth()->user()->hasRole('admin')))
                        <button wire:click="changeStatus('cancelled')" 
                                onclick="return confirm('Are you sure you want to cancel this booking?')"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel Booking
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>