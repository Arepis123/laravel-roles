<div class="min-h-screen py-4 sm:py-8">
    <div class="max-w-6xl mx-auto px-0 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Edit User</h1>
                    <p class="mt-1 sm:mt-2 text-gray-600 dark:text-gray-400 text-sm sm:text-base">Update user information, roles and permissions</p>
                </div>
                <div class="flex items-center gap-2 sm:gap-3">
                    <flux:button variant="ghost" href="{{ route('users.show', $user->id) }}" size="sm" class="flex items-center gap-2">
                        <flux:icon.eye class="size-4" />
                        <span class="hidden sm:inline">View Profile</span>
                        <span class="sm:hidden">View</span>
                    </flux:button>
                    <flux:button variant="ghost" href="{{ route('users.index') }}" size="sm" class="flex items-center gap-2">
                        <flux:icon.arrow-left class="size-4" />
                        <span class="hidden sm:inline">Back to Users</span>
                        <span class="sm:hidden">Back</span>
                    </flux:button>
                </div>
            </div>
            <flux:separator class="mt-4 sm:mt-6" />
        </div>

        <!-- Current User Info Card -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700 mb-6">
            <div class="p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <flux:avatar circle size="md" color="auto" name="{{ $user->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $user->name) : 'N/A' }}" />
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $user->name }}</h3>
                            @if($user->status === 'active')
                                <flux:badge variant="success" size="sm">Active</flux:badge>
                            @else
                                <flux:badge size="sm">Inactive</flux:badge>
                            @endif
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base">{{ $user->email }}</p>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $user->getPositionBadgeColor() }}">
                                {{ $user->position }}
                            </span>
                            <span class="text-xs text-gray-500">•</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Member since {{ $user->created_at->format('M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form Container -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
            <form wire:submit="submit" class="p-0">
                <!-- Form Header -->
                <div class="px-4 sm:px-8 py-6 border-b border-gray-200 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Update Information</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Modify the user's details and permissions</p>
                </div>

                <!-- Form Content -->
                <div class="px-4 sm:px-8 py-6 space-y-8">
                    <!-- Personal Information Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <flux:icon.user class="size-5 text-blue-500" />
                                    Personal Details
                                </h3>
                                <div class="space-y-4">
                                    <flux:input 
                                        wire:model="name" 
                                        type="text" 
                                        label="Full Name *" 
                                        placeholder="Enter the user's full name" 
                                        required 
                                    />
                                    
                                    <flux:input 
                                        wire:model="email" 
                                        type="email" 
                                        label="Email Address *" 
                                        placeholder="user@company.com" 
                                        required 
                                    />
                                </div>
                            </div>

                            <!-- Password Section (Only for Super Admin) -->
                            @if(auth()->user()->hasRole(['Super Admin']))
                            <div>
                                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <flux:icon.lock-closed class="size-5 text-green-500" />
                                    Password Change
                                    <flux:badge variant="warning" size="sm">Optional</flux:badge>
                                </h3>
                                <div class="space-y-4">
                                    <flux:input 
                                        wire:model="password" 
                                        type="password" 
                                        label="New Password" 
                                        placeholder="Leave blank to keep current password" 
                                        description="Only fill if you want to change the password"
                                    />
                                    
                                    <flux:input 
                                        wire:model="confirm_password" 
                                        type="password" 
                                        label="Confirm New Password" 
                                        placeholder="Re-enter the new password" 
                                    />
                                </div>
                            </div>
                            @else
                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                                <div class="flex items-center gap-2">
                                    <flux:icon.shield-exclamation class="size-5 text-amber-500" />
                                    <span class="text-sm font-medium text-amber-700 dark:text-amber-300">Password Change Restricted</span>
                                </div>
                                <p class="text-sm text-amber-600 dark:text-amber-400 mt-1">Only Super Administrators can change user passwords.</p>
                            </div>
                            @endif
                        </div>

                        <!-- Account Configuration Section -->
                        <div class="space-y-6">
                            <!-- Status & Position -->
                            <div>
                                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <flux:icon.cog-6-tooth class="size-5 text-purple-500" />
                                    Account Configuration
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <flux:radio.group wire:model="status" label="Account Status *">
                                            <div class="grid grid-cols-2 gap-4 mt-2">
                                                <flux:radio label="Active" value="active" description="User can access the system" />
                                                <flux:radio label="Inactive" value="inactive" description="User access is suspended" />
                                            </div>
                                        </flux:radio.group>
                                    </div>

                                    <flux:select wire:model="position" label="Position *" placeholder="Select user position" required>
                                        @foreach($positions as $positionOption)
                                            <flux:select.option value="{{ $positionOption }}">
                                                <div class="flex items-center gap-2">
                                                    @if($positionOption === 'CEO')
                                                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                                    @elseif($positionOption === 'Manager')
                                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                    @elseif($positionOption === 'Executive')
                                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                    @else
                                                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                                    @endif
                                                    {{ $positionOption }}
                                                </div>
                                            </flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </div>
                            </div>

                            <!-- Roles Section -->
                            <div>
                                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <flux:icon.shield-check class="size-5 text-amber-500" />
                                    Role Management
                                </h3>
                                
                                <!-- Current Roles Display -->
                                <div class="mb-4 p-3 bg-gray-50 dark:bg-neutral-700 rounded-lg">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Roles:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($user->roles as $role)
                                            <flux:badge variant="outline" size="sm">
                                                @if($role->name === 'Super Admin')
                                                    <flux:icon.shield-user class="text-sky-500 me-1 size-3"/>
                                                @elseif($role->name === 'Admin')
                                                    <flux:icon.shield-user class="text-amber-500 me-1 size-3"/>
                                                @else
                                                    <flux:icon.shield-user class="text-green-500 me-1 size-3"/>
                                                @endif
                                                {{ $role->name }}
                                            </flux:badge>
                                        @empty
                                            <span class="text-sm text-gray-500">No roles assigned</span>
                                        @endforelse
                                    </div>
                                </div>

                                <flux:checkbox.group wire:model="roles" label="Update Roles" description="Select roles to assign to this user">
                                    <div class="grid grid-cols-1 gap-3 mt-3">
                                        @foreach ($allRoles as $role)
                                            @if ($role->name === 'Super Admin')
                                                @if (auth()->user()->hasRole('Super Admin'))
                                                    <label class="flex items-center p-3 rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 cursor-pointer hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                                                        <input type="radio" wire:model="selectedRole" value="{{ $role->name }}" class="sr-only" />
                                                        <div class="flex items-center flex-1">
                                                            <div class="flex items-center">
                                                                <div class="w-4 h-4 border-2 border-red-300 rounded-full mr-3 flex items-center justify-center">
                                                                    <div class="w-2 h-2 bg-red-500 rounded-full opacity-0 transition-opacity" 
                                                                         :class="$wire.selectedRole === '{{ $role->name }}' ? 'opacity-100' : 'opacity-0'"></div>
                                                                </div>
                                                                <span class="font-medium text-red-700 dark:text-red-300">{{ $role->name }}</span>
                                                            </div>
                                                        </div>
                                                        <flux:badge variant="danger" size="sm">Restricted</flux:badge>
                                                    </label>
                                                @endif
                                            @else
                                                <label class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-neutral-600 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer transition-colors">
                                                    <input type="radio" wire:model="selectedRole" value="{{ $role->name }}" class="sr-only" />
                                                    <div class="flex items-center flex-1">
                                                        <div class="flex items-center">
                                                            <div class="w-4 h-4 border-2 border-gray-300 dark:border-neutral-500 rounded-full mr-3 flex items-center justify-center">
                                                                <div class="w-2 h-2 bg-blue-500 rounded-full opacity-0 transition-opacity" 
                                                                     :class="$wire.selectedRole === '{{ $role->name }}' ? 'opacity-100' : 'opacity-0'"></div>
                                                            </div>
                                                            <span class="font-medium text-gray-900 dark:text-white">{{ $role->name }}</span>
                                                        </div>
                                                    </div>
                                                    @if($role->name === 'Admin')
                                                        <flux:badge variant="warning" size="sm">Admin</flux:badge>
                                                    @else
                                                        <flux:badge variant="outline" size="sm">Standard</flux:badge>
                                                    @endif
                                                </label>
                                            @endif
                                        @endforeach
                                    </div>
                                </flux:checkbox.group>
                            </div>
                        </div>
                    </div>

                    <!-- Warning Box for Important Changes -->
                    @if($user->hasRole('Super Admin') || $user->hasRole('Admin'))
                    <flux:callout color="red" icon="exclamation-triangle">
                        <div class="text-sm">
                            <flux:callout.heading class="mb-2">Administrative User Warning</flux:callout.heading>                            
                            <flux:callout.text>
                                You are modifying a user with administrative privileges. Changes to roles or status 
                                may affect system access and permissions. Please proceed with caution.
                            </flux:callout.text>                            
                        </div>
                    </flux:callout>                    
                    @else
                    <flux:callout color="blue" icon="information-circle">
                        <div class="text-sm">
                            <flux:callout.heading class="mb-2">Update Guidelines</flux:callout.heading>                            
                            <flux:callout.text>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Changes will be applied immediately after saving</li>
                                    <li>The user will retain access to existing bookings and data</li>
                                    <li>Role changes may affect system permissions</li>
                                    <li>Password changes (if any) will require re-login</li>
                                </ul>
                            </flux:callout.text>                            
                        </div>
                    </flux:callout>                    
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="px-8 py-6 bg-gray-50 dark:bg-neutral-700 border-t border-gray-200 dark:border-neutral-600 rounded-b-xl">
                    <div class="flex items-center justify-between">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Last updated: {{ $user->updated_at->format('M j, Y \a\t g:i A') }}
                        </div>
                        <div class="flex items-center gap-3">
                            <flux:button variant="ghost" href="{{ route('users.index') }}" class="px-6">
                                Cancel
                            </flux:button>
                            <flux:button variant="primary" type="submit" class="px-8">
                                <flux:icon.check class="size-4 mr-2 inline" />
                                <div class="inline">Update User</div>
                            </flux:button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Loading State -->
        <div wire:loading.flex class="fixed inset-0 bg-gray-100/70 dark:bg-neutral-900/60 backdrop-blur-sm items-center justify-center z-50">
            <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-lg p-6 flex items-center gap-3">
                <flux:icon.arrow-path class="size-5 text-blue-500 animate-spin" />
                <span class="text-gray-900 dark:text-white">Updating user...</span>
            </div>
        </div>
    </div>
</div>