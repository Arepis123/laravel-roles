<div>
    <!-- Page Header -->
    <div class="relative mb-6 w-full">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $name }}</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400">{{ __('Role details and permissions overview') }}</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="flex gap-4 text-sm">
                <div class="text-center bg-white dark:bg-neutral-800 rounded-lg p-3 border border-gray-200 dark:border-neutral-700">
                    <div class="font-semibold text-blue-600">{{ is_array($permissions) ? count($permissions) : $permissions->count() }}</div>
                    <div class="text-gray-500 text-xs">Permissions</div>
                </div>
            </div>
        </div>
        <flux:separator variant="subtle" class="mt-4" />        
    </div>

    <!-- Navigation -->
    <div class="flex flex-col sm:flex-row gap-2 mb-6">
        <flux:button variant="primary" href="{{ route('roles.index') }}" icon="arrow-left" class="w-full sm:w-auto">
            Back to Roles
        </flux:button>
        @can('role.edit')
        <flux:button href="{{ route('roles.edit', $role->id ?? request()->route('role')->id) }}" icon="pencil" class="w-full sm:w-auto">
            Edit Role
        </flux:button>
        @endcan
    </div>

    <!-- Role Information Card -->
    <div class="bg-white dark:bg-zinc-900  rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden mb-6">
        <div class="px-6 py-3 bg-gray-50 dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">           
            <flux:icon name="identification" class="w-5 h-5 inline text-gray-900 dark:text-neutral-200 me-1" />
            <div class="text-left text-xs font-medium text-gray-900 dark:text-neutral-200 uppercase inline">Role Information</div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <flux:input wire:model="name" type="text" label="Role Name" readonly class="bg-gray-50 dark:bg-neutral-900" />
                </div>
                <div>
                    <flux:input value="{{ is_array($permissions) ? count($permissions) : $permissions->count() }} permissions assigned" label="Permission Count" readonly class="bg-gray-50 dark:bg-neutral-900" />
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-3 bg-gray-50 dark:bg-zinc-800 border-b border-gray-200 dark:border-zinc-700">        
            <flux:icon name="key" class="w-5 h-5 inline text-gray-900 dark:text-neutral-200 me-1" />
            <div class="text-left text-xs font-medium text-gray-900 dark:text-neutral-200 uppercase inline">
                Assigned Permissions ({{ is_array($permissions) ? count($permissions) : $permissions->count() }})
            </div>                           
        </div>
        <div class="p-6">
            @if((is_array($permissions) ? count($permissions) : $permissions->count()) > 0)
                @php
                    $permissionsArray = is_array($permissions) ? $permissions : $permissions->pluck('name')->toArray();
                @endphp
                
                <!-- Desktop Grid View -->
                <div class="hidden sm:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($allPermissions as $permission)
                        <div class="flex items-center p-3 rounded-lg border {{ in_array($permission->name, $permissionsArray) ? 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800' : 'bg-gray-50 border-gray-200 dark:bg-neutral-900 dark:border-neutral-700' }}">
                            <flux:checkbox 
                                label="{{ $permission->name}}" 
                                value="{{ $permission->name}}"
                                checked="{{ in_array($permission->name, $permissionsArray) }}"
                                disabled 
                            />
                        </div>
                    @endforeach
                </div>

                <!-- Mobile Grid View -->
                <div class="sm:hidden grid grid-cols-2 gap-3">
                    @foreach ($allPermissions as $permission)
                        <div class="flex items-center p-3 rounded-lg border {{ in_array($permission->name, $permissionsArray) ? 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800' : 'bg-gray-50 border-gray-200 dark:bg-neutral-900 dark:border-neutral-700' }}">
                            <flux:checkbox 
                                label="{{ $permission->name}}" 
                                value="{{ $permission->name}}" 
                                checked="{{ in_array($permission->name, $permissionsArray) }}" 
                                disabled 
                                class="pointer-events-none text-sm"
                            />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <flux:icon name="key" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                    <p class="text-gray-500 dark:text-gray-400">No permissions assigned to this role</p>
                    @can('role.edit')
                    <flux:button size="sm" href="{{ route('roles.edit', $role->id ?? request()->route('role')->id) }}" variant="ghost" class="mt-2">
                        Add Permissions
                    </flux:button>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>