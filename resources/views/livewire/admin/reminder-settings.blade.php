<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Booking Reminder Settings</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">Manage automated email reminders for incomplete bookings</p>
        <flux:separator variant="subtle" class="my-4" />
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6">
        <!-- Total Sent Card -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total Sent</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_sent'] }}</flux:text>
                </div>
            </div>
        </flux:card>

        <!-- Total Failed Card -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Failed</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_failed'] }}</flux:text>
                </div>
            </div>
        </flux:card>

        <!-- Last 24h Card -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Last 24 Hours</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['last_24h'] }}</flux:text>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div x-data="{ visible: true }" x-show="visible" x-collapse class="mb-4">
            <div x-show="visible" x-transition>
                <flux:callout icon="check-circle" variant="success" heading="{{ session('success') }}">
                    <x-slot name="controls">
                        <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                    </x-slot>
                </flux:callout>
            </div>
        </div>
    @endif

    <!-- Settings Form -->
    <flux:card class="mb-6 dark:bg-zinc-900">
        <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
            <flux:heading>Reminder Configuration</flux:heading>
            <flux:text class="text-gray-600 dark:text-gray-400">Configure how and when reminder emails are sent</flux:text>
        </div>
        <form wire:submit.prevent="save" class="space-y-6 p-6">

            <!-- Enable/Disable -->
            <flux:field variant="inline">
                <flux:checkbox wire:model="enabled" />
                <flux:label>Enable Automatic Reminders</flux:label>
                <flux:description>When enabled, the system will automatically send reminder emails to users who haven't completed their bookings</flux:description>
            </flux:field>

            <!-- Grid Layout for Input Fields -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Hours After End -->
                <flux:field>
                    <flux:label>Hours After Booking End Time</flux:label>
                    <flux:input type="number" wire:model="hours_after_end" min="1" max="72" placeholder="1" />
                    <flux:description>Send reminder this many hours after the booking end time (1-72 hours)</flux:description>
                    <flux:error name="hours_after_end" />
                </flux:field>

                <!-- Frequency -->
                <flux:field>
                    <flux:label>Check Frequency</flux:label>
                    <flux:select variant="listbox" wire:model="frequency">
                        @foreach($frequencies as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:description>How often the system should check for incomplete bookings</flux:description>
                    <flux:error name="frequency" />
                </flux:field>

                <!-- Max Reminders -->
                <flux:field>
                    <flux:label>Maximum Reminders Per Booking</flux:label>
                    <flux:input type="number" wire:model="max_reminders" min="1" max="10" placeholder="3" />
                    <flux:description>Stop sending reminders after this many attempts (1-10 reminders)</flux:description>
                    <flux:error name="max_reminders" />
                </flux:field>
            </div>

            <!-- Send to Passengers -->
            <flux:field variant="inline" class="md:col-span-1 flex items-start">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <flux:checkbox wire:model="send_to_passengers" />
                        <flux:label>Send Reminders to Passengers</flux:label>
                    </div>
                    <flux:description>Also send reminder emails to passengers in vehicle bookings (not just the booking owner)</flux:description>
                </div>
            </flux:field>

            <!-- Skip Weekends -->
            <flux:field variant="inline" class="md:col-span-1 flex items-start">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <flux:checkbox wire:model="skip_weekends" />
                        <flux:label>Skip Weekends</flux:label>
                    </div>
                    <flux:description>Don't send reminder emails on Saturdays and Sundays</flux:description>
                </div>
            </flux:field>            

            <!-- Custom Message -->
            <flux:field>
                <flux:label>Custom Message (Optional)</flux:label>
                <flux:textarea wire:model="custom_message" rows="3" placeholder="Add a custom message to include in reminder emails..." />
                <flux:description>This message will be displayed in the reminder email (max 500 characters)</flux:description>
                <flux:error name="custom_message" />
            </flux:field>

            <!-- Excluded Asset Types -->
            <flux:field>
                <flux:label>Exclude Asset Types (Optional)</flux:label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 mt-2">
                    @foreach($assetTypes as $value => $label)
                        <flux:field variant="inline">
                            <flux:checkbox wire:model="excluded_asset_types" value="{{ $value }}" />
                            <flux:label>{{ $label }}</flux:label>
                        </flux:field>
                    @endforeach
                </div>
                <flux:description>Select asset types that should NOT receive reminder emails</flux:description>
                <flux:error name="excluded_asset_types" />
            </flux:field>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <flux:button type="submit" variant="primary" class="w-full sm:w-auto">
                    Save Settings
                </flux:button>
                <flux:button type="button" wire:click="runTest" variant="filled" class="w-full sm:w-auto">
                    Run Test Now
                </flux:button>
                <flux:spacer class="hidden sm:block" />
                @if($stats['total_sent'] > 0 || $stats['total_failed'] > 0)
                    <flux:button type="button" wire:click="clearLogs" wire:confirm="Are you sure you want to clear all reminder logs?" variant="danger" class="w-full sm:w-auto">
                        Clear All Logs
                    </flux:button>
                @endif
            </div>
        </form>
    </flux:card>

    <!-- Reminder Logs -->
    <flux:card class="dark:bg-zinc-900">
        <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
            <flux:heading>Reminder Logs</flux:heading>
            <flux:text class="text-gray-600 dark:text-gray-400">History of all sent reminder emails</flux:text>
        </div>
        <div class="p-6 pt-0">
            <div class="mt-6">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date & Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Booking
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Reminder #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Error
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $log->created_at->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-500 block">{{ $log->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Booking #{{ $log->booking_id }}</span>
                                        @if($log->booking && $log->booking->asset)
                                            <span class="text-xs text-gray-500 block">{{ class_basename($log->booking->asset_type) }}: {{ $log->booking->asset->name ?? $log->booking->asset->model ?? 'N/A' }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $log->user->name ?? 'Unknown' }}</span>
                                    <span class="text-xs text-gray-500 block">{{ $log->user->email ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <flux:badge size="sm" color="zinc">{{ $log->reminder_count }}</flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->status === 'sent')
                                        <flux:badge size="sm" color="lime" icon="check-circle">Sent</flux:badge>
                                    @elseif($log->status === 'failed')
                                        <flux:badge size="sm" color="red" icon="x-circle">Failed</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="amber">{{ ucfirst($log->status) }}</flux:badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->error_message)
                                        <span class="text-xs text-red-600 dark:text-red-400">{{ Str::limit($log->error_message, 50) }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">No reminder logs yet</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Logs will appear here once reminders are sent</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            @endif
            </div>
        </div>
    </flux:card>

    <!-- Test Result Modal -->
    <flux:modal wire:model="showTestModal" class="min-w-[32rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Test Reminder Command</flux:heading>
                <flux:subheading>Command execution results</flux:subheading>
            </div>

            <div class="bg-gray-50 dark:bg-zinc-800 p-4 rounded-lg border border-gray-200 dark:border-zinc-700">
                <pre class="text-xs text-gray-900 dark:text-white whitespace-pre-wrap font-mono">{{ $testResult }}</pre>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button wire:click="closeTestModal" variant="ghost">Close</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
