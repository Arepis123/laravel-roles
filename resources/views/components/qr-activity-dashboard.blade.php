@props(['asset', 'logs' => [], 'stats' => []])

<div class="space-y-4">
    <!-- QR Statistics Summary -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <flux:card class="p-3 text-center">
            <div class="text-xl font-bold text-black">{{ $stats['total_scans'] ?? 0 }}</div>
            <flux:subheading>Total Scans</flux:subheading>
        </flux:card>

        <flux:card class="p-3 text-center">
            <div class="text-xl font-bold text-green-600">{{ $stats['successful_completions'] ?? 0 }}</div>            
            <flux:subheading>Completed</flux:subheading>
        </flux:card>

        <flux:card class="p-3 text-center">
            <div class="text-xl font-bold text-red-600">{{ $stats['failed_attempts'] ?? 0 }}</div>
            <flux:subheading>Failed</flux:subheading>
        </flux:card>

        <flux:card class="p-3 text-center">
            <div class="text-xl font-bold text-blue-600">{{ $stats['unique_users'] ?? 0 }}</div>
            <flux:subheading>Unique Users</flux:subheading>
        </flux:card>
    </div>

    <!-- Recent Activity Timeline -->
    <flux:card class="p-4">
        <flux:heading size="base" class="mb-4">Recent QR Activity</flux:heading>

        @if(count($logs) > 0)
            <div class="space-y-3">
                @foreach($logs as $log)
                    <flux:card size="sm" class="p-3 hover:bg-zinc-50 dark:hover:bg-zinc-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <!-- Action Icon -->
                                <div class="flex-shrink-0">
                                    @switch($log->action)
                                        @case('generated')
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <flux:icon.plus class="size-4 text-blue-600" />
                                            </div>
                                            @break
                                        @case('regenerated')
                                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                                <flux:icon.arrow-path class="size-4 text-orange-600" />
                                            </div>
                                            @break
                                        @case('scanned')
                                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                <flux:icon.qr-code class="size-4 text-purple-600" />
                                            </div>
                                            @break
                                        @case('booking_completed')
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <flux:icon.check class="size-4 text-green-600" />
                                            </div>
                                            @break
                                        @case('scan_failed')
                                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                <flux:icon.x-mark class="size-4 text-red-600" />
                                            </div>
                                            @break
                                        @default
                                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                <flux:icon.clock class="size-4 text-gray-600" />
                                            </div>
                                    @endswitch
                                </div>

                                <!-- Activity Details -->
                                <div>
                                    <div class="font-medium text-sm">{{ $log->getFormattedAction() }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-200">
                                        {{ $log->user->name ?? 'System' }} •
                                        {{ ($log->scanned_at ?? $log->created_at)->diffForHumans() }}
                                    </div>
                                    @if($log->metadata && isset($log->metadata['error']))
                                        <div class="text-xs text-red-600 dark:text-gray-200 mt-1">
                                            Error: {{ $log->metadata['error'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Additional Info -->
                            <div class="text-right">
                                <div class="text-xs text-gray-500 dark:text-gray-200">
                                    {{ ($log->scanned_at ?? $log->created_at)->format('M j, g:i A') }}
                                </div>
                                @if($log->booking_id)
                                    <div class="text-xs text-blue-600 dark:text-blue-200">
                                        Booking #{{ $log->booking_id }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </flux:card>
                @endforeach
            </div>

            @if(count($logs) >= 10)
                <div class="text-center mt-4">
                    <flux:button variant="ghost" size="sm">
                        <flux:icon.chevron-down class="size-4" />
                        View More Activity
                    </flux:button>
                </div>
            @endif
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <flux:icon.clock class="size-6 mx-auto mb-2 text-gray-300" />
                <flux:subheading class="text-gray-900 dark:text-white">No QR activity recorded yet</flux:subheading>
                <flux:subheading class="text-gray-500 dark:text-gray-400">Activity will appear here once the QR code is used</flux:subheading>                
            </div>
        @endif
    </flux:card>

    <!-- QR Usage Pattern -->
    @if($stats['last_scan'])
        <flux:card class="p-4">
            <flux:heading size="base" class="mb-4">Usage Pattern</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="font-medium text-gray-600 dark:text-gray-300">Last Scan</div>
                    <div class="text-gray-900 dark:text-gray-200">{{ $stats['last_scan']->diffForHumans() }}</div>
                </div>
                <div>
                    <div class="font-medium text-gray-600 dark:text-gray-300">Success Rate</div>
                    <div class="text-gray-900 dark:text-gray-200">
                        @if($stats['total_scans'] > 0)
                            {{ round(($stats['successful_completions'] / $stats['total_scans']) * 100) }}%
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                <div>
                    <div class="font-medium text-gray-600 dark:text-gray-300">QR Regenerations</div>
                    <div class="text-gray-900 dark:text-gray-200">{{ $stats['generation_count'] ?? 0 }} times</div>
                </div>
            </div>
        </flux:card>
    @endif
</div>