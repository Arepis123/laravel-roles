<div class="min-h-screen py-4 sm:py-8">
    <div class="max-w-6xl mx-auto px-0 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New User</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Add a new user to the system with roles and permissions</p>
                </div>
                <flux:button variant="ghost" href="{{ route('users.index') }}" class="flex items-center gap-2">
                    <flux:icon.arrow-left class="size-4" />
                    Back to Users
                </flux:button>
            </div>
            <flux:separator class="mt-6" />
        </div>

        <!-- Form Container -->
        <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-neutral-700">
            <form wire:submit="submit" class="p-0">
                <!-- Form Header -->
                <div class="px-8 py-6 border-b border-gray-200 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">User Information</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Please fill in all required fields marked with *</p>
                </div>

                <!-- Form Content -->
                <div class="p-8 space-y-8">
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

                            <!-- Account Security Section -->
                            <div>
                                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <flux:icon.lock-closed class="size-5 text-green-500" />
                                    Account Security
                                </h3>
                                <div class="space-y-4">
                                    <flux:input 
                                        wire:model="password" 
                                        type="password" 
                                        label="Password *" 
                                        placeholder="Minimum 8 characters" 
                                        required 
                                        description="Password must be at least 8 characters long"
                                    />
                                    
                                    <flux:input 
                                        wire:model="confirm_password" 
                                        type="password" 
                                        label="Confirm Password *" 
                                        placeholder="Re-enter the password" 
                                        required 
                                    />
                                </div>
                            </div>
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
                                    Role Assignment
                                </h3>
                                <div>
                                    <flux:field>
                                        <flux:label>Select Role *</flux:label>
                                        <flux:description>Choose one role for this user</flux:description>
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
                                    </flux:field>
                                </div>
                            </div>
                        </div>
                    </div>

                    <flux:callout color="blue" icon="information-circle">
                        <div class="text-sm">
                            <flux:callout.heading class="mb-2">User Creation Guidelines</flux:callout.heading>                            
                            <flux:callout.text>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>The user will receive login credentials via email</li>
                                    <li>Default role "User" will be assigned if no roles are selected</li>
                                    <li>Position determines access level within the system</li>
                                    <li>Account status can be changed later if needed</li>
                                </ul>
                            </flux:callout.text>                            
                        </div>
                    </flux:callout>

                </div>

                <!-- Form Actions -->
                <div class="px-8 py-6 bg-gray-50 dark:bg-neutral-700 border-t border-gray-200 dark:border-neutral-600 rounded-b-xl">
                    <div class="flex items-center justify-end gap-3">
                        <flux:button variant="ghost" href="{{ route('users.index') }}" class="px-6">
                            Cancel
                        </flux:button>
                        <flux:button variant="primary" type="submit" class="px-8">
                            <flux:icon.user-plus class="size-4 mr-2 inline" />
                            Create User
                        </flux:button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Loading State -->
        <div wire:loading.flex class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
            <div class="bg-white dark:bg-neutral-800 rounded-lg p-6 flex items-center gap-3">
                <flux:icon.arrow-path class="size-5 text-blue-500 animate-spin" />
                <span class="text-gray-900 dark:text-white">Creating user...</span>
            </div>
        </div>
    </div>
</div>