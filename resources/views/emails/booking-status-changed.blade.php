<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Status Update</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <h2>Booking Status Update</h2>
        </div>
        
        <div class="content">
            <p>Hello {{ $notifiable->name }},</p>
            
            <p>Your booking status has been updated:</p>
            
            <div class="details">
                <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                <p><strong>Previous Status:</strong> {{ ucfirst($oldStatus) }}</p>
                <p><strong>New Status:</strong> 
                    <span class="status-badge" style="background-color: {{ $statusColor }};">
                        {{ ucfirst($newStatus) }}
                    </span>
                </p>
                <p><strong>Updated by:</strong> {{ $changedByName }}</p>
                <p><strong>Updated at:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>
            </div>

            @if($statusMessage)
            <div style="border-left: 4px solid {{ $statusColor }}; padding-left: 20px; margin: 20px 0;">
                <p>{!! $statusMessage !!}</p>
            </div>
            @endif
            
            <p style="text-align: center;">
                <a href="{{ url('/bookings/' . $booking->id) }}" class="button">View Booking Details</a>
            </p>
            
            <p>If you have any questions about this status change, please don't hesitate to contact our support team.</p>
        </div>
        
        <div class="footer">
            <p>Best regards,<br>{{ config('app.name') }} Team</p>
        </div>
    </div>
</body>
</html>