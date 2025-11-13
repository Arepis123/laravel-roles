@component('mail::message')
# New {{ $assetDetails['type'] }} Booking Request

{{-- @component('mail::panel')
**⚠️ Action Required:** A new booking request has been submitted and requires your review.
@endcomponent --}}

## Booking Information

@component('mail::table')
| | |
|:---|:---|
| **Booking ID:** | #{{ $booking->id }} |
| **Status:** | **PENDING APPROVAL** |
| **Booked By:** | {{ $user->name }}<br>{{ $user->email }} |
| **Booking Type:** | {{ $assetDetails['type'] }} |
| **{{ $assetDetails['label'] }}:** | {{ $assetDetails['name'] }} |
@php
    $startDate = \Carbon\Carbon::parse($booking->start_time);
    $endDate = \Carbon\Carbon::parse($booking->end_time);
    $isMultiDay = !$startDate->isSameDay($endDate);
@endphp
@if($isMultiDay)
| **Period:** | {{ $startDate->format('F j, Y') }} - {{ $endDate->format('F j, Y') }} ({{ $startDate->diffInDays($endDate) + 1 }} days) |
| **Daily Time:** | {{ $startDate->format('g:i A') }} - {{ $endDate->format('g:i A') }} |
@else
| **Date:** | {{ $startDate->format('l, F j, Y') }} |
| **Time:** | {{ $startDate->format('g:i A') }} - {{ $endDate->format('g:i A') }} @php $duration = $startDate->diff($endDate); $hours = $duration->h; $minutes = $duration->i; @endphp ({{ $hours > 0 ? $hours . ' hour' . ($hours > 1 ? 's' : '') : '' }}{{ $minutes > 0 ? ($hours > 0 ? ' ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : '' }}) |
@endif
| **Purpose:** | {{ $booking->purpose }} |
@if($booking->capacity)
| **Capacity:** | {{ $booking->capacity }} {{ $assetDetails['type'] === 'Vehicle' ? 'person(s)' : 'people' }} |
@endif
@if(isset($assetDetails['destination']))
| **Destination:** | {{ $assetDetails['destination'] }} |
@endif
@if($assetDetails['type'] === 'Vehicle' && isset($assetDetails['last_parking']))
| **Last Parking:** | Level {{ $assetDetails['last_parking']['level'] }} @if($assetDetails['last_parking']['is_reserved'])(Reserved Slot)@endif<br>Last parked on {{ $assetDetails['last_parking']['date'] }} |
@endif
@if(isset($assetDetails['passengers']))
| **Passengers:** | {{ $assetDetails['passengers'] }} |
@endif
@endcomponent

@if($booking->additional_booking && count($booking->additional_booking) > 0)
### Additional Services Requested

@foreach($booking->additional_booking as $service)
@switch($service)
    @case('refreshment')
- **Refreshment**
@if($booking->refreshment_details)

  Details: {{ $booking->refreshment_details }}

@endif
        @break
    @case('technical')

- **Technical Support**
        @break
    @case('email')

- **Email & System Setup**
        @break
    @default

- **{{ ucfirst(str_replace('_', ' ', $service)) }}**
@endswitch
@endforeach
@endif

---

### Next Steps:

1. Review the booking details above
2. Approve or reject the booking in the system
3. The requester will be notified of your decision

@component('mail::button', ['url' => route('bookings.index')])
View Booking in System
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
This is an automated email from the eBooking system. Please do not reply to this email. For assistance, contact the Admin department.
@endslot
@endcomponent
