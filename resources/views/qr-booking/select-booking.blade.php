<x-layouts.app>
    <div class="max-w-4xl mx-auto px-2 sm:px-4 py-4 sm:py-8">
        <flux:card class="p-3 sm:p-6 bg-neutral-500">
            <div class="text-center mb-4 sm:mb-6">
                <flux:heading size="xl">Select Booking to Complete</flux:heading>
                <flux:subheading class="text-sm sm:text-base">Multiple active bookings found for this asset</flux:subheading>
            </div>

            <flux:card variant="filled" class="mb-4 sm:mb-6 p-3 sm:p-4">
                <flux:heading size="base" class="mb-2">Asset Information</flux:heading>
                <div class="text-xs sm:text-sm">
                    <span class="font-medium">{{ class_basename($asset) }}:</span>
                    {{ $asset->getAssetDisplayName() }}
                </div>
            </flux:card>

            <div class="space-y-4">
                @foreach($bookings as $booking)
                    <flux:card class="p-3 sm:p-4">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-3">
                                    <flux:heading size="sm">Booking #{{ $booking->id }}</flux:heading>
                                    <flux:badge
                                        color="{{ $booking->status === 'approved' ? 'green' : ($booking->status === 'pending' ? 'yellow' : 'gray') }}"
                                        size="sm"
                                    >
                                        {{ ucfirst($booking->status) }}
                                    </flux:badge>

                                    @php
                                        $timeStatus = $booking->start_time > now() ? 'FUTURE' :
                                                     ($booking->end_time < now() ? 'PAST' : 'CURRENT');
                                        $timeColor = $timeStatus === 'CURRENT' ? 'blue' :
                                                    ($timeStatus === 'FUTURE' ? 'orange' : 'purple');
                                    @endphp

                                    <flux:badge color="{{ $timeColor }}" size="sm">
                                        {{ $timeStatus }}
                                    </flux:badge>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 text-xs sm:text-sm mb-3">
                                    <div>
                                        <span class="font-medium text-gray-600">Start:</span>
                                        <div class="font-medium break-words">{{ $booking->start_time->format('M j, Y g:i A') }}</div>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-600">End:</span>
                                        <div class="font-medium break-words">{{ $booking->end_time->format('M j, Y g:i A') }}</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <span class="font-medium text-gray-600 text-xs sm:text-sm">Purpose:</span>
                                    <div class="text-gray-900 mt-1 text-xs sm:text-sm break-words">{{ $booking->purpose }}</div>
                                </div>

                                @if($booking->hasPassengers())
                                    <div class="mb-3">
                                        <span class="font-medium text-gray-600 text-xs sm:text-sm">Passengers:</span>
                                        <div class="text-gray-900 mt-1 text-xs sm:text-sm break-words">{{ $booking->passenger_names }}</div>
                                    </div>
                                @endif

                                @if($booking->additional_booking && count($booking->additional_booking) > 0)
                                    <div class="mb-3">
                                        <span class="font-medium text-gray-600 text-xs sm:text-sm">Additional Services:</span>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($booking->additional_booking as $service)
                                                <flux:badge color="gray" size="sm">{{ ucfirst($service) }}</flux:badge>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-row lg:flex-col gap-2 w-full lg:w-auto lg:ml-6">
                                <flux:button
                                    href="{{ route('bookings.show.user', ['id' => $booking->id]) }}?auto_open_completion_modal=1"
                                    variant="primary"
                                    size="sm"
                                    class="flex-1 lg:flex-none"
                                >
                                    <flux:icon.check class="size-3 sm:size-4" />
                                    <span class="hidden sm:inline">Complete Booking</span>
                                    <span class="sm:hidden">Complete</span>
                                </flux:button>

                                <flux:button
                                    href="{{ route('bookings.show.user', ['id' => $booking->id]) }}"
                                    variant="ghost"
                                    size="sm"
                                    class="flex-1 lg:flex-none"
                                >
                                    <flux:icon.eye class="size-3 sm:size-4" />
                                    <span class="hidden sm:inline">View Details</span>
                                    <span class="sm:hidden">View</span>
                                </flux:button>
                            </div>
                        </div>

                        @if($timeStatus === 'PAST')
                            <flux:card variant="warning" class="mt-3 p-2 sm:p-3">
                                <div class="flex items-start text-xs sm:text-sm">
                                    <flux:icon.exclamation-triangle class="size-3 sm:size-4 mr-2 mt-0.5 flex-shrink-0" />
                                    <span>This booking has ended. You can still complete it within the grace period.</span>
                                </div>
                            </flux:card>
                        @elseif($timeStatus === 'FUTURE')
                            <flux:card variant="filled" class="mt-3 p-2 sm:p-3">
                                <div class="flex items-start text-xs sm:text-sm">
                                    <flux:icon.clock class="size-3 sm:size-4 mr-2 mt-0.5 flex-shrink-0" />
                                    <span>This booking starts in {{ $booking->start_time->diffForHumans() }}. You can complete it early if needed.</span>
                                </div>
                            </flux:card>
                        @else
                            <flux:card variant="success" class="mt-3 p-2 sm:p-3">
                                <div class="flex items-start text-xs sm:text-sm">
                                    <flux:icon.play class="size-3 sm:size-4 mr-2 mt-0.5 flex-shrink-0" />
                                    <span>This booking is currently active.</span>
                                </div>
                            </flux:card>
                        @endif
                    </flux:card>
                @endforeach
            </div>

            <div class="mt-6 sm:mt-8 text-center">
                <flux:button variant="ghost" href="{{ route('dashboard') }}" size="sm">
                    <flux:icon.arrow-left class="size-3 sm:size-4" />
                    Cancel
                </flux:button>
            </div>
        </flux:card>
    </div>
</x-layouts.app>