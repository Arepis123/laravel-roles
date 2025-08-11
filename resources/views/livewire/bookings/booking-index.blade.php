<div>
    <div class="relative mb-6 w-full">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Bookings') }}</h1>
                <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('Manage all bookings here') }}</p>
            </div>
            <!-- Quick Stats -->
            <div class="flex gap-4 text-sm">
                <div class="text-center">
                    <div class="font-semibold text-yellow-600">{{ $bookings->where('status', 'pending')->count() }}</div>
                    <div class="text-gray-500">Pending</div>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-sky-600">{{ $bookings->where('status', 'approved')->count() }}</div>
                    <div class="text-gray-500">Approved</div>
                </div>
                <div class="text-center">
                    <div class="font-semibold text-green-600">{{ $bookings->where('status', 'done')->count() }}</div>
                    <div class="text-gray-500">Completed</div>
                </div>
            </div>
        </div>
        <flux:separator variant="subtle" class="mt-4" />        
    </div>
   
    @session('success')
        <div class="mb-4">
            <flux:callout variant="success" icon="check-circle" heading="{{ $value }}" />
        </div>
    @endsession    

    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-4">
        @can('book.create')
        <flux:button variant="primary" href="{{ route('bookings.create') }}">
            <flux:icon name="plus" class="w-4 h-4 mr-2" />
            New Booking
        </flux:button>
        @else
        <div></div>
        @endcan

        <!-- Filter and Search -->
        <div class="flex gap-2">
            <flux:select wire:model.live="statusFilter" placeholder="All Status" class="min-w-32">
                <flux:select.option value="">All Status</flux:select.option>
                <flux:select.option value="pending">Pending</flux:select.option>
                <flux:select.option value="approved">Approved</flux:select.option>
                <flux:select.option value="rejected">Rejected</flux:select.option>
                <flux:select.option value="cancelled">Cancelled</flux:select.option>
                <flux:select.option value="done">Done</flux:select.option>
            </flux:select>
            
            <flux:select wire:model.live="assetTypeFilter" placeholder="All Assets" class="min-w-32">
                <flux:select.option value="">All Assets</flux:select.option>
                <flux:select.option value="App\Models\Vehicle">Vehicle</flux:select.option>
                <flux:select.option value="App\Models\MeetingRoom">Meeting Room</flux:select.option>
                <flux:select.option value="App\Models\ItAsset">IT Asset</flux:select.option>
            </flux:select>

            <flux:input 
                wire:model.live.debounce.500ms="search" 
                placeholder="Search bookings..." 
                icon="magnifying-glass"
                class="min-w-48"
            />

            <flux:tooltip content="Reset Sort">
                <flux:button wire:click="resetFilters" icon="arrow-path" icon:variant="outline" />
            </flux:tooltip>            
        </div>
    </div>

    <div class="border border-gray-200 rounded-xl shadow-2xs overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            <div class="flex items-center gap-1">
                                #
                                <button wire:click="sortBy('id')" class="hover:bg-gray-100 p-1 rounded">
                                    <flux:icon name="chevron-up-down" class="w-3 h-3" />
                                </button>
                            </div>
                        </th>  
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">Asset Details</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            <div class="flex items-center gap-1">
                                Duration
                                <button wire:click="sortBy('start_time')" class="hover:bg-gray-100 p-1 rounded">
                                    <flux:icon name="chevron-up-down" class="w-3 h-3" />
                                </button>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">Booking Info</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">
                            <div class="flex items-center gap-1">
                                Status
                                <button wire:click="sortBy('status')" class="hover:bg-gray-100 p-1 rounded">
                                    <flux:icon name="chevron-up-down" class="w-3 h-3" />
                                </button>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-800 dark:divide-neutral-700">
                    @forelse ($bookings as $booking)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-neutral-200">
                                    #{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>
                           
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-2">                                    
                                    <div class="min-w-0">
                                        <flux:badge size="sm" icon="{{ $booking->asset_type_label == 'Vehicle' ? 'truck' : ($booking->asset_type_label == 'Meeting Room' ? 'building-office' : 'computer-desktop') }}" color="{{ $booking->asset_type_label == 'Vehicle' ? 'green' : ($booking->asset_type_label == 'Meeting Room' ? 'blue' : 'fuchsia') }}">
                                            {{ $booking->asset_type_label }}
                                        </flux:badge>
                                        <div class="text-sm font-medium text-gray-900 dark:text-neutral-200 mt-1">
                                            @if ($booking->asset_type_label == 'Vehicle')
                                                {{ $booking->asset?->model ?? 'N/A' }}
                                                @if($booking->asset?->plate_number)
                                                    <span class="text-xs text-gray-500 block">{{ $booking->asset->plate_number }}
                                                @endif
                                            @else
                                                {{ $booking->asset?->name ?? 'N/A' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-neutral-200">
                                    <div class="font-medium">{{ \Carbon\Carbon::parse($booking->start_time)->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                        <flux:icon name="clock" class="w-3 h-3" />
                                        <flux:text class="text-xs">
                                            {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                        </flux:text>

                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        @php
                                            $start = \Carbon\Carbon::parse($booking->start_time);
                                            $end = \Carbon\Carbon::parse($booking->end_time);

                                            $totalMinutes = $start->diffInMinutes($end);
                                            $totalHours = floor($totalMinutes / 60);
                                            $days = floor($totalHours / 24);

                                            if ($totalMinutes < 60) {
                                                $durationText = $totalMinutes . 'm';
                                            } elseif ($totalHours < 24) {
                                                $hours = $totalHours;
                                                $minutes = $totalMinutes % 60;
                                                $durationText = $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
                                            } else {
                                                $hours = $totalHours % 24;
                                                $durationText = $days . 'd' . ($hours > 0 ? ' ' . $hours . 'h' : '');
                                            }
                                        @endphp

                                        {{ $durationText }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <flux:avatar size="xs" color="auto" name="{{ $booking->user ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $booking->user->name) : 'N/A' }}" />
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-gray-900 dark:text-neutral-200 truncate">
                                            {{ $booking->user ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $booking->user->name) : 'No user found' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $booking->user?->email ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400 flex items-center gap-1">
                                    <flux:icon name="calendar" class="w-3 h-3" />
                                    Booked {{ \Carbon\Carbon::parse($booking->created_at)->diffForHumans() }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    @if ($booking->status == 'pending')
                                        <flux:badge color="yellow" class="w-fit">
                                            <flux:icon name="clock" class="w-3 h-3 mr-1" />
                                            {{ ucwords($booking->status) }}
                                        </flux:badge>
                                    @elseif ($booking->status == 'approved')     
                                        <flux:badge color="sky" class="w-fit">
                                            <flux:icon name="check" class="w-3 h-3 mr-1" />
                                            {{ ucwords($booking->status) }}
                                        </flux:badge>  
                                    @elseif ($booking->status == 'rejected')     
                                        <flux:badge color="red" class="w-fit">
                                            <flux:icon name="x-mark" class="w-3 h-3 mr-1" />
                                            {{ ucwords($booking->status) }}
                                        </flux:badge>                                                                                                
                                    @elseif ($booking->status == 'cancelled')
                                        <flux:badge color="zinc" class="w-fit">
                                            <flux:icon name="arrow-turn-down-left" class="w-3 h-3 mr-1" />
                                            {{ ucwords($booking->status) }}
                                        </flux:badge> 
                                    @elseif ($booking->status == 'done')
                                        <flux:badge color="green" class="w-fit">
                                            <flux:icon name="check-circle" class="w-3 h-3 mr-1" />
                                            {{ ucwords($booking->status) }}
                                        </flux:badge>
                                    @endif
                                    
                                    <!-- Show urgency indicator for upcoming bookings -->
                                    @if($booking->status == 'approved' && \Carbon\Carbon::parse($booking->start_time)->isToday())
                                        <flux:badge color="orange" size="sm" class="w-fit text-xs">Today</flux:badge>
                                    @elseif($booking->status == 'approved' && \Carbon\Carbon::parse($booking->start_time)->isTomorrow())
                                        <flux:badge color="blue" size="sm" class="w-fit text-xs">Tomorrow</flux:badge>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">                              
                                <flux:button.group>
                                    <flux:button size="sm" href="{{ route('bookings.show', $booking->id) }}" variant="ghost">
                                        <flux:icon name="eye" class="w-4 h-4" />
                                    </flux:button>
                                    @can('book.edit')
                                    <flux:dropdown>
                                        <flux:button icon="chevron-down" size="sm" variant="ghost"></flux:button>                                           
                                        <flux:menu>
                                            <flux:menu.submenu heading="Change status" icon="cog-6-tooth">
                                                <flux:menu.radio.group>
                                                    <flux:menu.radio :checked="$booking->status == 'pending'" wire:click="changeStatus('pending', {{ $booking->id }})">
                                                        <flux:icon name="clock" class="w-4 h-4 mr-2 text-yellow-500" />
                                                        Pending
                                                    </flux:menu.radio>
                                                    <flux:menu.radio :checked="$booking->status == 'approved'" wire:click="changeStatus('approved', {{ $booking->id }})">
                                                        <flux:icon name="check" class="w-4 h-4 mr-2 text-sky-500" />
                                                        Approve
                                                    </flux:menu.radio>   
                                                    <flux:menu.radio :checked="$booking->status == 'rejected'" wire:click="changeStatus('rejected', {{ $booking->id }})">
                                                        <flux:icon name="x-mark" class="w-4 h-4 mr-2 text-red-500" />
                                                        Reject
                                                    </flux:menu.radio>                                                                                                     
                                                    <flux:menu.radio :checked="$booking->status == 'cancelled'" wire:click="changeStatus('cancelled', {{ $booking->id }})">
                                                        <flux:icon name="arrow-turn-down-left" class="w-4 h-4 mr-2 text-zinc-500" />
                                                        Cancel
                                                    </flux:menu.radio>
                                                    <flux:menu.radio :checked="$booking->status == 'done'" wire:click="changeStatus('done', {{ $booking->id }})">
                                                        <flux:icon name="check-circle" class="w-4 h-4 mr-2 text-green-500" />
                                                        Mark Done
                                                    </flux:menu.radio>
                                                </flux:menu.radio.group>
                                            </flux:menu.submenu>
                                            <flux:menu.separator />
                                            <flux:menu.item icon="pencil" href="{{ route('bookings.edit', $booking->id) }}">
                                                Edit Details
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                    @endcan                                                                                                         
                                </flux:button.group>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="calendar-days" class="w-8 h-8 text-gray-400" />
                                    <div class="text-gray-500">No bookings found</div>
                                    @can('book.create')
                                    <flux:button size="sm" href="{{ route('bookings.create') }}" variant="ghost">
                                        Create first booking
                                    </flux:button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($bookings->hasPages())
        <div class="px-6 py-3 border-t border-gray-200 dark:border-neutral-700">
            {{ $bookings->links() }}
        </div>
        @endif        
       
    </div>
</div>