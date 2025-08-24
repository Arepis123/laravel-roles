<div>
    <!-- Page Header -->
    <div class="relative mb-6 w-full">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Roles') }}</h1>
                <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('Manage system roles and permissions') }}</p>
            </div>
            
            <!-- Stats -->
            <div class="flex gap-4 text-sm">
                <div class="text-center">
                    <div class="font-semibold text-blue-600">{{ $roles->count() }}</div>
                    <div class="text-gray-500">Total Roles</div>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-purple-600">{{ $roles->sum(fn($role) => $role->permissions->count()) }}</div>
                    <div class="text-gray-500">Permissions</div>
                </div>
            </div>
        </div>
        <flux:separator variant="subtle" class="mt-4" />        
    </div>    
   
    <!-- Success Message -->
    @session('success')
        <div class="mb-6">
            <flux:callout variant="success" icon="check-circle" heading="{{ $value }}" />
        </div>
    @endsession    

    <!-- Create Button -->
    @can('role.create')
    <div class="flex justify-between items-center mb-6">
        <div></div> <!-- Spacer for alignment -->
        <flux:button variant="primary" href="{{ route('roles.create') }}" icon="plus" class="w-full sm:w-auto">
            Create New Role
        </flux:button>
    </div>
    @endcan

    <!-- Desktop Table View (hidden on mobile) -->
    <div class="hidden md:block bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-900">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            <div class="flex items-center gap-1">
                                <flux:icon name="hashtag" class="w-3 h-3" />
                                No
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            <div class="flex items-center gap-1">
                                <flux:icon name="shield-check" class="w-3 h-3" />
                                Role Name
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            <div class="flex items-center gap-1">
                                <flux:icon name="key" class="w-3 h-3" />
                                Permissions
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                    @forelse ($roles as $role)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($role->name === 'Super Admin')
                                        <flux:icon name="shield-check" class="w-5 h-5 text-red-500" />
                                    @elseif($role->name === 'Admin')
                                        <flux:icon name="shield-check" class="w-5 h-5 text-amber-500" />
                                    @else
                                        <flux:icon name="shield-check" class="w-5 h-5 text-blue-500" />
                                    @endif
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $role->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1 max-w-md">
                                    @forelse ($role->permissions->take(5) as $permission)
                                        <flux:badge size="sm" variant="outline">{{ $permission->name }}</flux:badge>
                                    @empty
                                        <span class="text-sm text-gray-500">No permissions</span>
                                    @endforelse
                                    @if($role->permissions->count() > 5)
                                        <flux:badge size="sm" color="gray">+{{ $role->permissions->count() - 5 }} more</flux:badge>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center gap-1">
                                    @can('role.view')
                                    <flux:button size="sm" href="{{ route('roles.show', $role->id) }}" variant="ghost" title="View">
                                        <flux:icon name="eye" class="w-4 h-4" />
                                    </flux:button>
                                    @endcan
                                    @can('role.edit')
                                    <flux:button size="sm" href="{{ route('roles.edit', $role->id) }}" variant="ghost" title="Edit">
                                        <flux:icon name="pencil" class="w-4 h-4" />
                                    </flux:button>   
                                    @endcan 
                                    @can('role.delete')                                
                                    <flux:modal.trigger name="delete-role">
                                        <flux:button variant="ghost" size="sm" wire:click="confirmDelete({{ $role->id }})" title="Delete" class="text-red-600 hover:text-red-800">
                                            <flux:icon name="trash" class="w-4 h-4" />
                                        </flux:button>
                                    </flux:modal.trigger>  
                                    @endcan                      
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="shield-check" class="w-8 h-8 text-gray-400" />
                                    <div class="text-gray-500">No roles found</div>
                                    @can('role.create')
                                    <flux:button size="sm" href="{{ route('roles.create') }}" variant="ghost">
                                        Create first role
                                    </flux:button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View (hidden on desktop) -->
    <div class="md:hidden space-y-4">
        @forelse ($roles as $role)
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-4 shadow-sm">
                <!-- Card Header -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2">
                        @if($role->name === 'Super Admin')
                            <flux:icon name="shield-check" class="w-5 h-5 text-red-500" />
                        @elseif($role->name === 'Admin')
                            <flux:icon name="shield-check" class="w-5 h-5 text-amber-500" />
                        @else
                            <flux:icon name="shield-check" class="w-5 h-5 text-blue-500" />
                        @endif
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $role->name }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Role #{{ $loop->iteration }}</p>
                        </div>
                    </div>
                    
                    <!-- Permission Count Badge -->
                    <flux:badge size="sm" color="blue">
                        <flux:icon name="key" class="w-3 h-3 mr-1" />
                        {{ $role->permissions->count() }} permissions
                    </flux:badge>
                </div>

                <!-- Permissions -->
                <div class="mb-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Permissions:</p>
                    <div class="flex flex-wrap gap-1">
                        @forelse ($role->permissions->take(6) as $permission)
                            <flux:badge size="sm" variant="outline">{{ $permission->name }}</flux:badge>
                        @empty
                            <span class="text-sm text-gray-500">No permissions assigned</span>
                        @endforelse
                        @if($role->permissions->count() > 6)
                            <flux:badge size="sm" color="gray">+{{ $role->permissions->count() - 6 }} more</flux:badge>
                        @endif
                    </div>
                </div>

                <!-- Card Actions -->
                <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-neutral-700">
                    <div class="flex gap-2">
                        @can('role.view')
                        <flux:button size="sm" href="{{ route('roles.show', $role->id) }}" variant="ghost">
                            <flux:icon name="eye" class="w-4 h-4 mr-1" />
                            View
                        </flux:button>
                        @endcan
                        
                        @can('role.edit')
                        <flux:button size="sm" href="{{ route('roles.edit', $role->id) }}" variant="ghost">
                            <flux:icon name="pencil" class="w-4 h-4 mr-1" />
                            Edit
                        </flux:button>
                        @endcan
                    </div>
                    
                    @can('role.delete')
                    <flux:modal.trigger name="delete-role">
                        <flux:button variant="ghost" size="sm" wire:click="confirmDelete({{ $role->id }})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            <flux:icon name="trash" class="w-4 h-4 mr-1" />
                        </flux:button>
                    </flux:modal.trigger>
                    @endcan
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-8 text-center">
                <div class="flex flex-col items-center gap-2">
                    <flux:icon name="shield-check" class="w-8 h-8 text-gray-400" />
                    <div class="text-gray-500">No roles found</div>
                    @can('role.create')
                    <flux:button size="sm" href="{{ route('roles.create') }}" variant="ghost" class="mt-2">
                        Create first role
                    </flux:button>
                    @endcan
                </div>
            </div>
        @endforelse
    </div>

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-role" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Role?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this role.</p>
                    <p class="text-red-600 mt-2">This action cannot be reversed.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" wire:click="delete" variant="danger">Delete Role</flux:button>
            </div>
        </div>
    </flux:modal>    
</div>