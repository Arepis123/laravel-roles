<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Incomplete Booking Reminder</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { background: white; padding: 30px; border: 1px solid #e1e1e1; border-top: none; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: bold; color: white; margin: 10px 0; }
        .details { background: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0; }
        .details strong { color: #495057; }
        .button { display: inline-block; background: #667eea; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; color: #6c757d; padding: 20px; font-size: 14px; }
        .alert-box { border-left: 4px solid #ffc107; background: #fff3cd; padding: 15px; margin: 20px 0; border-radius: 6px; color: #856404; }
        .info-box { border-left: 4px solid #667eea; background: #e7f1ff; padding: 15px; margin: 20px 0; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <h2>⏰ Booking Completion Reminder</h2>
        </div>

        <div class="content">
            <p>Hello {{ $user->name }},</p>

            <div class="alert-box">
                <strong>⚠️ Action Required:</strong> Your booking has ended, but it has not been marked as complete. Please mark your booking as done and provide the required details.
            </div>

            <p>You had a booking that has ended. To ensure proper asset management and tracking, please mark your booking as complete and provide the necessary details.</p>

            @if($customMessage)
            <div style="border-left: 4px solid #667eea; padding-left: 20px; margin: 20px 0; background: #f8f9fa; padding: 15px; border-radius: 6px;">
                <p style="margin: 0;"><strong>Message from Admin:</strong></p>
                <p style="margin: 10px 0 0 0;">{{ $customMessage }}</p>
            </div>
            @endif

            <div class="details">
                <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                <p><strong>Status:</strong>
                    <span class="status-badge" style="background-color: #4CAF50;">
                        Approved
                    </span>
                    <span style="color: #f44336; font-weight: bold;">(Awaiting Completion)</span>
                </p>
                <p><strong>Booking Type:</strong> {{ $assetDetails['type'] }}</p>
                <p><strong>{{ $assetDetails['label'] }}:</strong> {{ $assetDetails['name'] }}</p>

                @php
                    $startDate = \Carbon\Carbon::parse($booking->start_time);
                    $endDate = \Carbon\Carbon::parse($booking->end_time);
                    $isMultiDay = !$startDate->isSameDay($endDate);
                @endphp

                @if($isMultiDay)
                    <p><strong>Period:</strong> {{ $startDate->format('F j, Y') }} - {{ $endDate->format('F j, Y') }} ({{ $startDate->diffInDays($endDate) + 1 }} days)</p>
                    <p><strong>Daily Time:</strong> {{ $startDate->format('g:i A') }} - {{ $endDate->format('g:i A') }}</p>
                @else
                    <p><strong>Date:</strong> {{ $startDate->format('l, F j, Y') }}</p>
                    <p><strong>Time:</strong> {{ $startDate->format('g:i A') }} - {{ $endDate->format('g:i A') }}
                        @php
                            $duration = $startDate->diff($endDate);
                            $hours = $duration->h;
                            $minutes = $duration->i;
                        @endphp
                        ({{ $hours > 0 ? $hours . ' hour' . ($hours > 1 ? 's' : '') : '' }}{{ $minutes > 0 ? ($hours > 0 ? ' ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : '' }})
                    </p>
                @endif

                <p><strong>Ended:</strong> <span style="color: #f44336;">{{ $endDate->diffForHumans() }}</span></p>
                <p><strong>Purpose:</strong> {{ $booking->purpose }}</p>

                @if(isset($assetDetails['destination']))
                    <p><strong>Destination:</strong> {{ $assetDetails['destination'] }}</p>
                @endif

                @if(isset($assetDetails['passengers']))
                    <p><strong>Passengers:</strong><br>
                        <span style="background: #f0f0f0; padding: 8px; display: inline-block; border-radius: 4px; margin-top: 5px;">
                            {{ $assetDetails['passengers'] }}
                        </span>
                    </p>
                @endif
            </div>

            <div class="info-box">
                <strong>What you need to do:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    @if($assetDetails['type'] === 'Vehicle')
                        <li>Provide the final odometer reading</li>
                        <li>Indicate if you refueled the vehicle and the amount</li>
                        <li>Add any remarks about the vehicle condition</li>
                    @elseif($assetDetails['type'] === 'Meeting Room')
                        <li>Confirm the meeting room has been cleaned and restored</li>
                        <li>Report any issues or damage if applicable</li>
                        <li>Add any remarks about your usage</li>
                    @elseif($assetDetails['type'] === 'IT Asset')
                        <li>Confirm the IT asset is in good condition</li>
                        <li>Report any issues encountered during usage</li>
                        <li>Add any remarks about your usage</li>
                    @else
                        <li>Confirm the asset has been returned in good condition</li>
                        <li>Report any issues if applicable</li>
                        <li>Add any remarks about your usage</li>
                    @endif
                    <li>Mark the booking as "Done" in the system</li>
                </ul>
            </div>

            <p style="text-align: center;">
                <a href="{{ route('bookings.index.user') }}" class="button">Complete My Booking</a>
            </p>

            <p style="color: #666; font-size: 14px;"><strong>Note:</strong> Completing your booking helps us maintain accurate records and ensures assets are properly managed for other users.</p>
        </div>

        <div class="footer">
            <p>Best regards,<br>{{ config('app.name') }} Team</p>
            <p style="margin-top: 10px;">This is an automated reminder. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
