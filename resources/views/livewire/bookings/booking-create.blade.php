
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
        {{-- <flux:heading size="xl"><b>Booking Info</b></flux:heading>

        <flux:separator variant="subtle" class="my-2" /> --}}

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
            <flux:select wire:model="asset_id" placeholder="Select asset" :disabled="!$asset_type">
                @foreach ($this->assetOptions as $asset)
                    <flux:select.option value="{{ $asset->id }}">{{ $asset->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>    

            <flux:field>
                <flux:label>Capacity</flux:label>
                <flux:input placeholder="How many people" wire:model="capacity" type="number"/>
                <flux:error name="" />
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

                {{-- Time Selection - Only show if date is selected --}}
                @if($booking_date)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Start Time --}}
                        <flux:field>
                            <flux:label>Start Time</flux:label>
                            <flux:select wire:model.live="start_time" placeholder="Select start time">
                                @foreach($this->getAvailableTimeSlots() as $time => $label)
                                    <flux:select.option value="{{ $time }}">{{ $label }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="start_time" />
                        </flux:field>

                        {{-- End Time --}}
                        <flux:field>
                            <flux:label>End Time</flux:label>
                            <flux:select wire:model.live="end_time" placeholder="Select end time" :disabled="!$start_time">
                                @if($start_time)
                                    @foreach($this->getAvailableEndTimes() as $time => $label)
                                        <flux:select.option value="{{ $time }}">{{ $label }}</flux:select.option>
                                    @endforeach
                                @else
                                    <flux:select.option disabled>Select start time first</flux:select.option>
                                @endif
                            </flux:select>
                            <flux:error name="end_time" />
                        </flux:field>
                    </div>

                    {{-- Alternative: Simple Time Inputs (if you prefer input fields over dropdowns) --}}
                    {{--
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Start Time</flux:label>
                            <flux:input 
                                placeholder="09:00" 
                                wire:model.live="start_time" 
                                type="time"
                                step="900"
                            />
                            <flux:error name="start_time" />
                            <flux:description>15-minute intervals</flux:description>
                        </flux:field>
                        <flux:field>
                            <flux:label>End Time</flux:label>
                            <flux:input 
                                placeholder="17:00" 
                                wire:model.live="end_time" 
                                type="time"
                                step="900"
                            />
                            <flux:error name="end_time" />
                        </flux:field>             
                    </div>
                    --}}

                    {{-- Booking Summary --}}
                    @if($start_time && $end_time)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="font-semibold text-blue-900 mb-2">Booking Summary</h3>
                            <div class="space-y-1 text-sm text-blue-800">
                                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking_date)->format('l, F j, Y') }}</p>
                                <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($end_time)->format('g:i A') }}</p>
                                <p><strong>Duration:</strong> {{ $this->bookingDuration }}</p>
                            </div>
                        </div>
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
                <flux:checkbox 
                    label="Refreshment" 
                    value="refreshment" 
                    description="Meals such as breakfast, lunch, or snacks can be arranged before or during the session." 
                />

                @if (in_array('refreshment', $additional_booking))
                    <div class="ml-6 mb-4">
                        <flux:textarea wire:model.live="refreshment_details" placeholder="e.g., breakfast and coffee for 5 people. Pastries for 5 people"/>
                    </div>
                @endif

                <flux:checkbox 
                    label="Smart Monitor" 
                    value="smart_monitor" 
                    description="A smart monitor will be set up in the room before the meeting starts." 
                />

                <flux:checkbox 
                    label="Laptop" 
                    value="laptop" 
                    description="A laptop will be prepared and set up for use during your session." 
                />
            </flux:checkbox.group>
         
            <flux:button type="submit" variant="primary">
                Submit Booking
            </flux:button>
        </form>
    </div>

</div>
