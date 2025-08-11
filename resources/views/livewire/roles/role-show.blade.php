<div>
    <div class="relative mb-6 w-full">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('View Role') }}</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('This page is for viewing role details') }}</p>
        <flux:separator variant="subtle" class="mt-4" />        
    </div>     

    <div class="py-3">
        <flux:button variant="primary" href="{{ route('roles.index') }}">Back</flux:button>
    </div>

    <div class="w-150">
        <form class="mt-6 space-y-6">
            <div class="py-3">
                <div class="mb-4">
                    <flux:input wire:model="name" type="name" label="Name" />                   
                </div>
                <div class="mb-4">
                    <flux:checkbox.group wire:model="permissions" label="Permissions">
                        @foreach ($allPermissions as $permission)
                            <flux:checkbox label="{{ $permission->name}}" value="{{ $permission->name}}" disabled/>
                        @endforeach
                    </flux:checkbox.group>                    
                </div> 
            </div>
        </form>
    </div>

</div>
