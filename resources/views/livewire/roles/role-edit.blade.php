<div>
    <!-- Page Header -->
    <div class="relative mb-6 w-full">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <flux:icon name="pencil" class="w-6 h-6 text-blue-500" />
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Edit Role') }}</h1>
                </div>
                <p class="text-gray-600 dark:text-gray-400">{{ __('Modify role name and permissions') }}</p>
            </div>
            
            <!-- Progress Indicator -->
            <div class="flex gap-4 text-sm">
                <div class="text-center bg-white dark:bg-neutral-800 rounded-lg p-3 border border-gray-200 dark:border-neutral-700">
                    <div class="font-semibold text-blue-600">{{ count($permissions ?? []) }}/{{ count($allPermissions) }}</div>
                    <div class="text-gray-500 text-xs">Selected</div>
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
    </div>

    <form wire:submit="submit" class="space-y-6">
        <!-- Role Information Card -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-6 py-3 bg-gray-50 dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-700">
                <flux:icon name="identification" class="w-5 h-5 inline text-gray-500 me-1" />
                <div class="text-left text-xs font-medium text-gray-500 uppercase inline">Role Information</div>                
            </div>
            <div class="p-6">
                <div class="max-w-md">
                    <flux:input wire:model="name" type="text" label="Role Name" placeholder="Enter role name..." required />
                </div>
            </div>
        </div>

        <!-- Permissions Management Card -->
        <div class="bg-white dark:bg-neutral-800 rounded-lg border border-gray-200 dark:border-neutral-700 overflow-hidden">
            <div class="px-6 py-3 bg-gray-50 dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-700">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">                   
                    <div>
                        <flux:icon name="key" class="w-5 h-5 inline text-gray-500 me-1" />
                        <div class="text-xs font-medium text-gray-500 uppercase inline">
                            Assigned Permissions
                        </div> 
                    </div>
                    <div class="flex gap-2">
                        <flux:button size="sm" variant="ghost" type="button" onclick="selectAllPermissions()">
                            Select All
                        </flux:button>
                        <flux:button size="sm" variant="ghost" type="button" onclick="deselectAllPermissions()">
                            Deselect All
                        </flux:button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <!-- Desktop Grid View -->
                <div class="hidden sm:block">
                    <flux:checkbox.group wire:model="permissions" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($allPermissions as $permission)
                            <div class="p-3 rounded-lg border border-gray-200 dark:border-neutral-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors group-hover:bg-blue-50 dark:group-hover:bg-blue-900/20">
                                <flux:checkbox 
                                    label="{{ $permission->name}}" 
                                    value="{{ $permission->name}}" 
                                />
                            </div>
                        @endforeach
                    </flux:checkbox.group>
                </div>

                <!-- Mobile List View -->
                <div class="sm:hidden grid sm:grid-cols-2 gap-3">
                    <flux:checkbox.group wire:model="permissions" class="space-y-3">
                        @foreach ($allPermissions as $permission)
                            <div class="p-3 rounded-lg border border-gray-200 dark:border-neutral-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors group-hover:bg-blue-50 dark:group-hover:bg-blue-900/20"">
                                <flux:checkbox 
                                    label="{{ $permission->name}}" 
                                    value="{{ $permission->name}}"                                     
                                />
                            </div>
                        @endforeach
                    </flux:checkbox.group>
                </div>

                @if(count($allPermissions) === 0)
                    <div class="text-center py-8">
                        <flux:icon name="key" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                        <p class="text-gray-500 dark:text-gray-400">No permissions available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
            <flux:button variant="ghost" href="{{ route('roles.index') }}" class="w-full sm:w-auto">
                Cancel
            </flux:button>
            <flux:button variant="primary" type="submit" class="w-full sm:w-auto">
                <flux:icon name="check" class="w-4 h-4 mr-1" />
                Save Changes
            </flux:button>
        </div>
    </form>

    <!-- JavaScript for Select/Deselect All -->
    <script>
        function selectAllPermissions() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][wire\\:model="permissions"]');
            checkboxes.forEach(checkbox => {
                if (!checkbox.checked) {
                    checkbox.click();
                }
            });
        }
        
        function deselectAllPermissions() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][wire\\:model="permissions"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    checkbox.click();
                }
            });
        }
    </script>
</div>