<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Roles') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage your all your roles') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>
   
    @session('success')
        <div>
            <flux:callout variant="success" icon="check-circle" heading="{{ $value }}" />
        </div>
    @endsession    

    @can('role.create')
    <div class="py-3">
        <flux:button variant="primary" href="{{ route('roles.create') }}">Create Role</flux:button>
    </div>
    @endcan

    <div class="border border-gray-200 rounded-xl shadow-2xs p-4 dark:bg-neutral-800 dark:border-neutral-700">
        <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead>
                    <tr>
                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">No</th>
                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Name</th>
                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Permission</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @foreach ($roles as $role)
                        <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                            <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $loop->iteration }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $role->name }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                @if ($role->permissions)
                                    @foreach ($role->permissions as $permission)
                                        <flux:badge class="mt-1">{{ $permission->name }}</flux:badge>
                                    @endforeach
                                @endif
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-center text-sm font-medium">
                                @can('role.view')
                                <flux:button size="sm" href="{{ route('roles.show', $role->id) }}">View</flux:button>
                                @endcan
                                @can('role.edit')
                                <flux:button size="sm" href="{{ route('roles.edit', $role->id) }}">Edit</flux:button>   
                                @endcan 
                                @can('role.delete')                                
                                <flux:modal.trigger name="delete-role">
                                    <flux:button variant="danger" size="sm" wire:click="confirmDelete({{ $role->id }})">Delete</flux:button>
                                </flux:modal.trigger>  
                                @endcan                      
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            </div>
        </div>
        </div>
    </div>
   
    <flux:modal name="delete-role" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete role?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this role.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" wire:click="delete" variant="danger">Delete</flux:button>
            </div>
        </div>
    </flux:modal>    

</div>

