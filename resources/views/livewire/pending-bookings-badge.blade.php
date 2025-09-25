<div class="inline-block">
    @if($count > 0)
        {{-- Just show the badge without any tooltip - let sidebar handle tooltip --}}
        @if($urgentCount > 0)
            {{-- Red badge for urgent pending bookings --}}
            <flux:badge variant="pill" color="red" size="sm" class="ml-auto animate-pulse">
                {{ $count > 99 ? '99+' : $count }}
                <flux:icon name="exclamation-triangle" class="w-3 h-3 ml-1" />
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
    @endif
</div>