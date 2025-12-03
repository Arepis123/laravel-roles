<div>
    <div class="relative mb-6 w-full">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Edit My Booking') }}</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('Modify your booking details') }}</p>
        <flux:separator variant="subtle" class="mt-4" />        
    </div>

    @session('success')
        <div>
            <flux:callout variant="success" icon="check-circle" heading="{{ $value }}" />
        </div>
    @endsession 

    @session('error')
        <div>
            <flux:callout variant="error" icon="x-circle" heading="{{ $value }}" />
        </div>
    @endsession

    <div class="max-w-2xl mt-8 space-y-6">
        {{-- Current Booking Info --}}
        <flux:callout color="blue" icon="information-circle">
            <flux:callout.heading>Current Booking Details</flux:callout.heading>
            <flux:callout.text>
                @php
                    $assetConfig = null;
                    foreach ($this->assetTypeConfig as $key => $config) {
                        if ($config['model'] === $booking->asset_type) {
                            $assetConfig = $config;
                            break;
                        }
                    }
                    $assetModel = $assetConfig['model'] ?? '';
                    $nameField = $assetConfig['name_field'] ?? 'name';
                    $asset = $assetModel ? $assetModel::find($booking->asset_id) : null;
                @endphp
                <strong>Asset:</strong> {{ $assetConfig['label'] ?? 'Unknown' }} - {{ $asset->{$nameField} ?? 'Unknown' }}<br>
                <strong>Date:</strong> {{ $booking->start_time->format('l, F j, Y') }}
                @if($booking->end_time->format('Y-m-d') !== $booking->start_time->format('Y-m-d'))
                    to {{ $booking->end_time->format('l, F j, Y') }}
                @endif
                <br>
                <strong>Time:</strong> {{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}<br>
                <strong>Duration:</strong> {{ $booking->start_time->diffForHumans($booking->end_time, true) }}<br>
                <strong>Status:</strong> 
                <span class="px-2 py-1 text-xs rounded-full
                    @if($booking->status === 'approved') bg-green-100 text-green-800
                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($booking->status === 'rejected') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($booking->status) }}
                </span>
            </flux:callout.text>
        </flux:callout>

        @if (session()->has('success'))
            <div class="text-green-600">{{ session('success') }}</div>
        @endif

        @if (session()->has('error'))
            <div class="text-red-600">{{ session('error') }}</div>
        @endif

        <form wire:submit.prevent="update" class="space-y-4">

            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select variant="listbox" wire:model.live="asset_type" placeholder="Select booking type" searchable disabled>
                    @foreach ($this->assetTypeOptions as $option)
                        <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>{{ $this->assetFieldLabel }}</flux:label>
                <flux:select
                    variant="listbox"
                    wire:model.live="asset_id"
                    placeholder="Select {{ strtolower($this->assetFieldLabel) }}"
                    :disabled="!$asset_type"
                    searchable
                >
                    @foreach ($this->assetOptions as $asset)
                        <flux:select.option value="{{ $asset->id }}">{{ $asset->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>

            {{-- Last Parking Level Info for Vehicles --}}
            @if($asset_type === 'vehicle' && $asset_id && $this->lastParkingInfo)
                <flux:callout color="blue" icon="information-circle" class="mt-2">
                    <flux:callout.heading>Last Parking Location</flux:callout.heading>
                    <flux:callout.text>
                        This vehicle was last parked at
                        <strong>Level B{{ $this->lastParkingInfo['level'] }}</strong>
                        @if($this->lastParkingInfo['is_reserved'])
                            <span class="text-blue-600 dark:text-blue-400 font-medium">(Reserved Slot)</span>
                        @endif
                        on {{ $this->lastParkingInfo['date']->format('M d, Y') }}.
                    </flux:callout.text>
                </flux:callout>
            @endif    

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

            {{-- Passengers Selection for Vehicles --}}
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
                                    <div>
                                        <div class="font-medium">{{ $user->name }}</div>
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
                        <flux:date-picker with-today
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
                            <flux:date-picker with-today
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

                {{-- Time Selection - Only show if asset and date are selected --}}
                @if($booking_date && $asset_type && $asset_id)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Start Time --}}
                        <flux:field>
                            <flux:label>
                                @if($this->allowsMultiDayBooking && $this->bookingDays > 1)
                                    Pick-up Time
                                @elseif($this->allowsMultiDayBooking)
                                    Start Time
                                @else
                                    Start Time
                                @endif
                            </flux:label>
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
                        </flux:field>

                        {{-- End Time --}}
                        <flux:field>
                            <flux:label>
                                @if($this->allowsMultiDayBooking && $this->bookingDays > 1)
                                    Return Time
                                @elseif($this->allowsMultiDayBooking)
                                    End Time
                                @else
                                    End Time
                                @endif
                            </flux:label>
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
                            <flux:callout.heading>Updated Booking Summary</flux:callout.heading>
                            @if($this->bookingDays > 1)
                                <flux:callout.text><strong>Period:</strong> {{ \Carbon\Carbon::parse($booking_date)->format('F j') }} - {{ \Carbon\Carbon::parse($end_date)->format('F j, Y') }}</flux:callout.text>
                                <flux:callout.text><strong>Duration:</strong> {{ $this->bookingDuration }}</flux:callout.text>
                                <flux:callout.text><strong>Pick-up:</strong> {{ \Carbon\Carbon::parse($booking_date)->format('F j') }} at {{ \Carbon\Carbon::parse($start_time)->format('g:i A') }}</flux:callout.text>
                                <flux:callout.text><strong>Return:</strong> {{ \Carbon\Carbon::parse($end_date)->format('F j') }} at {{ \Carbon\Carbon::parse($end_time)->format('g:i A') }}</flux:callout.text>
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

            <div class="py-4 my-0">
                <flux:separator/>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <flux:button type="submit" variant="primary" class="flex-1">
                    Update Booking
                </flux:button>
                
                {{-- Cancel Button - Only show for pending bookings --}}
                @if($booking->status === 'pending')
                    <flux:button 
                        type="button" 
                        variant="outline" 
                        class="flex-1"
                        wire:click="cancel"
                        onclick="return confirm('Are you sure you want to cancel this booking?')"
                    >
                        Cancel Booking
                    </flux:button>
                @endif

                {{-- Mark as Done Button - Only show for approved bookings that have ended --}}
                @if($booking->status === 'approved' && $booking->end_time->isPast())
                    <flux:button 
                        type="button" 
                        variant="primary" 
                        class="flex-1"
                        wire:click="markDone"
                        onclick="return confirm('Mark this booking as completed?')"
                    >
                        Mark as Done
                    </flux:button>
                @endif

                <flux:button 
                    type="button" 
                    variant="subtle" 
                    class="flex-1"
                    onclick="window.history.back()"
                >
                    Go Back
                </flux:button>
            </div>
        </form>
    </div>
</div>