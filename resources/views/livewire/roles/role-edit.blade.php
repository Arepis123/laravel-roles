<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Role User') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Form to edit role') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="py-3">
        <flux:button variant="primary" href="{{ route('roles.index') }}">Back</flux:button>
    </div>

    <div class="w-150">
        <form wire:submit="submit" class="mt-6 space-y-6">
            <div class="py-3">
                <div class="mb-4">
                    <flux:input wire:model="name" type="name" label="Name" />                   
                </div>
                <div class="mb-4">
                    <flux:checkbox.group wire:model="permissions" label="Permissions">
                        @foreach ($allPermissions as $permission)
                            <flux:checkbox label="{{ $permission->name}}" value="{{ $permission->name}}" />
                        @endforeach
                    </flux:checkbox.group>                    
                </div>                
                <flux:button variant="primary" type="submit">Submit</flux:button>
            </div>
        </form>
    </div>


</div>
