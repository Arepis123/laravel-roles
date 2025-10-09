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
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Users</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format(\App\Models\User::notDeleted()->count()) }}</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Active Users</flux:heading>
                    <flux:text class="text-xl font-semibold text-green-600 dark:text-green-400">{{ number_format(\App\Models\User::notDeleted()->where('status', 'active')->count()) }}</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Inactive Users</flux:heading>
                    <flux:text class="text-xl font-semibold text-red-600 dark:text-red-400">{{ number_format(\App\Models\User::notDeleted()->where('status', 'inactive')->count()) }}</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Roles</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format(\Spatie\Permission\Models\Role::count()) }}</flux:text>
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Filters and Actions --}}
    <div class="mb-6 mx-2">
        <flux:accordion>
            <flux:accordion.item>
                <flux:accordion.heading>
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 transition-transform duration-200 accordion-icon"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filters & Actions
                    </span>
                </flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 pt-4 mx-3">
                        <flux:field>
                            <flux:label>Search Users</flux:label>
                            <flux:input
                                wire:model.live.debounce.300ms="search"
                                type="search"
                                placeholder="Search by name or email..."
                            >
                                <flux:icon.magnifying-glass slot="leading" class="size-4" />
                            </flux:input>
                        </flux:field>

                        <flux:field>
                            <flux:label>Status</flux:label>
                            <flux:select variant="listbox" wire:model.live="statusFilter" placeholder="All Status">
                                <flux:select.option value="">All Status</flux:select.option>
                                <flux:select.option value="active">Active</flux:select.option>
                                <flux:select.option value="inactive">Inactive</flux:select.option>
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>Role</flux:label>
                            <flux:select variant="listbox" wire:model.live="roleFilter" placeholder="All Roles">
                                <flux:select.option value="">All Roles</flux:select.option>
                                @foreach($roles as $role)
                                    <flux:select.option value="{{ $role }}">{{ $role }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:field>

                        <flux:field>
                            <flux:label>Position</flux:label>
                            <flux:select variant="listbox" wire:model.live="positionFilter" placeholder="All Positions">
                                <flux:select.option value="">All Positions</flux:select.option>
                                @foreach($positions as $position)
                                    <flux:select.option value="{{ $position }}">{{ $position }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3 pt-4 mx-3">
                        <flux:button variant="filled" size="sm" wire:click="resetFilters" icon="arrow-path" class="bg-gray-600 hover:bg-gray-700">
                            Reset Filters
                        </flux:button>

                        @can('user.create')
                        <flux:button variant="filled" size="sm" href="{{ route('users.create') }}" icon="plus" class="bg-blue-600 hover:bg-blue-700">
                            Create User
                        </flux:button>
                        @endcan
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </div>

    {{-- Users Table --}}
    <div class="hidden md:block sm:block bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th wire:click="sort('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>User</span>
                                @if($sortField === 'name')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th wire:click="sort('email')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Email</span>
                                @if($sortField === 'email')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th wire:click="sort('position')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Position</span>
                                @if($sortField === 'position')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Roles
                        </th>
                        <th wire:click="sort('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Status</span>
                                @if($sortField === 'status')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    @endif
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700" wire:loading.class="opacity-50">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
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