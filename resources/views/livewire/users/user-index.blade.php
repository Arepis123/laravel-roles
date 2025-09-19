<div>
    <div class="relative mb-6 w-full">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Users') }}</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('Manage all system users') }}</p>
        <flux:separator variant="subtle" class="mt-4" />        
    </div>    
   
    @if(session('success'))
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" class="mb-4" />
    @endif
    
    @if(session('error'))
        <flux:callout variant="danger" icon="x-circle" heading="{{ session('error') }}" class="mb-4" />
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\User::notDeleted()->count() }}</p>
                </div>
                <flux:icon.users class="size-8 text-blue-500" />
            </div>
        </div>
        
        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Active Users</p>
                    <p class="text-2xl font-bold text-green-600">{{ \App\Models\User::notDeleted()->where('status', 'active')->count() }}</p>
                </div>
                <flux:icon.check-circle class="size-8 text-green-500" />
            </div>
        </div>
        
        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Inactive Users</p>
                    <p class="text-2xl font-bold text-red-600">{{ \App\Models\User::notDeleted()->where('status', 'inactive')->count() }}</p>
                </div>
                <flux:icon.x-circle class="size-8 text-red-500" />
            </div>
        </div>
        
        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Roles</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \Spatie\Permission\Models\Role::count() }}</p>
                </div>
                <flux:icon.shield-check class="size-8 text-purple-500" />
            </div>
        </div>
    </div>

    {{-- Filters and Actions --}}
    <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 p-4 mb-6">
        <div class="space-y-4">
            <!-- Mobile-first stacked layout -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4">
                <div class="sm:col-span-2 lg:col-span-3">
                    <flux:input 
                        wire:model.live.debounce.300ms="search" 
                        type="search" 
                        placeholder="Search users..." 
                        icon="magnifying-glass"
                    />
                </div>
                
                <div class="lg:col-span-2">
                    <flux:select wire:model.live="statusFilter" placeholder="All Status">
                        <flux:select.option value="">All Status</flux:select.option>
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                    </flux:select>
                </div>
                
                <div class="lg:col-span-2">
                    <flux:select wire:model.live="roleFilter" placeholder="All Roles">
                        <flux:select.option value="">All Roles</flux:select.option>
                        @foreach($roles as $role)
                            <flux:select.option value="{{ $role }}">{{ $role }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="lg:col-span-2">
                    <flux:select wire:model.live="positionFilter" placeholder="All Positions">
                        <flux:select.option value="">All Positions</flux:select.option>
                        @foreach($positions as $position)
                            <flux:select.option value="{{ $position }}">{{ $position }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                
                <div class="lg:col-span-1">
                    <flux:button wire:click="resetFilters" variant="filled" class="w-full">
                        <flux:icon.arrow-path class="w-4 h-4" />
                        <!-- <span class="hidden sm:inline">Reset</span> -->
                    </flux:button>                
                </div>
                
                @can('user.create')
                <div class="lg:col-span-2">
                    <flux:button variant="primary" href="{{ route('users.create') }}" class="w-full">
                        <flux:icon.plus class="w-4 h-4 sm:me-1" />
                        <span class="hidden sm:inline">Create User</span>
                        <span class="sm:hidden">Create</span>
                    </flux:button>
                </div>
                @endcan
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="hidden md:block sm:block bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-900">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left">
                            <button 
                                wire:click="sort('name')" 
                                class="group inline-flex items-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 transition-colors cursor-pointer"
                            >
                                User
                                <span class="ml-2 flex-none rounded">
                                    @if($sortField === 'name')
                                        @if($sortDirection === 'asc')
                                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                            </svg>
                                        @else
                                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="size-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    @endif
                                </span>
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left">
                            <button 
                                wire:click="sort('email')" 
                                class="group inline-flex items-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 transition-colors cursor-pointer"
                            >
                                Email
                                <span class="ml-2 flex-none rounded">
                                    @if($sortField === 'email')
                                        @if($sortDirection === 'asc')
                                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                            </svg>
                                        @else
                                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="size-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    @endif
                                </span>
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left">
                            <button 
                                wire:click="sort('position')" 
                                class="group inline-flex items-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 transition-colors cursor-pointer"
                            >
                                Position
                                <span class="ml-2 flex-none rounded">
                                    @if($sortField === 'position')
                                        @if($sortDirection === 'asc')
                                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                            </svg>
                                        @else
                                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="size-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    @endif
                                </span>
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left">
                            <button 
                                wire:click="sort('roles')" 
                                class="group inline-flex items-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 transition-colors cursor-pointer"
                            >
                                Roles
                                <span class="ml-2 flex-none rounded">
                                    @if($sortField === 'roles')
                                        @if($sortDirection === 'asc')
                                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                            </svg>
                                        @else
                                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="size-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    @endif
                                </span>
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left">
                            <button 
                                wire:click="sort('status')" 
                                class="group inline-flex items-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 transition-colors cursor-pointer"
                            >
                                Status
                                <span class="ml-2 flex-none rounded">
                                    @if($sortField === 'status')
                                        @if($sortDirection === 'asc')
                                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                            </svg>
                                        @else
                                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        @endif
                                    @else
                                        <svg class="size-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    @endif
                                </span>
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700" wire:loading.class="opacity-50">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">                                        
                                        <flux:avatar circle color="auto" name="{{ $user->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $user->name) : 'N/A' }}" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            ID: #{{ $user->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-200">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <!-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->getPositionBadgeColor() }}"> -->
                                    <div class="text-sm text-gray-900 dark:text-gray-200">{{ $user->position }}</div>
                                <!-- </span> -->
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($user->roles as $role)
                                        <flux:badge variant="outline">
                                            @if($role->name === 'Super Admin')
                                                <flux:icon.shield-user class="text-sky-500 me-1 size-5"/>
                                            @elseif($role->name === 'Admin')
                                                <flux:icon.shield-user class="text-amber-500 me-1 size-5"/>
                                            @else
                                                <flux:icon.shield-user class="text-green-500 me-1 size-5"/>
                                            @endif
                                            {{ $role->name }}
                                        </flux:badge>
                                        <!-- <div class="text-sm text-gray-900 dark:text-gray-200">{{ $role->name }}</div> -->
                                    @empty
                                        <span class="text-sm text-gray-500">No roles</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->status === 'active')
                                    <!-- <flux:badge variant="success">Active</flux:badge> -->
                                    <div class="text-sm text-gray-900 dark:text-gray-200">Active</div>
                                @else
                                    <!-- <flux:badge>Inactive</flux:badge> -->
                                    <div class="text-sm text-gray-900 dark:text-gray-200">Inactive</div>
                                @endif
                            </td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->created_at->format('M d, Y') }}
                            </td> -->
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-1">
                                    @can('user.view')
                                        <flux:button size="sm" variant="ghost" href="{{ route('users.show', $user->id) }}" title="View">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </flux:button>
                                    @endcan                                    
                                    
                                    @can('user.edit')
                                        <flux:button size="sm" variant="ghost" href="{{ route('users.edit', $user->id) }}" title="Edit">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </flux:button>
                                    @endcan

                                    @can('user.edit')
                                    <flux:dropdown>
                                        <flux:button icon="chevron-down" size="sm" variant="ghost"></flux:button>                                           
                                        <flux:menu>
                                            <flux:menu.submenu heading="Change status" icon="cog-6-tooth">
                                                <flux:menu.radio.group>
                                                    <flux:menu.radio :checked="$user->status === 'active'" wire:click="toggleStatus({{ $user->id }})">                                                        
                                                        Active
                                                    </flux:menu.radio>
                                                    <flux:menu.radio :checked="$user->status == 'inactive'" wire:click="toggleStatus({{ $user->id }})">                                                        
                                                        Inactive
                                                    </flux:menu.radio>
                                                </flux:menu.radio.group>
                                            </flux:menu.submenu>
                                            @if(auth()->user()->hasRole(['Super Admin']))
                                            <flux:menu.separator />
                                            @if($user->id !== auth()->id())
                                                <flux:menu.item wire:click="confirmDelete({{ $user->id }}, '{{ $user->name }}')" icon="trash" variant="danger">
                                                    Delete User
                                                </flux:menu.item>
                                                @endif
                                            @endif 
                                        </flux:menu>
                                    </flux:dropdown>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="size-12 text-gray-400 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400">No users found</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting your search or filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Mobile Card View --}}
    <div class="block lg:hidden space-y-3">
        @forelse ($users as $user)
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-4 shadow-sm">
                <!-- Card Header -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <flux:avatar circle size="sm" color="auto" name="{{ $user->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $user->name) : 'N/A' }}" />
                        <div class="min-w-0 flex-1">
                            <h3 class="font-medium text-gray-900 dark:text-white truncate text-sm" title="{{ $user->name }}">
                                {{ Str::limit($user->name, 25) }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">ID: #{{ $user->id }}</p>
                        </div>
                    </div>
                    
                    @can('user.edit')
                    <flux:dropdown>
                        <flux:button icon="ellipsis-vertical" size="sm" variant="ghost"></flux:button>
                        <flux:menu>
                            @can('user.view')
                            <flux:menu.item href="{{ route('users.show', $user->id) }}" icon="eye">
                                View Details
                            </flux:menu.item>
                            @endcan
                            @can('user.edit')
                            <flux:menu.item href="{{ route('users.edit', $user->id) }}" icon="pencil">
                                Edit User
                            </flux:menu.item>
                            @endcan
                            <flux:menu.separator />
                            <flux:menu.submenu heading="Status" icon="cog-6-tooth">
                                <flux:menu.radio.group>
                                    <flux:menu.radio :checked="$user->status === 'active'" wire:click="toggleStatus({{ $user->id }})">
                                        Active
                                    </flux:menu.radio>
                                    <flux:menu.radio :checked="$user->status == 'inactive'" wire:click="toggleStatus({{ $user->id }})">
                                        Inactive
                                    </flux:menu.radio>
                                </flux:menu.radio.group>
                            </flux:menu.submenu>
                            @if(auth()->user()->hasRole(['Super Admin']) && $user->id !== auth()->id())
                            <flux:menu.separator />
                            <flux:menu.item wire:click="confirmDelete({{ $user->id }}, '{{ $user->name }}')" icon="trash" variant="danger">
                                Delete User
                            </flux:menu.item>
                            @endif
                        </flux:menu>
                    </flux:dropdown>
                    @endcan
                </div>

                <!-- User Info Grid -->
                <div class="space-y-2">
                    <!-- Email -->
                    <div class="flex items-center gap-2 text-sm">
                        <flux:icon.envelope class="w-3 h-3 text-gray-400 flex-shrink-0" />
                        <span class="text-gray-600 dark:text-gray-300 truncate">{{ $user->email }}</span>
                    </div>

                    <!-- Status & Position Row -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            @if($user->status === 'active')
                                <flux:badge variant="success" size="sm">Active</flux:badge>
                            @else
                                <flux:badge size="sm">Inactive</flux:badge>
                            @endif
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $user->getPositionBadgeColor() }}">
                                {{ $user->position }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $user->created_at->format('M j') }}
                        </div>
                    </div>

                    <!-- Role -->
                    @if($user->roles->first())
                    <div class="flex items-center gap-2">
                        <flux:icon.shield-check class="w-3 h-3 text-gray-400 flex-shrink-0" />
                        <flux:badge variant="outline" size="sm">
                            {{ $user->roles->first()->name }}
                        </flux:badge>
                    </div>
                    @endif
                </div>

            </div>
        @empty
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-8 text-center">
                <flux:icon.users class="size-12 text-gray-400 mx-auto mb-3" />
                <p class="text-gray-500 dark:text-gray-400">No users found</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting filters</p>
            </div>
        @endforelse

        @if($users->hasPages())
        <div class="mt-4">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model="showDeleteModal" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete User?</flux:heading>

                <flux:text class="mt-2">
                    <p>You're about to delete <strong>{{ $userToDelete['name'] ?? '' }}</strong>.</p>
                    <p class="text-red-600 mt-2">This action cannot be reversed.</p>
                </flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:button wire:click="cancelDelete" variant="ghost">Cancel</flux:button>

                <flux:button wire:click="confirmDeleteUser" variant="danger">Delete User</flux:button>
            </div>
        </div>
    </flux:modal>
</div>