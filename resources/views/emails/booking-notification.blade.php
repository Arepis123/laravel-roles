<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4A90E2;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
        }
        .booking-details {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        .detail-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            width: 150px;
            color: #666;
        }
        .detail-value {
            flex: 1;
            color: #333;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            background-color: #FFC107;
            color: #333;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
        }
        .additional-services {
            background-color: #e8f4fd;
            padding: 10px;
            border-radius: 3px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .action-required {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 10px;
            border-radius: 3px;
            margin: 15px 0;
        }
        .passenger-list {
            background-color: #f0f0f0;
            padding: 8px;
            border-radius: 3px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New {{ $assetDetails['type'] }} Booking Request</h1>
    </div>
    
    <div class="content">
        <div class="action-required">
            <strong>⚠️ Action Required:</strong> A new booking request has been submitted and requires your review.
        </div>

        <div class="booking-details">
            <h2 style="margin-top: 0; color: #4A90E2;">Booking Information</h2>
            
            <div class="detail-row">
                <div class="detail-label">Booking ID:</div>
                <div class="detail-value">#{{ $booking->id }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Status:</div>
                <div class="detail-value">
                    <span class="status-badge">PENDING APPROVAL</span>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Booked By:</div>
                <div class="detail-value">
                    {{ $user->name }}<br>
                    <small style="color: #666;">{{ $user->email }}</small>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Booking Type:</div>
                <div class="detail-value">{{ $assetDetails['type'] }}</div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">{{ $assetDetails['label'] }}:</div>
                <div class="detail-value">{{ $assetDetails['name'] }}</div>
            </div>
            
            @php
                $startDate = \Carbon\Carbon::parse($booking->start_time);
                $endDate = \Carbon\Carbon::parse($booking->end_time);
                $isMultiDay = !$startDate->isSameDay($endDate);
            @endphp
            
            @if($isMultiDay)
                <div class="detail-row">
                    <div class="detail-label">Period:</div>
                    <div class="detail-value">
                        {{ $startDate->format('F j, Y') }} - {{ $endDate->format('F j, Y') }}
                        ({{ $startDate->diffInDays($endDate) + 1 }} days)
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Daily Time:</div>
                    <div class="detail-value">
                        {{ $startDate->format('g:i A') }} - {{ $endDate->format('g:i A') }}
                    </div>
                </div>
            @else
                <div class="detail-row">
                    <div class="detail-label">Date:</div>
                    <div class="detail-value">{{ $startDate->format('l, F j, Y') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Time:</div>
                    <div class="detail-value">
                        {{ $startDate->format('g:i A') }} - {{ $endDate->format('g:i A') }}
                        @php
                            $duration = $startDate->diff($endDate);
                            $hours = $duration->h;
                            $minutes = $duration->i;
                        @endphp
                        ({{ $hours > 0 ? $hours . ' hour' . ($hours > 1 ? 's' : '') : '' }}{{ $minutes > 0 ? ($hours > 0 ? ' ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : '' }})
                    </div>
                </div>
            @endif
            
            <div class="detail-row">
                <div class="detail-label">Purpose:</div>
                <div class="detail-value">{{ $booking->purpose }}</div>
            </div>
            
            @if($booking->capacity)
                <div class="detail-row">
                    <div class="detail-label">Capacity:</div>
                    <div class="detail-value">{{ $booking->capacity }} {{ $assetDetails['type'] === 'Vehicle' ? 'person(s)' : 'people' }}</div>
                </div>
            @endif
            
            @if(isset($assetDetails['destination']))
                <div class="detail-row">
                    <div class="detail-label">Destination:</div>
                    <div class="detail-value">{{ $assetDetails['destination'] }}</div>
                </div>
            @endif
            
            @if(isset($assetDetails['passengers']))
                <div class="detail-row">
                    <div class="detail-label">Passengers:</div>
                    <div class="detail-value">
                        <div class="passenger-list">
                            {{ $assetDetails['passengers'] }}
                        </div>
                    </div>
                </div>
            @endif
            
            @if($booking->additional_booking && count($booking->additional_booking) > 0)
                <div class="additional-services">
                    <strong>Additional Services Requested:</strong>
                    <ul style="margin: 5px 0; padding-left: 20px;">
                        @foreach($booking->additional_booking as $service)
                            <li>
                                @switch($service)
                                    @case('refreshment')
                                        Refreshment
                                        @if($booking->refreshment_details)
                                            <br><small style="color: #666;">Details: {{ $booking->refreshment_details }}</small>
                                        @endif
                                        @break
                                    @case('technical')
                                        Technical Support
                                        @break
                                    @case('email')
                                        Email & Other Setup
                                        @break
                                    @default
                                        {{ ucfirst(str_replace('_', ' ', $service)) }}
                                @endswitch
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px; border-radius: 3px; margin-top: 15px;">
            <strong>Next Steps:</strong>
            <ol style="margin: 5px 0; padding-left: 20px;">
                <li>Review the booking details above</li>
                <li>Check availability and any conflicts</li>
                <li>Approve or reject the booking in the system</li>
                <li>The requester will be notified of your decision</li>
            </ol>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('bookings.index') }}" style="display: inline-block; padding: 10px 20px; background-color: #4A90E2; color: white; text-decoration: none; border-radius: 5px;">View Booking in System</a>
        </div>
    </div>
    
    <div class="footer">
        <p>This is an automated email from the eBooking CLAB.</p>
        <p>Please do not reply to this email. For assistance, contact the Admin department.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'eBooking CLAB') }}. All rights reserved.</p>
    </div>
</body>
</html>