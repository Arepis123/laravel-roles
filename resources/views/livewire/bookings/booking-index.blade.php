<div x-data="{}" x-init="
    @if($highlightId)
        setTimeout(() => {
            const element = document.getElementById('booking-row-{{ $highlightId }}') || document.getElementById('booking-card-{{ $highlightId }}');
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Add blinking animation
                let blinkCount = 0;
                const blinkInterval = setInterval(() => {
                    if (blinkCount % 2 === 0) {
                        element.classList.remove('bg-gray-100', 'dark:bg-zinc-700', 'border-zinc-400', 'dark:border-zinc-500');
                    } else {
                        element.classList.add('bg-gray-100', 'dark:bg-zinc-700', 'border-zinc-400', 'dark:border-zinc-500');
                    }
                    blinkCount++;

                    // Stop after 2 blinks (1 complete cycle at 700ms interval)
                    if (blinkCount >= 2) {
                        clearInterval(blinkInterval);
                        element.classList.remove('bg-gray-100', 'dark:bg-zinc-700', 'border-zinc-400', 'dark:border-zinc-500');
                    }
                }, 700);
            }
        }, 100);
    @endif
">
    <div class="relative mb-6 w-full">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4">
            <!-- Header Section -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Bookings') }}</h1>
                <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('Manage all bookings here') }}</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="flex gap-4 text-sm lg:gap-6">
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
   
    @if (session()->has('success'))
        <div class="mb-4">
            <div x-data="{ visible: true }" x-show="visible" x-collapse>
                <div x-show="visible" x-transition>
                    <flux:callout icon="check-circle" variant="success" heading="{{ session('success') }}">                  
                        <x-slot name="controls">
                            <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                        </x-slot>
                    </flux:callout>
                </div>
            </div>  
        </div>
    @endif
    
    @if (session()->has('error'))
        <div x-data="{ visible: true }" x-show="visible" x-collapse>
            <div x-show="visible" x-transition>
                <flux:callout icon="x-circle" variant="danger" heading="{{ session('error') }}">                  
                    <x-slot name="controls">
                        <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                    </x-slot>
                </flux:callout>
            </div>
        </div>   
    @endif    

    <!-- Action Bar -->
    <div class="flex flex-col md:flex-row md:justify-end gap-2 mb-4">

        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 w-full md:w-auto">
            <flux:select wire:model.live="statusFilter" placeholder="All Status" class="min-w-32 w-full sm:w-auto">
                <flux:select.option value="">All Status</flux:select.option>
                <flux:select.option value="pending">Pending</flux:select.option>
                <flux:select.option value="approved">Approved</flux:select.option>
                <flux:select.option value="rejected">Rejected</flux:select.option>
                <flux:select.option value="cancelled">Cancelled</flux:select.option>
                <flux:select.option value="done">Done</flux:select.option>
            </flux:select>
            
            <flux:select wire:model.live="assetTypeFilter" placeholder="All Assets" class="min-w-32 w-full sm:w-auto">
                <flux:select.option value="">All Assets</flux:select.option>
                <flux:select.option value="App\Models\Vehicle">Vehicle</flux:select.option>
                <flux:select.option value="App\Models\MeetingRoom">Meeting Room</flux:select.option>
                <flux:select.option value="App\Models\ItAsset">IT Asset</flux:select.option>
            </flux:select>

            <flux:input 
                wire:model.live.debounce.500ms="search" 
                placeholder="Search bookings..." 
                icon="magnifying-glass"
                class="min-w-48 w-full sm:w-auto"
            />

            <!-- Reset Button - Icon on desktop, Text on mobile -->
            <flux:tooltip content="Reset Filters" class="hidden sm:block">
                <flux:button 
                    wire:click="resetFilters" 
                    icon="arrow-path"
                    class="hidden"
                />
            </flux:tooltip>
            
            <!-- Reset Button for Mobile - Full width with text -->
            <flux:button 
                wire:click="resetFilters" 
                icon="arrow-path"
                class="sm:hidden w-full" 
                variant="filled"
            >
                Reset Filters
            </flux:button>            
        </div>
    </div>

    <!-- Desktop Table View (hidden on mobile) -->
    <div class="hidden md:block sm:block bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th wire:click="sortBy('id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>#</span>
                                @if($sortField === 'id')
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Asset Details</th>
                        <th wire:click="sortBy('start_time')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
                            <div class="flex items-center space-x-1">
                                <span>Duration</span>
                                @if($sortField === 'start_time')
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Booking Info</th>
                        <th wire:click="sortBy('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 select-none">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse ($bookings as $booking)
                        <tr id="booking-row-{{ $booking->id }}"
                            class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors duration-300 {{ $highlightId == $booking->id ? 'bg-gray-100 dark:bg-zinc-700' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                #{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge size="sm" icon="{{ $booking->asset_type_label == 'Vehicle' ? 'car' : ($booking->asset_type_label == 'Meeting Room' ? 'building-office' : 'computer-desktop') }}" color="{{ $booking->asset_type_label == 'Vehicle' ? 'green' : ($booking->asset_type_label == 'Meeting Room' ? 'blue' : 'fuchsia') }}">
                                    {{ $booking->asset_type_label }}
                                </flux:badge>
                                <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                                    @if ($booking->asset_type_label == 'Vehicle')
                                        {{ $booking->asset?->model ?? 'N/A' }}
                                        @if($booking->asset?->plate_number)
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $booking->asset->plate_number }}</span>
                                        @endif
                                    @elseif ($booking->asset_type_label == 'IT Asset')
                                        {{ $booking->asset?->name ?? 'N/A' }}
                                        @if($booking->asset?->asset_tag)
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $booking->asset->asset_tag }}</span>
                                        @endif
                                    @else
                                        {{ $booking->asset?->name ?? 'N/A' }}
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    @php
                                        $start = \Carbon\Carbon::parse($booking->start_time);
                                        $end = \Carbon\Carbon::parse($booking->end_time);
                                        $totalMinutes = $start->diffInMinutes($end);
                                        $totalHours = floor($totalMinutes / 60);
                                        $days = (int) $start->diffInDays($end);
                                        
                                        // Enhanced date display logic
                                        if ($days > 1) {
                                            // Multi-day booking
                                            if ($start->month === $end->month) {
                                                // Same month: "Aug 14 - 17, 2025"
                                                $dateDisplay = $start->format('M j') . ' - ' . $end->format('j, Y');
                                            } else {
                                                // Different months: "Aug 29 - Sept 2, 2025"
                                                $dateDisplay = $start->format('M j') . ' - ' . $end->format('M j, Y');
                                            }
                                        } else {
                                            // Same day booking
                                            $dateDisplay = $start->format('M d, Y');
                                        }
                                        
                                        // Duration calculation
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

                                    <div class="font-medium">{{ $dateDisplay }}</div>
                                    @if($days === 0)
                                        {{-- Single day booking - show time range --}}
                                        <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                            <flux:icon name="clock" class="w-3 h-3" />
                                            <flux:text class="text-xs">
                                                {{ $start->format('h:i A') }} - {{ $end->format('h:i A') }}
                                            </flux:text>
                                        </div>
                                    @else
                                        {{-- Multi-day booking - show start and end times --}}
                                        <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                            <flux:icon name="clock" class="w-3 h-3" />
                                            <flux:text class="text-xs">
                                                {{ $start->format('h:i A') }} → {{ $end->format('h:i A') }}
                                            </flux:text>
                                        </div>
                                    @endif
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $durationText }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <flux:avatar size="xs" color="auto" name="{{ $booking->user ? preg_replace('/\s+(BIN|BINTI|BT)\b.*/i', '', $booking->user->name) : 'N/A' }}" />
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $booking->user ? preg_replace('/\s+(BIN|BINTI|BT)\b.*/i', '', $booking->user->name) : 'No user found' }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($booking->created_at)->diffForHumans() }}
                                        </div>
                                    </div>
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
                                            <flux:icon name="square-check" class="w-3 h-3 mr-1" />
                                            {{ ucwords($booking->status) }}
                                        </flux:badge>  
                                    @elseif ($booking->status == 'rejected')     
                                        <flux:badge color="red" class="w-fit">
                                            <flux:icon name="circle-x" class="w-3 h-3 mr-1" />
                                            {{ ucwords($booking->status) }}
                                        </flux:badge>                                                                                                
                                    @elseif ($booking->status == 'cancelled')
                                        <flux:badge color="zinc" class="w-fit">
                                            <flux:icon name="ban" class="w-3 h-3 mr-1" />
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

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    @can('book.create')
                                    <flux:button size="xs" href="{{ route('bookings.show', ['id' => $booking->id, 'page' => $bookings->currentPage()]) }}" variant="ghost">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </flux:button>
                                    @endcan
                                    @can('book.edit')
                                    <flux:dropdown>
                                        <flux:button icon="chevron-down" size="xs" variant="ghost"></flux:button>                                           
                                        <flux:menu>
                                            <flux:menu.submenu heading="Change status" icon="cog-6-tooth">
                                                <flux:menu.radio.group>
                                                    <flux:menu.radio :checked="$booking->status == 'pending'" wire:click="changeStatus('pending', {{ $booking->id }})">
                                                        {{-- <flux:icon name="clock" class="w-4 h-4 mr-2 text-yellow-500" /> --}}
                                                        Pending
                                                    </flux:menu.radio>
                                                    <flux:menu.radio :checked="$booking->status == 'approved'" wire:click="changeStatus('approved', {{ $booking->id }})">                                                     
                                                        Approve
                                                    </flux:menu.radio>   
                                                    <flux:menu.radio :checked="$booking->status == 'rejected'" wire:click="changeStatus('rejected', {{ $booking->id }})">                                                    
                                                        Reject
                                                    </flux:menu.radio>                                                                                                     
                                                    <flux:menu.radio :checked="$booking->status == 'cancelled'" wire:click="changeStatus('cancelled', {{ $booking->id }})">                                                     
                                                        Cancel
                                                    </flux:menu.radio>
                                                    <flux:menu.radio :checked="$booking->status == 'done'" wire:click="changeStatus('done', {{ $booking->id }})">                                                   
                                                        Mark Done
                                                    </flux:menu.radio>
                                                </flux:menu.radio.group>
                                            </flux:menu.submenu>
                                            <flux:menu.separator />
                                            <flux:menu.item icon="pencil" href="{{ route('bookings.edit', ['booking' => $booking->id, 'page' => $bookings->currentPage()]) }}">
                                                Edit Details
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <h3 class="font-medium text-gray-900 dark:text-white mb-1">No bookings</h3>
                                    <p class="text-gray-500 dark:text-gray-400">Get started by creating your first booking.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($bookings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700">
            {{ $bookings->links() }}
        </div>
        @endif        
       
    </div>

    <!-- Mobile Card View (hidden on desktop) -->
    <div class="sm:hidden md:hidden space-y-4">
        @forelse ($bookings as $booking)
            <div id="booking-card-{{ $booking->id }}"
                 class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-4 shadow-sm transition-colors duration-300 {{ $highlightId == $booking->id ? 'bg-gray-100 dark:bg-zinc-700 border-zinc-400 dark:border-zinc-500' : '' }}">
                <!-- Card Header -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <flux:badge size="sm" icon="{{ $booking->asset_type_label == 'Vehicle' ? 'car' : ($booking->asset_type_label == 'Meeting Room' ? 'building-office' : 'computer-desktop') }}" color="{{ $booking->asset_type_label == 'Vehicle' ? 'green' : ($booking->asset_type_label == 'Meeting Room' ? 'blue' : 'fuchsia') }}">
                            {{ $booking->asset_type_label }}
                        </flux:badge>
                        <span class="text-xs text-gray-500 dark:text-gray-400">#{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    
                    <!-- Status Badge -->
                    @if ($booking->status == 'pending')
                        <flux:badge color="yellow" size="sm">
                            <flux:icon name="clock" class="w-3 h-3 mr-1" />
                            {{ ucwords($booking->status) }}
                        </flux:badge>
                    @elseif ($booking->status == 'approved')     
                        <flux:badge color="sky" size="sm">
                            <flux:icon name="square-check" class="w-3 h-3 mr-1" />
                            {{ ucwords($booking->status) }}
                        </flux:badge>  
                    @elseif ($booking->status == 'rejected')     
                        <flux:badge color="red" size="sm">
                            <flux:icon name="circle-x" class="w-3 h-3 mr-1" />
                            {{ ucwords($booking->status) }}
                        </flux:badge>                                                                                                
                    @elseif ($booking->status == 'cancelled')
                        <flux:badge color="zinc" size="sm">
                            <flux:icon name="ban" class="w-3 h-3 mr-1" />
                            {{ ucwords($booking->status) }}
                        </flux:badge> 
                    @elseif ($booking->status == 'done')
                        <flux:badge color="green" size="sm">
                            <flux:icon name="check-circle" class="w-3 h-3 mr-1" />
                            {{ ucwords($booking->status) }}
                        </flux:badge>
                    @endif
                </div>

                <!-- Asset Details -->
                <div class="mb-3">
                    <h3 class="font-semibold text-gray-900 dark:text-white">
                        @if ($booking->asset_type_label == 'Vehicle')
                            {{ $booking->asset?->model ?? 'N/A' }}
                            @if($booking->asset?->plate_number)
                                <span class="text-sm text-gray-500 font-normal">({{ $booking->asset->plate_number }})</span>
                            @endif
                        @elseif ($booking->asset_type_label == 'IT Asset')
                            {{ $booking->asset?->name ?? 'N/A' }}
                            @if($booking->asset?->asset_tag)
                                <span class="text-sm text-gray-500 font-normal">({{ $booking->asset->asset_tag }})</span>
                            @endif
                        @else
                            {{ $booking->asset?->name ?? 'N/A' }}
                        @endif
                    </h3>
                </div>

                <!-- Date & Duration Info -->
                <div class="mb-3 text-sm">
                    @php
                        $start = \Carbon\Carbon::parse($booking->start_time);
                        $end = \Carbon\Carbon::parse($booking->end_time);
                        $totalMinutes = $start->diffInMinutes($end);
                        $totalHours = floor($totalMinutes / 60);
                        $days = (int) $start->diffInDays($end);
                        
                        // Enhanced date display logic
                        if ($days > 1) {
                            // Multi-day booking
                            if ($start->month === $end->month) {
                                // Same month: "Aug 14 - 17, 2025"
                                $dateDisplay = $start->format('M j') . ' - ' . $end->format('j, Y');
                            } else {
                                // Different months: "Aug 29 - Sept 2, 2025"
                                $dateDisplay = $start->format('M j') . ' - ' . $end->format('M j, Y');
                            }
                        } else {
                            // Same day booking
                            $dateDisplay = $start->format('M d, Y');
                        }
                        
                        // Duration calculation
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

                    <div class="flex items-center gap-1 text-gray-900 dark:text-white mb-1">
                        <flux:icon name="calendar" class="w-4 h-4 text-gray-500" />
                        <span class="font-medium">{{ $dateDisplay }}</span>
                    </div>
                    
                    @if($days === 0)
                        {{-- Single day booking - show time range --}}
                        <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400 ml-5">                           
                            <span class="text-sm">{{ $start->format('h:i A') }} - {{ $end->format('h:i A') }} ({{ $durationText }})</span>
                        </div>
                    @else
                        {{-- Multi-day booking - show start and end times --}}
                        <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400 ml-5">                            
                            <span class="text-sm">{{ $start->format('h:i A') }} → {{ $end->format('h:i A') }} ({{ $durationText }})</span>
                        </div>
                    @endif
                </div>

                <!-- Booked by info -->
                <div class="flex items-center gap-2 mb-4">
                    <flux:avatar size="xs" color="auto" name="{{ $booking->user ? preg_replace('/\s+(BIN|BINTI|BT)\b.*/i', '', $booking->user->name) : 'N/A' }}" />
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $booking->user ? preg_replace('/\s+(BIN|BINTI|BT)\b.*/i', '', $booking->user->name) : 'No user found' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Booked {{ \Carbon\Carbon::parse($booking->created_at)->diffForHumans() }}
                        </div>
                    </div>
                </div>

                <!-- Urgency indicators -->
                @if($booking->status == 'approved' && (\Carbon\Carbon::parse($booking->start_time)->isToday() || \Carbon\Carbon::parse($booking->start_time)->isTomorrow()))
                    <div class="mb-3">
                        @if(\Carbon\Carbon::parse($booking->start_time)->isToday())
                            <flux:badge color="orange" size="sm">
                                <flux:icon name="exclamation-triangle" class="w-3 h-3 mr-1" />
                                Starting Today
                            </flux:badge>
                        @elseif(\Carbon\Carbon::parse($booking->start_time)->isTomorrow())
                            <flux:badge color="blue" size="sm">
                                <flux:icon name="clock" class="w-3 h-3 mr-1" />
                                Starting Tomorrow
                            </flux:badge>
                        @endif
                    </div>
                @endif

                <!-- Card Actions -->
                <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-neutral-700">
                    <flux:button size="sm" href="{{ route('bookings.show', ['id' => $booking->id, 'page' => $bookings->currentPage()]) }}" variant="ghost">
                        <flux:icon name="eye" class="w-4 h-4 mr-1" />
                        View More
                    </flux:button>
                    
                    @can('book.edit')
                    <flux:dropdown>
                        <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost"></flux:button>                                           
                        <flux:menu>
                            <flux:menu.submenu heading="Change Status" icon="cog-6-tooth">
                                <flux:menu.radio.group>
                                    <flux:menu.radio :checked="$booking->status == 'pending'" wire:click="changeStatus('pending', {{ $booking->id }})">
                                        Pending
                                    </flux:menu.radio>
                                    <flux:menu.radio :checked="$booking->status == 'approved'" wire:click="changeStatus('approved', {{ $booking->id }})">                                                     
                                        Approve
                                    </flux:menu.radio>   
                                    <flux:menu.radio :checked="$booking->status == 'rejected'" wire:click="changeStatus('rejected', {{ $booking->id }})">                                                    
                                        Reject
                                    </flux:menu.radio>                                                                                                     
                                    <flux:menu.radio :checked="$booking->status == 'cancelled'" wire:click="changeStatus('cancelled', {{ $booking->id }})">                                                     
                                        Cancel
                                    </flux:menu.radio>
                                    <flux:menu.radio :checked="$booking->status == 'done'" wire:click="changeStatus('done', {{ $booking->id }})">                                                   
                                        Mark Done
                                    </flux:menu.radio>
                                </flux:menu.radio.group>
                            </flux:menu.submenu>
                            <flux:menu.separator />
                            <flux:menu.item icon="pencil" href="{{ route('bookings.edit', ['booking' => $booking->id, 'page' => $bookings->currentPage()]) }}">
                                Edit Details
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                    @endcan
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-8 text-center">
                <div class="flex flex-col items-center gap-2">
                    <flux:icon name="calendar-days" class="w-8 h-8 text-gray-400" />
                    <div class="text-gray-500">No bookings found</div>
                    @can('book.create')
                    <flux:button size="sm" href="{{ route('bookings.create') }}" variant="ghost" class="mt-2">
                        Create first booking
                    </flux:button>
                    @endcan
                </div>
            </div>
        @endforelse

        <!-- Mobile Pagination -->
        @if($bookings->hasPages())
        <div class="pt-4 border-t border-gray-200 dark:border-neutral-700">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>    
</div>