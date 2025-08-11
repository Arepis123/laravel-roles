<div>
    <div class="relative mb-6 w-full">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Users') }}</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('Manage your all your users') }}</p>
        <flux:separator variant="subtle" class="mt-4" />        
    </div>    
   
    @session('success')
        <div>
            <flux:callout variant="success" icon="check-circle" heading="{{ $value }}" />
        </div>
    @endsession    

    @can('user.create')
    <div class="py-3">
        <flux:button variant="primary" href="{{ route('users.create') }}">Create user</flux:button>
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
                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Email</th>
                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Role</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                            <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $loop->iteration }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $user->name }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $user->email }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                @if ($user->roles)
                                    @foreach ($user->roles as $permission)
                                        <flux:badge class="mt-1">{{ $permission->name }}</flux:badge>
                                    @endforeach
                                @endif
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-center text-sm font-medium">
                                @can('user.view')
                                <flux:button size="sm" href="{{ route('users.show', $user->id) }}">View</flux:button>
                                @endcan
                                @can('user.edit')
                                <flux:button size="sm" href="{{ route('users.edit', $user->id) }}">Edit</flux:button>  
                                 @endcan
                                @can('user.delete')                           
                                <flux:modal.trigger name="delete-profile">
                                    <flux:button variant="danger" size="sm">Delete</flux:button>
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

    <flux:modal name="delete-profile" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete user?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this user.</p>
                    <p>This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" wire:click="delete({{ $user->id }})" variant="danger">Delete</flux:button>
            </div>
        </div>
    </flux:modal>    

</div>
