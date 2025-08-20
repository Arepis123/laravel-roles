<div class="inline-flex items-center">
    @if($count > 0)
        @php
            $tooltipContent = 'Pending bookings';
            if ($urgentCount > 0) {
                $tooltipContent = "Urgent! pending > 36hrs";
            } elseif ($todayCount > 0) {
                $tooltipContent = "Today's pending";
            }
        @endphp  

        <flux:tooltip :content="$tooltipContent"> 
            @if($urgentCount > 0)
                {{-- Red badge for urgent pending bookings --}}
                <flux:badge variant="pill" color="red" size="sm" class="ml-auto animate-pulse">
                    {{ $count > 99 ? '99+' : $count }}
                    @if($urgentCount > 0)
                        <flux:icon name="exclamation-triangle" class="w-3 h-3 ml-1" />
                    @endif
                </flux:badge>
            @elseif($todayCount > 0)
                {{-- Orange badge for today's pending bookings --}}
                <flux:badge variant="pill" color="orange" size="sm" class="ml-auto">
                    {{ $count > 99 ? '99+' : $count }}
                </flux:badge>
            @else
                {{-- Yellow badge for regular pending bookings --}}
                <flux:badge variant="pill" color="yellow" size="sm" class="ml-auto">
                    {{ $count > 99 ? '99+' : $count }}
                </flux:badge>
            @endif                       
        </flux:tooltip> 
       
    @endif
</div>