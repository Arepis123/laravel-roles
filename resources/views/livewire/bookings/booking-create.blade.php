
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
                <flux:select wire:model="asset_type" placeholder="Choose booking type...">
                    <flux:select.option value="meeting_room">Meeting Room</flux:select.option>
                    <flux:select.option value="vehicle">Vehicle</flux:select.option>
                    <flux:select.option value="it_asset">IT Asset</flux:select.option>
                </flux:select>            
            </flux:field>
{{-- <?php dd($asset_options); ?> --}}
            @if (!empty($asset_options))
                <div>
                    <label class="block font-medium">Asset</label>
                    <select wire:model="asset_id" class="w-full border rounded px-3 py-2">
                        <option value="">-- Select Asset --</option>
                        @foreach ($asset_options as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif          

            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input placeholder="" wire:model="asset_id"/>
                <flux:error name="asset_id" />
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
