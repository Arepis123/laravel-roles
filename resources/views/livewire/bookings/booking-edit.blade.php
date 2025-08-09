<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Edit Booking') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Modify your booking details') }}</flux:subheading>
        <flux:separator variant="subtle" />
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
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold text-gray-900 mb-2">Current Booking Details</h3>
            <div class="space-y-1 text-sm text-gray-700">
                <p><strong>Asset:</strong> 
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
                    {{ $assetConfig['label'] ?? 'Unknown' }} - {{ $asset->{$nameField} ?? 'Unknown' }}
                </p>
                <p><strong>Date:</strong> {{ $booking->start_time->format('l, F j, Y') }}</p>
                <p><strong>Time:</strong> {{ $booking->start_time->format('g:i A') }} - {{ $booking->end_time->format('g:i A') }}</p>
                <p><strong>Duration:</strong> {{ $booking->start_time->diffForHumans($booking->end_time, true) }}</p>
                <p><strong>Status:</strong> 
                    <span class="px-2 py-1 text-xs rounded-full
                        @if($booking->status === 'approved') bg-green-100 text-green-800
                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status === 'rejected') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                </p>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="text-green-600">{{ session('success') }}</div>
        @endif

        @if (session()->has('error'))
            <div class="text-red-600">{{ session('error') }}</div>
        @endif

        <form wire:submit.prevent="update" class="space-y-4">
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
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-blue-800 text-sm">
                            <strong>Note:</strong> Please select an asset type and asset before choosing time slots to see real-time availability.
                        </p>
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
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h3 class="font-semibold text-green-900 mb-2">✅ Available Updated Booking</h3>
                            <div class="space-y-1 text-sm text-green-800">
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
                        <flux:error name="refreshment_details" />
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

            <div class="py-4 my-0">
                <flux:separator/>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <flux:button type="submit" variant="primary" class="flex-1">
                    Update Booking
                </flux:button>
                
                <flux:button 
                    type="button" 
                    variant="outline" 
                    class="flex-1"
                    onclick="if(confirm('Are you sure you want to cancel this booking?')) { $wire.cancel(); }"
                >
                    Cancel Booking
                </flux:button>

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