<div class="min-h-screen py-4 sm:py-8">
    <div class="max-w-6xl mx-auto px-0 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">User Profile</h1>
                    <p class="mt-1 sm:mt-2 text-gray-600 dark:text-gray-400 text-sm sm:text-base">Detailed information about {{ $user->name }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    @can('user.edit')
                    <flux:button variant="primary" href="{{ route('users.edit', $user->id) }}" size="sm" class="flex items-center gap-2">
                        <flux:icon.pencil class="size-4" />
                        <span class="hidden sm:inline">Edit User</span>
                        <span class="sm:hidden">Edit</span>
                    </flux:button>
                    @endcan
                    <flux:button variant="ghost" href="{{ route('users.index') }}" size="sm" class="flex items-center gap-2">
                        <flux:icon.arrow-left class="size-4" />
                        <span class="hidden sm:inline">Back to Users</span>
                        <span class="sm:hidden">Back</span>
                    </flux:button>
                </div>
            </div>
            <flux:separator class="mt-4 sm:mt-6" />
        </div>

        <!-- User Profile Header Card -->
        @php
            $gradients = [
                'bg-gradient-to-br from-pink-100/80 via-red-100/60 to-yellow-100/60',
                'bg-gradient-to-br from-green-100/80 via-emerald-100/60 to-teal-100/60',
                'bg-gradient-to-br from-blue-100/80 via-indigo-100/60 to-purple-100/60',
                'bg-gradient-to-br from-orange-100/80 via-amber-100/60 to-pink-100/60',
                'bg-gradient-to-br from-sky-100/80 via-cyan-100/60 to-blue-100/60',
            ];
            $randomGradient = $gradients[array_rand($gradients)];
        @endphp         
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700 mb-6 sm:mb-8">
            <div class="relative">
                <!-- Background Pattern -->
                <div class="absolute inset-0 h-20 sm:h-32 rounded-t-xl bg-white dark:bg-neutral-900"></div>
                <div class="absolute inset-0 h-20 sm:h-32 rounded-t-xl {{ $randomGradient }}"></div>
                <!-- Subtle border accent -->
                <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
                
                <!-- Profile Content -->
                <div class="relative px-4 sm:px-8 py-4 sm:py-6">
                    <div class="flex flex-col sm:flex-row items-center sm:items-end gap-4 sm:gap-6 pt-8 sm:pt-16">
                        <!-- Avatar -->
                        <div class="relative flex-shrink-0">
                            <flux:avatar 
                                circle 
                                size="xl" 
                                color="auto" 
                                name="{{ $user->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $user->name) : 'N/A' }}" 
                                class="ring-4 ring-white dark:ring-neutral-800 bg-white dark:bg-neutral-800" 
                            />
                            <!-- Status Indicator -->
                            <div class="absolute -bottom-1 -right-1">
                                @if($user->status === 'active')
                                    <div class="w-5 h-5 sm:w-6 sm:h-6 bg-green-500 border-2 border-white dark:border-neutral-800 rounded-full flex items-center justify-center">
                                        <flux:icon.check class="size-2 sm:size-3 text-white" />
                                    </div>
                                @else
                                    <div class="w-5 h-5 sm:w-6 sm:h-6 bg-red-500 border-2 border-white dark:border-neutral-800 rounded-full flex items-center justify-center">
                                        <flux:icon.x-mark class="size-2 sm:size-3 text-white" />
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="flex-1 text-center sm:text-left sm:pb-2 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-2">
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $user->name }}</h2>
                                <div class="flex items-center justify-center sm:justify-start gap-2">
                                    @if($user->status === 'active')
                                        <flux:badge variant="success" size="sm">Active</flux:badge>
                                    @else
                                        <flux:badge variant="outline" size="sm">Inactive</flux:badge>
                                    @endif
                                    @if($user->isManagement())
                                        <flux:badge variant="warning" size="sm">Management</flux:badge>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-gray-600 dark:text-gray-300 mb-3 text-sm">
                                <div class="flex items-center justify-center sm:justify-start gap-2">
                                    <flux:icon.envelope class="size-4" />
                                    <span class="truncate">{{ $user->email }}</span>
                                </div>
                                <div class="flex items-center justify-center sm:justify-start gap-2">
                                    <flux:icon.calendar class="size-4" />
                                    <span>Joined {{ $user->created_at->format('M Y') }}</span>
                                </div>
                            </div>

                            <!-- Position and Role -->
                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 sm:gap-3">
                                <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium {{ $user->getPositionBadgeColor() }}">
                                    <flux:icon.briefcase class="size-3 mr-1" />
                                    {{ $user->position }}
                                </span>
                                @if($user->roles->first())
                                    <flux:badge variant="outline" size="sm">
                                        @if($user->roles->first()->name === 'Super Admin')
                                            <flux:icon.shield-user class="text-sky-500 me-1 size-3 sm:size-4"/>
                                        @elseif($user->roles->first()->name === 'Admin')
                                            <flux:icon.shield-user class="text-amber-500 me-1 size-3 sm:size-4"/>
                                        @else
                                            <flux:icon.shield-user class="text-green-500 me-1 size-3 sm:size-4"/>
                                        @endif
                                        {{ $user->roles->first()->name }}
                                    </flux:badge>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Stats - Hidden on small screens, shown inline on large screens -->
                        <div class="flex sm:hidden w-full justify-center gap-6 pt-4 border-t border-gray-200 dark:border-neutral-700">
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->bookings->count() }}</div>
                                <div class="text-xs text-gray-500">Bookings</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ round($user->created_at->diffInDays(now(), false, true)) }}
                                </div>
                                <div class="text-xs text-gray-500">Days Active</div>
                            </div>
                        </div>
                        
                        <!-- Quick Stats for larger screens -->
                        <div class="hidden lg:flex items-center gap-8 pb-2">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->bookings->count() }}</div>
                                <div class="text-sm text-gray-500">Total Bookings</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ round($user->created_at->diffInDays(now(), false, true)) }}
                                </div>
                                <div class="text-sm text-gray-500">Days Active</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 lg:gap-8">
            <!-- Main Content - Full width on mobile, 2/3 on desktop -->
            <div class="xl:col-span-2 space-y-4 sm:space-y-6">
                <!-- Personal Information -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <flux:icon.user class="size-4 sm:size-5 text-blue-500" />
                            Personal Information
                        </h3>
                    </div>
                    <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <flux:field>
                                    <flux:label class="text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</flux:label>
                                    <div class="mt-1 p-2 sm:p-3 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                                        <span class="text-gray-900 dark:text-white font-medium text-sm sm:text-base">{{ $user->name }}</span>
                                    </div>
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</flux:label>
                                    <div class="mt-1 p-2 sm:p-3 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                                        <span class="text-gray-900 dark:text-white font-medium text-sm sm:text-base break-all">{{ $user->email }}</span>
                                    </div>
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label class="text-sm font-medium text-gray-700 dark:text-gray-300">User ID</flux:label>
                                    <div class="mt-1 p-2 sm:p-3 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                                        <span class="text-gray-900 dark:text-white font-mono text-sm sm:text-base">#{{ $user->id }}</span>
                                    </div>
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label class="text-sm font-medium text-gray-700 dark:text-gray-300">Account Status</flux:label>
                                    <div class="mt-1 p-2 sm:p-3 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                                        @if($user->status === 'active')
                                            <flux:badge variant="success" size="sm">
                                                <flux:icon.check-circle class="size-3 mr-1" />
                                                Active Account
                                            </flux:badge>
                                        @else
                                            <flux:badge variant="outline" size="sm">
                                                <flux:icon.x-circle class="size-3 mr-1" />
                                                Inactive Account
                                            </flux:badge>
                                        @endif
                                    </div>
                                </flux:field>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role & Position Details -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <flux:icon.shield-check class="size-4 sm:size-5 text-amber-500" />
                            Role & Permissions
                        </h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <flux:field>
                                    <flux:label class="text-sm font-medium text-gray-700 dark:text-gray-300">Position</flux:label>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-sm font-medium {{ $user->getPositionBadgeColor() }}">
                                            <flux:icon.briefcase class="size-3 sm:size-4 mr-2" />
                                            {{ $user->position }}
                                        </span>
                                    </div>
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label class="text-sm font-medium text-gray-700 dark:text-gray-300">Current Role</flux:label>
                                    <div class="mt-1">
                                        @if($user->roles->first())
                                            <flux:badge variant="outline" size="md">
                                                @if($user->roles->first()->name === 'Super Admin')
                                                    <flux:icon.shield-user class="text-sky-500 me-2 size-4"/>
                                                @elseif($user->roles->first()->name === 'Admin')
                                                    <flux:icon.shield-user class="text-amber-500 me-2 size-4"/>
                                                @else
                                                    <flux:icon.shield-user class="text-green-500 me-2 size-4"/>
                                                @endif
                                                {{ $user->roles->first()->name }}
                                            </flux:badge>
                                        @else
                                            <span class="text-sm text-gray-500 italic">No role assigned</span>
                                        @endif
                                    </div>
                                </flux:field>
                            </div>
                        </div>

                        @if($user->isManagement())
                        <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-start gap-3">
                                <flux:icon.information-circle class="size-4 sm:size-5 text-blue-500 mt-0.5 flex-shrink-0" />
                                <div>
                                    <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Management Access</p>
                                    <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                                        This user has management-level access with enhanced permissions for system administration and oversight.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Activity Summary -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <flux:icon.chart-bar class="size-4 sm:size-5 text-green-500" />
                            Activity Overview
                        </h3>
                    </div>
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                            <div class="text-center p-3 sm:p-4 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                                <div class="text-xl sm:text-2xl font-bold text-blue-600 mb-1">{{ $user->bookings->count() }}</div>
                                <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">Total Bookings</div>
                            </div>
                            <div class="text-center p-3 sm:p-4 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                                <div class="text-xl sm:text-2xl font-bold text-green-600 mb-1">
                                    {{ $user->bookings->where('created_at', '>=', now()->subMonth())->count() }}
                                </div>
                                <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">This Month</div>
                            </div>
                            <div class="text-center p-3 sm:p-4 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                                <div class="text-xl sm:text-2xl font-bold text-purple-600 mb-1">
                                    {{ round($user->created_at->diffInDays(now(), false, true)) }}
                                </div>
                                <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">Days Active</div>
                            </div>
                        </div>
                        
                        @if($user->bookings->count() > 0)
                        <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200 dark:border-neutral-700">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <flux:icon.calendar class="size-4 inline mr-1" />
                                Last booking: {{ $user->bookings->sortByDesc('created_at')->first()?->created_at?->format('M j, Y \a\t g:i A') ?? 'Never' }}
                            </p>
                        </div>
                        @else
                        <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200 dark:border-neutral-700 text-center">
                            <div class="text-gray-400">
                                <flux:icon.calendar-days class="size-6 sm:size-8 mx-auto mb-2" />
                                <p class="text-xs sm:text-sm">No bookings yet</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Quick Info & Actions -->
            <div class="space-y-4 sm:space-y-6">
                <!-- Account Timeline -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <flux:icon.clock class="size-4 sm:size-5 text-purple-500" />
                            Account Timeline
                        </h3>
                    </div>
                    <div class="p-4 sm:p-6 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Account Created</p>
                                <p class="text-xs text-gray-500">{{ $user->created_at->format('F j, Y \a\t g:i A') }}</p>
                                <p class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Last Updated</p>
                                <p class="text-xs text-gray-500">{{ $user->updated_at->format('F j, Y \a\t g:i A') }}</p>
                                <p class="text-xs text-gray-400">{{ $user->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                @canany(['user.edit', 'user.delete'])
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <flux:icon.cog-6-tooth class="size-4 sm:size-5 text-gray-500" />
                            Quick Actions
                        </h3>
                    </div>
                    <div class="p-4 sm:p-6 space-y-2 sm:space-y-3">
                        @can('user.edit')
                        <flux:button variant="ghost" href="{{ route('users.edit', $user->id) }}" size="sm" class="w-full justify-start">
                            <flux:icon.pencil class="size-4 mr-3 inline" />
                            <div class="inline">Edit Profile</div>
                        </flux:button>
                        @endcan
                        
                        @can('user.edit')
                        <flux:button variant="ghost" wire:click="toggleStatus({{ $user->id }})" size="sm" class="w-full justify-start">
                            @if($user->status === 'active')
                                <flux:icon.stop class="size-4 mr-3 text-red-500 inline"/>
                                <div class="inline">Deactivate Account</div>
                            @else
                                <flux:icon.play class="size-4 mr-3 text-green-500 inline" />
                                <div class="inline">Activate Account</div>
                            @endif
                        </flux:button>
                        @endcan
                        
                        @if(auth()->user()->hasRole(['Super Admin']) && $user->id !== auth()->id())
                        @can('user.delete')
                        <flux:modal.trigger name="delete-user-{{ $user->id }}">
                            <flux:button variant="ghost" size="sm" class="w-full justify-start text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20">
                                <flux:icon.trash class="size-4 mr-3 inline" />
                                <div class="inline">Delete User</div>
                            </flux:button>
                        </flux:modal.trigger>
                        @endcan
                        @endif
                    </div>
                </div>
                @endcanany

                <!-- System Information -->
                <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <flux:icon.information-circle class="size-4 sm:size-5 text-gray-500" />
                            System Info
                        </h3>
                    </div>
                    <div class="p-4 sm:p-6 space-y-3 sm:space-y-4 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">User ID:</span>
                            <span class="font-mono text-gray-900 dark:text-white">#{{ $user->id }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <span class="font-medium {{ $user->status === 'active' ? 'text-green-600' : 'text-red-600' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Role:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $user->roles->first()?->name ?? 'No Role' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Position:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $user->position }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        @if(auth()->user()->hasRole(['Super Admin']) && $user->id !== auth()->id())
        @can('user.delete')
        <flux:modal name="delete-user-{{ $user->id }}" class="min-w-[20rem] sm:min-w-[24rem]">
            <div class="space-y-4 sm:space-y-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-red-100 dark:bg-red-900/20 mb-3 sm:mb-4">
                        <flux:icon.exclamation-triangle class="size-5 sm:size-6 text-red-600" />
                    </div>
                    <flux:heading size="md" class="text-gray-900 dark:text-white">Delete User Account</flux:heading>
                    <flux:text class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                        You're about to permanently delete <strong>{{ $user->name }}</strong>'s account.
                    </flux:text>
                </div>
                
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 sm:p-4">
                    <div class="flex items-start gap-3">
                        <flux:icon.exclamation-triangle class="size-4 sm:size-5 text-red-500 mt-0.5 flex-shrink-0" />
                        <div class="text-sm text-red-700 dark:text-red-300">
                            <p class="font-medium">This action cannot be undone!</p>
                            <ul class="list-disc list-inside mt-2 text-red-600 dark:text-red-400 space-y-1">
                                <li>All user data will be permanently removed</li>
                                <li>Associated bookings and history will be lost</li>
                                <li>User will lose all system access</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <flux:spacer class="hidden sm:block" />
                    <flux:modal.close class="order-2 sm:order-1">
                        <flux:button variant="ghost" size="sm" class="w-full sm:w-auto">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button wire:click="delete({{ $user->id }})" variant="danger" size="sm" class="w-full sm:w-auto order-1 sm:order-2">
                        <flux:icon.trash class="size-4 mr-2" />
                        Delete Account
                    </flux:button>
                </div>
            </div>
        </flux:modal>
        @endcan
        @endif
    </div>
</div>