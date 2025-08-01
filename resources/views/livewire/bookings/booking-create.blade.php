
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
                <flux:input placeholder="How many people" wire:model="" type="number"/>
                <flux:error name="" />
            </flux:field>             
            
            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Start Time</flux:label>
                    <flux:input placeholder="Select time" wire:model="start_time" type="datetime-local"/>
                    <flux:error name="start_time" />
                </flux:field>
                <flux:field>
                    <flux:label>End Time</flux:label>
                    <flux:input placeholder="Select time" wire:model="end_time" type="datetime-local"/>
                    <flux:error name="end_time" />
                </flux:field>             
            </div>          

            <flux:textarea label="Purpose" wire:model="purpose" placeholder="Explain your booking purpose"/>

            <flux:checkbox.group wire:model="" label="Addtional booking">
                <flux:checkbox label="Refreshment" value="refreshment" description="Refreshment might be mineral water and traditional kuih"/>
                <flux:checkbox label="Smart Monitor" value="smart_monitor" description="IT will setup the smart monitor before the meeting start"/>
                <flux:checkbox label="Laptop" value="laptop" description="IT will setup laptop"/>
            </flux:checkbox.group>            

            <flux:button type="submit" variant="primary">
                Submit Booking
            </flux:button>
        </form>
    </div>

</div>
