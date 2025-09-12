<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Analytics Report</title>
    <style>
        @page {
            margin: 20mm;
            size: A4 landscape;
        }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
            padding-bottom: 15px;
            border-bottom: 3px solid #2563EB;
        }
        .header h1 {
            color: #2563EB;
            font-size: 24px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }
        .header .subtitle {
            color: #374151;
            font-size: 16px;
            margin: 0;
            font-weight: 600;
        }
        .info { 
            margin-bottom: 20px; 
            display: flex;
            justify-content: space-between;
            background-color: #F9FAFB;
            padding: 10px;
            border-radius: 5px;
        }
        .info-left, .info-right {
            flex: 1;
        }
        .info p {
            margin: 3px 0;
            font-size: 9px;
            color: #6B7280;
        }
        .info strong {
            color: #374151;
            font-weight: 600;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
            font-size: 8px;
        }
        th {
            background: linear-gradient(135deg, #1F2937 0%, #374151 100%);
            color: white;
            font-weight: bold;
            padding: 8px 4px;
            text-align: left;
            border: 1px solid #374151;
            font-size: 8px;
        }
        td { 
            border: 1px solid #E5E7EB; 
            padding: 6px 4px; 
            text-align: left;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        tr:hover {
            background-color: #F3F4F6;
        }
        .section-title { 
            font-size: 16px; 
            font-weight: bold; 
            margin: 25px 0 15px 0;
            color: #1F2937;
            padding-left: 10px;
            border-left: 4px solid #2563EB;
        }
        .no-data {
            text-align: center;
            color: #6B7280;
            font-style: italic;
            padding: 30px;
            background-color: #F9FAFB;
        }
        .summary {
            background-color: #EBF8FF;
            border: 1px solid #90CDF4;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .summary h3 {
            color: #1E3A8A;
            margin: 0 0 5px 0;
            font-size: 12px;
        }
        .summary p {
            margin: 0;
            font-size: 9px;
            color: #1E40AF;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Vehicle Analytics Report</h1>
        <p class="subtitle">{{ ucfirst($analyticsType) }} Analytics</p>
    </div>

    <div class="info">
        <div class="info-left">
            <p><strong>Date Range:</strong> {{ $data['date_from'] }} to {{ $data['date_to'] }}</p>
            @if(isset($data['vehicle']))
                <p><strong>Vehicle:</strong> {{ $data['vehicle']->model }} ({{ $data['vehicle']->plate_number }})</p>
            @else
                <p><strong>Vehicle:</strong> All Vehicles</p>
            @endif
        </div>
        <div class="info-right">
            <p><strong>Generated At:</strong> {{ $data['generated_at'] }}</p>
            <p><strong>Report Type:</strong> {{ ucfirst($analyticsType) }}</p>
        </div>
    </div>

    @php
        $recordCount = 0;
        $totalCost = 0;
        
        switch($analyticsType) {
            case 'fuel':
                $recordCount = isset($data['fuel_data']) ? count($data['fuel_data']) : 0;
                $totalCost = isset($data['fuel_data']) ? $data['fuel_data']->sum('fuel_cost') : 0;
                break;
            case 'maintenance':
                $recordCount = isset($data['maintenance_data']) ? count($data['maintenance_data']) : 0;
                $totalCost = isset($data['maintenance_data']) ? $data['maintenance_data']->sum('cost') : 0;
                break;
            case 'odometer':
                $recordCount = isset($data['odometer_data']) ? count($data['odometer_data']) : 0;
                break;
            default:
                $recordCount = isset($data['overview_data']['vehicles']) ? count($data['overview_data']['vehicles']) : 0;
        }
    @endphp

    @if($recordCount > 0)
        <div class="summary">
            <h3>Summary</h3>
            <p><strong>Total Records:</strong> {{ $recordCount }}</p>
            @if(in_array($analyticsType, ['fuel', 'maintenance']))
                <p><strong>Total Cost:</strong> RM {{ number_format($totalCost, 2) }}</p>
            @endif
        </div>
    @endif

    @if($analyticsType === 'fuel' && isset($data['fuel_data']))
        <div class="section-title">Fuel Analytics</div>
        @if(count($data['fuel_data']) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%">Date & Time</th>
                        <th style="width: 12%">Vehicle Model</th>
                        <th style="width: 8%">Plate Number</th>
                        <th style="width: 8%">Fuel Type</th>
                        <th style="width: 7%">Amount</th>
                        <th style="width: 8%">Cost</th>
                        <th style="width: 12%">Location</th>
                        <th style="width: 7%">Odometer</th>
                        <th style="width: 10%">Filled By</th>
                        <th style="width: 18%">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['fuel_data'] as $log)
                        <tr>
                            <td>{{ $log->filled_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $log->vehicle->model ?? 'N/A' }}</td>
                            <td>{{ $log->vehicle->plate_number ?? 'N/A' }}</td>
                            <td>{{ ucfirst($log->fuel_type) }}</td>
                            <td>{{ number_format($log->fuel_amount, 2) }} L</td>
                            <td>RM {{ number_format($log->fuel_cost, 2) }}</td>
                            <td>{{ $log->fuel_station ?? '-' }}</td>
                            <td>{{ $log->odometer_at_fill ? number_format($log->odometer_at_fill) . ' km' : '-' }}</td>
                            <td>{{ $log->filledBy->name ?? '-' }}</td>
                            <td>{{ $log->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No fuel records found for the selected period.</div>
        @endif
    @endif

    @if($analyticsType === 'odometer' && isset($data['odometer_data']))
        <div class="section-title">Odometer Analytics</div>
        @if(count($data['odometer_data']) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 12%">Date & Time</th>
                        <th style="width: 15%">Vehicle Model</th>
                        <th style="width: 10%">Plate Number</th>
                        <th style="width: 10%">Reading</th>
                        <th style="width: 10%">Type</th>
                        <th style="width: 12%">Performed By</th>
                        <th style="width: 12%">Recorded By</th>
                        <th style="width: 19%">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['odometer_data'] as $log)
                        <tr>
                            <td>{{ $log->recorded_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $log->vehicle->model ?? 'N/A' }}</td>
                            <td>{{ $log->vehicle->plate_number ?? 'N/A' }}</td>
                            <td>{{ number_format($log->odometer_reading) }} km</td>
                            <td>{{ ucfirst($log->reading_type) }}</td>
                            <td>{{ $log->performed_by ?? '-' }}</td>
                            <td>{{ $log->recordedBy->name ?? '-' }}</td>
                            <td>{{ $log->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No odometer records found for the selected period.</div>
        @endif
    @endif

    @if($analyticsType === 'maintenance' && isset($data['maintenance_data']))
        <div class="section-title">Maintenance Analytics</div>
        @if(count($data['maintenance_data']) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 12%">Date & Time</th>
                        <th style="width: 12%">Vehicle</th>
                        <th style="width: 10%">Plate</th>
                        <th style="width: 10%">Type</th>
                        <th style="width: 15%">Title</th>
                        <th style="width: 8%">Cost</th>
                        <th style="width: 8%">Status</th>
                        <th style="width: 12%">Service Provider</th>
                        <th style="width: 10%">Next Due</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['maintenance_data'] as $log)
                        <tr>
                            <td>{{ $log->performed_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $log->vehicle->model ?? 'N/A' }}</td>
                            <td>{{ $log->vehicle->plate_number ?? 'N/A' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $log->maintenance_type)) }}</td>
                            <td>{{ $log->title }}</td>
                            <td>RM {{ number_format($log->cost ?? 0, 2) }}</td>
                            <td>{{ ucfirst($log->status) }}</td>
                            <td>{{ $log->service_provider ?? '-' }}</td>
                            <td>{{ $log->next_maintenance_due ? $log->next_maintenance_due->format('Y-m-d') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No maintenance records found for the selected period.</div>
        @endif
    @endif

    @if($analyticsType === 'overview' && isset($data['overview_data']))
        <div class="section-title">Fleet Overview</div>
        @if(count($data['overview_data']['vehicles']) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 8%">ID</th>
                        <th style="width: 25%">Model</th>
                        <th style="width: 12%">Plate Number</th>
                        <th style="width: 10%">Status</th>
                        <th style="width: 15%">Engine Number</th>
                        <th style="width: 15%">Chassis Number</th>
                        <th style="width: 8%">Year</th>
                        <th style="width: 12%">Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['overview_data']['vehicles'] as $vehicle)
                        <tr>
                            <td>{{ $vehicle->id }}</td>
                            <td>{{ $vehicle->model }}</td>
                            <td>{{ $vehicle->plate_number }}</td>
                            <td>{{ ucfirst($vehicle->status) }}</td>
                            <td>{{ $vehicle->engine_number ?? '-' }}</td>
                            <td>{{ $vehicle->chassis_number ?? '-' }}</td>
                            <td>{{ $vehicle->year ?? '-' }}</td>
                            <td>{{ $vehicle->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No vehicles found.</div>
        @endif
    @endif
</body>
</html>