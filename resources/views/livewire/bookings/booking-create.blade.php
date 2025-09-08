<div>
    <div class="relative mb-6 w-full">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Booking') }}</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('Create new booking here') }}</p>
        <flux:separator variant="subtle" class="mt-4" />        
    </div>    

    {{-- Success Toast will be handled by Livewire component --}}
    @if (session()->has('success'))
        <flux:toast variant="success" :closable="true" :duration="5000">
            <flux:icon name="check-circle" class="w-5 h-5" />
            {{ session('success') }}
        </flux:toast>
    @endif
    
    @if (session()->has('error'))
        <flux:toast variant="danger" :closable="true" :duration="7000">
            <flux:icon name="exclamation-triangle" class="w-5 h-5" />
            {{ session('error') }}
        </flux:toast>
    @endif 

    <div class="max-w-2xl mt-8 space-y-6">

        {{-- Session success is now handled by toast notifications above --}}

        <form wire:submit.prevent="save" class="space-y-4">

        <flux:field>
            <flux:label>Type</flux:label>
            <flux:select variant="listbox" wire:model.live="asset_type" placeholder="Select booking type" searchable>
                @foreach ($this->assetTypeOptions as $option)
                    <flux:select.option value="{{ $option['value'] }}" description="{{ $option['description'] ?? '' }}">{{ $option['label'] }}</flux:select.option>
                @endforeach
            </flux:select>            
        </flux:field>
                        
        <flux:field>
            <flux:label>{{ $this->assetFieldLabel }}</flux:label>
            <flux:select variant="listbox" wire:model.live="asset_id" placeholder="Select {{ strtolower($this->assetFieldLabel) }}" :disabled="!$asset_type" searchable>
                @foreach ($this->assetOptions as $asset)
                    <flux:select.option value="{{ $asset->id }}" description="{{ $asset->description ?? '' }}">
                        {{ $asset->name }}
                    </flux:select.option>
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
                    
                    <flux:select 
                        variant="listbox"
                        wire:model.live="passengers"
                        placeholder="Search and select passengers..."
                        searchable
                        multiple
                        class="max-h-48"
                    >
                        @foreach ($availablePassengers as $user)
                            <flux:select.option value="{{ $user->id }}">
                                <div class="flex items-center space-x-3">
                                    {{-- <flux:avatar 
                                        :initials="$user->initials()" 
                                        size="sm"
                                        class="shrink-0"
                                    /> --}}
                                    <div>
                                        <div class="font-medium">{{ $user->name }}</div>
                                        {{-- @if($user->email)
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        @endif --}}
                                        {{-- @if($user->position)
                                            <flux:badge size="xs" color="gray">{{ $user->position }}</flux:badge>
                                        @endif --}}
                                    </div>
                                </div>
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    
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
                        <flux:date-picker 
                            wire:model.live="booking_date" 
                            placeholder="Select date"
                            :min-date="date('Y-m-d')"
                            show-today-button
                            show-clear-button
                            format="Y-m-d"
                        />
                        <flux:error name="booking_date" />
                        @if($booking_date)
                            <flux:description>
                                <flux:icon name="calendar" class="w-4 h-4 inline mr-1" />
                                {{ \Carbon\Carbon::parse($booking_date)->format('l, F j, Y') }}
                            </flux:description>
                        @endif
                    </flux:field>

                    {{-- End Date (for multi-day bookings) --}}
                    @if($this->allowsMultiDayBooking)
                        <flux:field>
                            <flux:label>End Date (Optional)</flux:label>
                            <flux:date-picker 
                                wire:model.live="end_date" 
                                placeholder="Same as start date if empty"
                                :min-date="$booking_date ?: date('Y-m-d')"
                                :disabled="!$booking_date"
                                show-today-button
                                show-clear-button
                                format="Y-m-d"
                            />
                            <flux:error name="end_date" />
                            @if($end_date)
                                <flux:description>
                                    <flux:icon name="calendar-days" class="w-4 h-4 inline mr-1" />
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
                            <flux:select variant="listbox" wire:model.live="start_time" placeholder="Select start time" searchable>
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
                            @if($vehicleUnderMaintenance)
                                <flux:description class="text-amber-600 flex items-center gap-2">
                                    <span>🔧</span>
                                    This vehicle is currently under maintenance and cannot be booked until maintenance is completed.
                                </flux:description>
                            @endif
                        </flux:field>

                        {{-- End Time --}}
                        <flux:field>
                            <flux:label>{{ $this->allowsMultiDayBooking ? 'Daily End Time' : 'End Time' }}</flux:label>
                            <flux:select variant="listbox" wire:model.live="end_time" placeholder="Select end time" :disabled="!$start_time" searchable>
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

                <div class="space-y-4">
                    <flux:subheading>Additional Services</flux:subheading>
                    
                    <div class="space-y-3">
                        {{-- Refreshment - Only for Meeting Room --}}
                        @if($this->isServiceAvailable('refreshment'))                                                           
                            <flux:checkbox.group wire:model.live="additional_booking" variant="cards" class="">                                    
                                <flux:checkbox
                                value="refreshment" 
                                icon="cake"
                                label="Refreshment Service"
                                description="Meals such as breakfast, lunch, or snacks can be arranged before or during the session."
                                />
                            </flux:checkbox.group>                                                            
                            @if (is_array($additional_booking) && in_array('refreshment', $additional_booking))
                                <div class="ml-6 mt-3">
                                    <flux:textarea 
                                        wire:model.live="refreshment_details" 
                                        label="Refreshment Details"
                                        placeholder="e.g., breakfast and coffee for 5 people. Pastries for 5 people"
                                        class="mt-2"
                                    />
                                    <flux:error name="refreshment_details" />
                                </div>
                            @endif
                        @endif
                        
                        {{-- Technical Support - Only for Meeting Room --}}
                        @if($this->isServiceAvailable('technical'))
                            <flux:checkbox.group wire:model.live="additional_booking" variant="cards" class="">                                    
                                <flux:checkbox
                                    value="technical" 
                                    icon="wrench-screwdriver"
                                    label="Technical Support"
                                    description="IT support for setting up presentations, connecting devices, and troubleshooting technical issues."
                                />
                            </flux:checkbox.group>                              
                        @endif
                        
                        {{-- Email & Other Setup - Only for IT Asset --}}
                        @if($this->isServiceAvailable('email'))
                            <flux:checkbox.group wire:model.live="additional_booking" variant="cards" class="">                                    
                                <flux:checkbox 
                                    wire:model.live="additional_booking"
                                    value="email" 
                                    icon="envelope"
                                    label="Email & System Setup"
                                    description="IT technician will help setup email in Outlook, install necessary software, and configure system settings."
                                />
                            </flux:checkbox.group>                                                              
                        @endif                        
                    </div>
                </div>
            @endif
         
            <div class="flex items-center pt-6">
                <flux:button variant="filled" wire:click="resetForm" type="button" class="me-3">
                    <flux:icon name="arrow-path" class="w-4 h-4 mr-2 inline" />
                    Reset Form
                </flux:button>
                
                <flux:modal.trigger name="booking-confirmation">
                    <flux:button 
                        variant="primary"
                    >
                        Confirm Booking
                    </flux:button>
                </flux:modal.trigger>
            </div>
        </form>
    </div>

    {{-- Confirmation Modal --}}
    <flux:modal name="booking-confirmation" class="max-w-2xl">
        <div class="space-y-6">
            {{-- Header --}}
            <div class="text-center pb-4">
                <div class="mx-auto flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/20 mb-4">
                    <flux:icon name="calendar-days" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:heading size="xl" class="text-gray-900 dark:text-white">Confirm Your Booking</flux:heading>
                <flux:subheading class="text-gray-500 dark:text-gray-400 mt-0">Please review your booking details before submitting</flux:subheading>
            </div>

            {{-- Booking Details Card --}}
            <div class="card border rounded-lg p-3">
                <div class="space-y-4">
                    {{-- Basic Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center space-x-3">
                            <div>
                                <flux:heading>Type</flux:heading>                                
                                <flux:text class="mt-2">{{ collect($this->assetTypeOptions)->firstWhere('value', $asset_type)['label'] ?? $asset_type }}</flux:text>
                            </div>
                        </div>
                        
                        @if($asset_id && !empty($this->assetOptions))
                        <div class="flex items-center space-x-3">
                            <div>
                                <flux:heading>{{ $this->assetFieldLabel }}</flux:heading>
                                <flux:text class="mt-2">{{ collect($this->assetOptions)->firstWhere('id', $asset_id)?->name }}</flux:text>
                            </div>
                        </div>
                        @endif
                        
                        @if($booking_date)
                        <div class="flex items-center space-x-3">
                            <div>
                                <flux:heading>Date</flux:heading>
                                <flux:text class="mt-2">{{ \Carbon\Carbon::parse($booking_date)->format('F j, Y') }}</flux:text>
                            </div>
                        </div>
                        @endif
                        
                        @if($start_time && $end_time)
                        <div class="flex items-center space-x-3">
                            <div>
                                <flux:heading>Time</flux:heading>
                                <flux:text class="mt-2">{{ \Carbon\Carbon::parse($start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($end_time)->format('g:i A') }}</flux:text>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Additional Details --}}
                    @if($capacity || $destination)
                    <flux:separator />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($capacity)
                        <div class="flex items-center space-x-3">
                            <div>
                                <flux:heading>Capacity</flux:heading>
                                <flux:text class="mt-2">{{ $capacity }} {{ $asset_type === 'vehicle' ? 'passenger(s)' : 'people' }}</flux:text>                                                                    
                            </div>
                        </div>
                        @endif
                        
                        @if($destination)
                        <div class="flex items-center space-x-3">
                            <div>
                                <flux:heading>Destination</flux:heading>
                                <flux:text class="mt-2">{{ $destination }}</flux:text>              
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Passengers --}}
                    @if(count($passengers) > 0)                    
                    <flux:separator />
                    <div class="flex-1">
                        <flux:heading>Passengers ({{ count($passengers) }})</flux:heading>
                        @foreach($availablePassengers->whereIn('id', $passengers) as $passenger)
                            <flux:badge variant="outline" color="blue" class="mt-2">{{ $passenger->name }}</flux:badge>
                        @endforeach                    
                    </div>
                    @endif

                    {{-- Purpose --}}
                    @if($purpose)
                        <flux:separator />
                        <div class="flex items-start space-x-3">
                            <div class="flex-1">
                                <flux:heading>Purpose</flux:heading>
                                <flux:text class="mt-2">{{ $purpose }}</flux:text>                                      
                            </div>
                        </div>
                    @endif

                    {{-- Additional Services --}}
                    @if(count($additional_booking) > 0)
                    <flux:separator />
                    <div class="flex items-start space-x-3">
                        <div class="flex-1">
                            <flux:heading class="mb-2">Additional Services</flux:heading>
                            <div class="space-y-2">
                                @if(is_array($additional_booking) && in_array('refreshment', $additional_booking))
                                    <div>
                                        <flux:text>
                                            <flux:icon.arrow-turn-down-right class="w-4 h-4 inline"/>
                                            Refreshment Service
                                        </flux:text>
                                        {{-- Debug: Check refreshment_details value --}}
                                        @if($refreshment_details)
                                            {{-- <div class="mt-1 pl-4 border-l-2 border-blue-200 text-gray-600 dark:text-gray-400">
                                                <strong>Details:</strong> {{ $refreshment_details }}
                                            </div> --}}
                                            <flux:text color="blue" class="ms-5">{{ $refreshment_details }}</flux:text>
                                        @else
                                            <flux:text color="red">No details provided</flux:text>
                                            <div class="mt-1 text-xs text-red-500">No details provided</div>
                                        @endif
                                    </div>
                                @endif
                                @if(is_array($additional_booking) && in_array('technical', $additional_booking))
                                    <flux:text>
                                        <flux:icon.arrow-turn-down-right class="w-4 h-4 inline"/>
                                        Technical Support
                                    </flux:text>                                    
                                @endif
                                @if(is_array($additional_booking) && in_array('email', $additional_booking))
                                    <flux:text>
                                        <flux:icon.arrow-turn-down-right class="w-4 h-4 inline"/>
                                        Email & System Setup
                                    </flux:text>                                       
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>         

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                
                <flux:spacer class="hidden sm:block" />
                
                <flux:button 
                    variant="primary" 
                    wire:click="save" 
                    wire:loading.attr="disabled"
                    class="order-1 sm:order-2"
                >
                    <span wire:loading.remove>Submit Booking</span>
                    <span wire:loading>Submitting...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>