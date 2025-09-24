<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ ucfirst($type) }} Report</title>
    <style>
        body { font-size: 12px; }
        h1 { color: #333; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .small-font { font-size: 10px; }
        .center { text-align: center; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>{{ ucfirst($type) }} Report</h1>
    <p>Generated on: {{ $generated_at }}</p>
    <p>Total Records: {{ $data->count() }}</p>

    @if($type === 'assets')
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->type }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($type === 'vehicles')
        <table class="small-font">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Model</th>
                    <th>Plate No.</th>
                    <th>Capacity</th>
                    <th>Driver</th>
                    <th>Status</th>
                    <th>Total Fuel (L)</th>
                    <th>Fuel Sessions</th>
                    <th>Latest Odometer</th>
                    <th>Total Distance</th>
                    <th>Total Bookings</th>
                    <th>Utilization (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $vehicle)
                    <tr>
                        <td>{{ $vehicle->id }}</td>
                        <td>{{ $vehicle->model }}</td>
                        <td>{{ $vehicle->plate_number }}</td>
                        <td class="center">{{ $vehicle->capacity }}</td>
                        <td>{{ $vehicle->driver_name }}</td>
                        <td>{{ ucfirst($vehicle->status) }}</td>
                        <td class="right">{{ $vehicle->total_fuel_filled }}</td>
                        <td class="center">{{ $vehicle->fuel_sessions_count }}</td>
                        <td class="right">{{ $vehicle->latest_odometer }}</td>
                        <td class="right">{{ $vehicle->total_distance }}</td>
                        <td class="center">{{ $vehicle->total_bookings }}</td>
                        <td class="center">{{ $vehicle->utilization_rate }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($data->count() > 0)
            <div style="margin-top: 20px;">
                <h3>Summary Statistics</h3>
                <p><strong>Total Vehicles:</strong> {{ $data->count() }}</p>
                <p><strong>Total Fuel Consumed:</strong> {{ $data->sum('total_fuel_filled') }} L</p>
                <p><strong>Average Utilization Rate:</strong> {{ round($data->avg('utilization_rate'), 1) }}%</p>
                <p><strong>Total Distance Covered:</strong> {{ $data->sum('total_distance') }} km</p>
                <p><strong>Total Bookings:</strong> {{ $data->sum('total_bookings') }}</p>
            </div>
        @endif
    @endif

    @if($type === 'bookings')
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Asset</th>
                    <th>Asset Type</th>
                    <th>User</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $booking)
                    <tr>
                        <td>{{ $booking->id }}</td>
                        <td>{{ $booking->asset_name ?? 'N/A' }}</td>
                        <td>{{ $booking->asset_type ?? 'N/A' }}</td>
                        <td>{{ $booking->user_name ?? 'N/A' }}</td>
                        <td>{{ $booking->start_date ?? 'N/A' }}</td>
                        <td>{{ $booking->end_date ?? 'N/A' }}</td>
                        <td>{{ ucfirst($booking->status) }}</td>
                        <td>{{ $booking->notes ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($type === 'users')
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Total Bookings</th>
                    <th>Email Verified</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td class="center">{{ $user->bookings_count }}</td>
                        <td class="center">{{ $user->email_verified_at ? 'Yes' : 'No' }}</td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>