<div>
    <div class="relative mb-6 w-full">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Create Role') }}</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('Create new user here') }}</p>
        <flux:separator variant="subtle" class="mt-4" />        
    </div>

    <div class="py-3">
        <flux:button variant="primary" href="{{ route('users.index') }}">Back</flux:button>
    </div>

    <div class="w-150">
        <form  wire:submit="submit" class="">
            <div class="py-3">
                <div class="mb-4">
                    <flux:input wire:model="name" type="name" label="Name" />
                </div>
                <div class="mb-4">
                    <flux:input wire:model="email" type="email" label="Email" />
                </div>
                <div class="mb-4">
                    <flux:input wire:model="password" type="password" label="Password" />
                </div>
                <div class="mb-4">
                    <flux:input wire:model="confirm_password" type="password" label="Confirm Password" />
                </div>
                <div class="mb-4">
                    <flux:checkbox.group wire:model="roles" label="Roles">
                        @foreach ($allRoles as $role)
                            <flux:checkbox label="{{ $role->name }}" value="{{ $role->name }}" />
                        @endforeach
                    </flux:checkbox.group>                     
                </div> 
                <flux:button variant="primary" type="submit">Submit</flux:button>
            </div>
        </form>
    </div>


</div>
