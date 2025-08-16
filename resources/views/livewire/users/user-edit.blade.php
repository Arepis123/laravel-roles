<div>
    <div class="relative mb-6 w-full">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Edit User') }}</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('Update user information') }}</p>
        <flux:separator variant="subtle" class="mt-4" />        
    </div>

    <div class="py-3">
        <flux:button variant="primary" href="{{ route('users.index') }}">
            <flux:icon.arrow-left class="size-4" />
            Back
        </flux:button>
    </div>

    <div class="max-w-2xl">
        <form wire:submit="submit">
            <div class="space-y-4">
                <flux:input wire:model="name" type="text" label="Name" placeholder="Enter full name" required />
                
                <flux:input wire:model="email" type="email" label="Email" placeholder="user@example.com" required />
                
                @if(auth()->user()->hasRole(['Super Admin']))
                <flux:input wire:model="password" type="password" label="Password (leave blank to keep current)" placeholder="Minimum 8 characters" />
                
                <flux:input wire:model="confirm_password" type="password" label="Confirm Password" placeholder="Re-enter password" />
                @endif

                <div>
                    <flux:radio.group wire:model="status" label="Status">
                        <flux:radio label="Active" value="active" />
                        <flux:radio label="Inactive" value="inactive" />
                    </flux:radio.group>
                </div>
                
                <div>
                    <flux:checkbox.group wire:model="roles" label="Roles">
                        @foreach ($allRoles as $role)
                            @if ($role->name === 'Super Admin')
                                @if (auth()->user()->hasRole('Super Admin'))
                                    <flux:checkbox label="{{ $role->name }}" value="{{ $role->name }}" />
                                @endif
                            @else
                                <flux:checkbox label="{{ $role->name }}" value="{{ $role->name }}" />
                            @endif
                        @endforeach
                    </flux:checkbox.group>                     
                </div>                
                
                <div class="flex gap-2 pt-4">
                    <flux:button variant="primary" type="submit">Update User</flux:button>
                    <flux:button variant="ghost" href="{{ route('users.index') }}">Cancel</flux:button>
                </div>
            </div>
        </form>
    </div>
</div>