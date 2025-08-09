<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Booking') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage all booking here') }}</flux:subheading>
        <flux:separator variant="subtle" />
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
            <flux:label>Asset</flux:label>
            <flux:select wire:model.live="asset_id" placeholder="Select asset" :disabled="!$asset_type">
                @foreach ($this->assetOptions as $asset)
                    <flux:select.option value="{{ $asset->id }}">{{ $asset->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>    

            <flux:field>
                <flux:label>Capacity</flux:label>
                <flux:input placeholder="How many people" wire:model="capacity" type="number"/>
                <flux:error name="capacity" />
            </flux:field>                         
                   
            <div class="py-4 my-0">
                <flux:separator/>
            </div>

            <div class="space-y-6">
                {{-- Date Selection --}}
                <flux:field>
                    <flux:label>Booking Date</flux:label>
                    <flux:input 
                        placeholder="Select date" 
                        wire:model.live="booking_date" 
                        type="date"
                        min="{{ date('Y-m-d') }}"
                    />
                    <flux:error name="booking_date" />
                    @if($booking_date)
                        <flux:description>
                            Selected: {{ \Carbon\Carbon::parse($booking_date)->format('l, F j, Y') }}
                        </flux:description>
                    @endif
                </flux:field>

                {{-- Asset and Date Selection Notice --}}
                @if(!$asset_type || !$asset_id)
                    <div x-data="{ visible: true }" x-show="visible" x-collapse>
                        <div x-show="visible" x-transition>
                            <flux:callout variant="warning" icon="information-circle" inline x-data="{ visible: true }" x-show="visible">
                                <flux:callout.heading>Note</flux:callout.heading>
                                <flux:callout.text>Please select an asset type and asset before choosing time slots to see real-time availability.</flux:callout.text>
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
                            <flux:label>Start Time</flux:label>
                            <flux:select wire:model.live="start_time" placeholder="Select start time">
                                @forelse($this->getAvailableTimeSlots() as $time => $label)
                                    <flux:select.option value="{{ $time }}">{{ $label }}</flux:select.option>
                                @empty
                                    <flux:select.option disabled>No available time slots</flux:select.option>
                                @endforelse
                            </flux:select>
                            <flux:error name="start_time" />
                            @if(empty($this->getAvailableTimeSlots()))
                                <flux:description class="text-red-600">
                                    No available time slots for this asset on the selected date.
                                </flux:description>
                            @endif
                        </flux:field>

                        {{-- End Time --}}
                        <flux:field>
                            <flux:label>End Time</flux:label>
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
                            <flux:callout.heading>Available Booking</flux:callout.heading>
                            <flux:callout.text><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking_date)->format('l, F j, Y') }}</flux:callout.text>
                            <flux:callout.text><strong>Time:</strong> {{ \Carbon\Carbon::parse($start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($end_time)->format('g:i A') }}</flux:callout.text>
                            <flux:callout.text><strong>Duration:</strong> {{ $this->bookingDuration }}</flux:callout.text>
                        </flux:callout>                        
                    @endif
                @endif
            </div>
                   
            <div class="py-4 my-0">
                <flux:separator/>
            </div>

            <flux:textarea label="Purpose" wire:model="purpose" placeholder="Explain your booking purpose"/>

            <div class="py-4 my-0">
                <flux:separator/>
            </div>

            <flux:checkbox.group wire:model.live="additional_booking" label="Additional Services">

                <flux:callout color="sky" class="mb-3">
                    <flux:checkbox label="Refreshment" value="refreshment" description="Meals such as breakfast, lunch, or snacks can be arranged before or during the session." />                    
                    @if (in_array('refreshment', $additional_booking))
                        <div class="ml-6 mb-4">
                            <flux:textarea wire:model.live="refreshment_details" placeholder="e.g., breakfast and coffee for 5 people. Pastries for 5 people"/>
                            <flux:error name="refreshment_details" />
                        </div>
                    @endif                   
                </flux:callout>     
                
                <flux:callout color="sky" class="mb-3">
                    <flux:checkbox label="Technical Support" value="technical" description="IT will help in giving technical support inside the meeting room." />                                     
                </flux:callout>        
                
                <flux:callout color="sky" class="mb-3">
                    <flux:checkbox label="Laptop" value="laptop" description="A laptop will be prepared and set up for use during your session." />                                     
                </flux:callout>                 
                
                <!-- <flux:checkbox 
                    label="Smart Monitor" 
                    value="smart_monitor" 
                    description="A smart monitor will be set up in the room before the meeting starts." 
                /> -->
            </flux:checkbox.group>
         
            <flux:button type="submit" variant="primary">
                Submit Booking
            </flux:button>
        </form>
    </div>

</div>