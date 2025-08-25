<div>
    <div class="relative mb-6 w-full">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Booking') }}</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('Create new booking here') }}</p>
        <flux:separator variant="subtle" class="mt-4" />        
    </div>    

    @session('success')
        <div>
            <flux:callout variant="success" icon="check-circle" heading="{{ $value }}" />
        </div>
    @endsession 

    <div class="max-w-2xl mt-8 space-y-6">

        @if (session()->has('success'))
            <div class="text-green-600">{{ session('success') }}</div>
        @endif

        <form wire:submit.prevent="save" class="space-y-4">

        <flux:field>
            <flux:label>Type</flux:label>
            <flux:select wire:model.live="asset_type" placeholder="Select booking type">
                @foreach ($this->assetTypeOptions as $option)
                    <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                @endforeach
            </flux:select>            
        </flux:field>
                        
        <flux:field>
            <flux:label>{{ $this->assetFieldLabel }}</flux:label>
            <flux:select wire:model.live="asset_id" placeholder="Select {{ strtolower($this->assetFieldLabel) }}" :disabled="!$asset_type">
                @foreach ($this->assetOptions as $asset)
                    <flux:select.option value="{{ $asset->id }}">{{ $asset->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>    

            {{-- Only show capacity for Meeting Room and Vehicle --}}
            @if($this->shouldShowCapacity)
                <flux:field>
                    <flux:label>Capacity</flux:label>
                    <flux:input placeholder="{{ $this->capacityPlaceholder }}" wire:model.live="capacity" type="number"/>
                    <flux:error name="capacity" />
                    @if($asset_type === 'vehicle' && $capacity)
                        <flux:description>
                            @if($capacity == 1)
                                Only you will be using the vehicle
                            @else
                                You can select up to {{ $this->maxPassengers }} passenger(s)
                            @endif
                        </flux:description>
                    @endif
                </flux:field>
            @endif

            {{-- Destination field for Vehicles --}}
            @if($asset_type === 'vehicle')
                <flux:field>
                    <flux:label>Destination</flux:label>
                    <flux:input placeholder="Where are you going?" wire:model="destination" type="text"/>
                    <flux:error name="destination" />
                </flux:field>
            @endif

            {{-- Passengers Selection for Vehicles using Tailwind --}}
            @if($this->shouldShowPassengers)
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 my-2">
                            Select Passengers ({{ count($passengers) }}/{{ $this->maxPassengers }})
                        </label>
                        
                        {{-- Deselect All Button - Only show when passengers are selected --}}
                        @if(count($passengers) > 0)
                            <flux:button 
                                wire:click="deselectAllPassengers" 
                                variant="ghost" 
                                size="sm"
                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 py-0 my-0"
                            >
                                Deselect All
                            </flux:button>
                        @endif
                    </div>
                    
                    <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-3 max-h-48 overflow-y-auto bg-white dark:bg-gray-800">
                        @forelse ($availablePassengers as $user)
                            <div class="flex items-center py-2 px-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer"
                                wire:click="togglePassenger({{ $user->id }})">
                                <input type="checkbox" 
                                    value="{{ $user->id }}"
                                    @checked(in_array($user->id, $passengers))
                                    @if(!in_array($user->id, $passengers) && count($passengers) >= $this->maxPassengers) disabled @endif
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded pointer-events-none">
                                <label class="ml-3 text-sm text-gray-700 dark:text-gray-300 flex-1">
                                    {{ $user->name }}
                                    @if($user->email)
                                        <span class="text-gray-500 dark:text-gray-400 text-xs">({{ $user->email }})</span>
                                    @endif
                                </label>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No other users available</p>
                        @endforelse
                    </div>
                    
                    @if(count($passengers) > 0 && $availablePassengers)
                        <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-md">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                <strong>Selected passengers:</strong> 
                                {{ $availablePassengers->whereIn('id', $passengers)->pluck('name')->implode(', ') }}
                            </p>
                        </div>
                    @endif
                    
                    @error('passengers')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            @endif
                   
            <div class="py-4 my-0">
                <flux:separator/>
            </div>

            <div class="space-y-6">
                {{-- Date Selection --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Start Date --}}
                    <flux:field>
                        <flux:label>{{ $this->allowsMultiDayBooking ? 'Start Date' : 'Booking Date' }}</flux:label>
                        <flux:input 
                            placeholder="Select date" 
                            wire:model.live="booking_date" 
                            type="date"
                            min="{{ date('Y-m-d') }}"
                        />
                        <flux:error name="booking_date" />
                        @if($booking_date)
                            <flux:description>
                                {{ \Carbon\Carbon::parse($booking_date)->format('l, F j, Y') }}
                            </flux:description>
                        @endif
                    </flux:field>

                    {{-- End Date (for multi-day bookings) --}}
                    @if($this->allowsMultiDayBooking)
                        <flux:field>
                            <flux:label>End Date (Optional)</flux:label>
                            <flux:input 
                                placeholder="Same as start date if empty" 
                                wire:model.live="end_date" 
                                type="date"
                                min="{{ $booking_date ?: date('Y-m-d') }}"
                                :disabled="!$booking_date"
                            />
                            <flux:error name="end_date" />
                            @if($end_date)
                                <flux:description>
                                    {{ \Carbon\Carbon::parse($end_date)->format('l, F j, Y') }}
                                    ({{ $this->bookingDays }} {{ Str::plural('day', $this->bookingDays) }})
                                </flux:description>
                            @endif
                        </flux:field>
                    @endif
                </div>

                {{-- Multi-day booking notice --}}
                @if($this->allowsMultiDayBooking && $this->bookingDays > 1)
                    <flux:callout color="blue" icon="information-circle">
                        <flux:callout.heading>Multi-day Booking</flux:callout.heading>
                        <flux:callout.text>
                            You are booking for {{ $this->bookingDays }} days. 
                            The {{ strtolower($this->assetFieldLabel) }} will be reserved from 
                            {{ \Carbon\Carbon::parse($booking_date)->format('M j') }} to 
                            {{ \Carbon\Carbon::parse($end_date)->format('M j, Y') }}.
                        </flux:callout.text>
                    </flux:callout>
                @endif

                {{-- Asset and Date Selection Notice --}}
                @if(!$asset_type || !$asset_id)
                    <div x-data="{ visible: true }" x-show="visible" x-collapse>
                        <div x-show="visible" x-transition>
                            <flux:callout variant="warning" icon="information-circle">
                                <flux:callout.heading>Note</flux:callout.heading>
                                <flux:callout.text>
                                    Please select a booking type and {{ $asset_type ? strtolower($this->assetFieldLabel) : 'asset' }} 
                                    @if($asset_type === 'meeting_room')
                                        before choosing time slots to see real-time availability.
                                    @else
                                        to continue with your booking.
                                    @endif
                                </flux:callout.text>
                                <x-slot name="controls">
                                    <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                                </x-slot>
                            </flux:callout>   
                        </div>
                    </div>                                      
                @endif

                {{-- Time Selection --}}
                @if($booking_date && $asset_type && $asset_id)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Start Time --}}
                        <flux:field>
                            <flux:label>{{ $this->allowsMultiDayBooking ? 'Daily Start Time' : 'Start Time' }}</flux:label>
                            <flux:select wire:model.live="start_time" placeholder="Select start time">
                                @forelse($this->getAvailableTimeSlots() as $time => $label)
                                    <flux:select.option value="{{ $time }}">{{ $label }}</flux:select.option>
                                @empty
                                    <flux:select.option disabled>No available time slots</flux:select.option>
                                @endforelse
                            </flux:select>
                            <flux:error name="start_time" />
                            @if($asset_type === 'meeting_room' && empty($this->getAvailableTimeSlots()))
                                <flux:description class="text-red-600">
                                    No available time slots for this {{ strtolower($this->assetFieldLabel) }} on the selected date.
                                </flux:description>
                            @endif
                        </flux:field>

                        {{-- End Time --}}
                        <flux:field>
                            <flux:label>{{ $this->allowsMultiDayBooking ? 'Daily End Time' : 'End Time' }}</flux:label>
                            <flux:select wire:model.live="end_time" placeholder="Select end time" :disabled="!$start_time">
                                @if($start_time)
                                    @forelse($this->getAvailableEndTimes() as $time => $label)
                                        <flux:select.option value="{{ $time }}">{{ $label }}</flux:select.option>
                                    @empty
                                        <flux:select.option disabled>No available end times</flux:select.option>
                                    @endforelse
                                @else
                                    <flux:select.option disabled>Select start time first</flux:select.option>
                                @endif
                            </flux:select>
                            <flux:error name="end_time" />
                            @if($start_time && empty($this->getAvailableEndTimes()))
                                <flux:description class="text-red-600">
                                    No available end times from the selected start time.
                                </flux:description>
                            @endif
                        </flux:field>
                    </div>

                    {{-- Booking Summary --}}
                    @if($start_time && $end_time)
                        <flux:callout variant="success" icon="check-badge">
                            <flux:callout.heading>Booking Summary</flux:callout.heading>
                            @if($this->bookingDays > 1)
                                <flux:callout.text><strong>Period:</strong> {{ \Carbon\Carbon::parse($booking_date)->format('F j') }} - {{ \Carbon\Carbon::parse($end_date)->format('F j, Y') }}</flux:callout.text>
                                <flux:callout.text><strong>Duration:</strong> {{ $this->bookingDuration }}</flux:callout.text>
                                <flux:callout.text><strong>Daily Usage:</strong> {{ \Carbon\Carbon::parse($start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($end_time)->format('g:i A') }}</flux:callout.text>
                            @else
                                <flux:callout.text><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking_date)->format('l, F j, Y') }}</flux:callout.text>
                                <flux:callout.text><strong>Time:</strong> {{ \Carbon\Carbon::parse($start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($end_time)->format('g:i A') }}</flux:callout.text>
                                <flux:callout.text><strong>Duration:</strong> {{ $this->bookingDuration }}</flux:callout.text>
                            @endif
                        </flux:callout>                        
                    @endif
                @endif
            </div>
                   
            <div class="py-4 my-0">
                <flux:separator/>
            </div>

            <flux:textarea label="Purpose" wire:model="purpose" placeholder="Explain your booking purpose"/>

            {{-- Only show additional services section if there are available services --}}
            @if(count($this->availableServices) > 0)
                <div class="py-4 my-0">
                    <flux:separator/>
                </div>

                <flux:checkbox.group wire:model.live="additional_booking" label="Additional Services">
                    
                    {{-- Refreshment - Only for Meeting Room --}}
                    @if($this->isServiceAvailable('refreshment'))
                        <flux:callout color="sky" class="mb-3">
                            <flux:checkbox label="Refreshment" value="refreshment" description="Meals such as breakfast, lunch, or snacks can be arranged before or during the session." />                    
                            @if (in_array('refreshment', $additional_booking))
                                <div class="ml-6 mb-4">
                                    <flux:textarea wire:model.live="refreshment_details" placeholder="e.g., breakfast and coffee for 5 people. Pastries for 5 people"/>
                                    <flux:error name="refreshment_details" />
                                </div>
                            @endif                   
                        </flux:callout>
                    @endif
                    
                    {{-- Technical Support - Only for Meeting Room --}}
                    @if($this->isServiceAvailable('technical'))
                        <flux:callout color="sky" class="mb-3">
                            <flux:checkbox label="Technical Support" value="technical" description="IT will help in giving technical support." />                                     
                        </flux:callout>
                    @endif
                    
                    {{-- Email & Other Setup - Only for IT Asset --}}
                    @if($this->isServiceAvailable('email'))
                        <flux:callout color="sky" class="mb-3">
                            <flux:checkbox label="Email & Other Setup" value="email" description="IT technician will help setup email in the Outlook and other things requested by user." />                                     
                        </flux:callout>
                    @endif
                    
                </flux:checkbox.group>
            @endif
         
            <flux:button type="submit" variant="primary">
                Submit Booking
            </flux:button>
        </form>
    </div>

</div>