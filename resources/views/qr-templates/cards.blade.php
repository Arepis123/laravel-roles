<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code Information Cards</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            page-break-inside: avoid;
            margin-bottom: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .asset-info {
            flex: 1;
        }

        .asset-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .asset-type {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .qr-section {
            text-align: center;
            margin: 15px 0;
        }

        .qr-code svg {
            width: {{ $qrSize === 'small' ? '120px' : ($qrSize === 'medium' ? '150px' : '180px') }};
            height: {{ $qrSize === 'small' ? '120px' : ($qrSize === 'medium' ? '150px' : '180px') }};
            border: 1px solid #eee;
            border-radius: 4px;
        }

        .scan-instruction {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
        }

        .details {
            font-size: 14px;
            color: #555;
            line-height: 1.4;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .detail-row {
            margin-bottom: 8px;
        }

        .detail-label {
            font-weight: 600;
            color: #333;
        }

        @if($includeLogo)
        .logo {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            background: #f0f0f0;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #999;
        }
        @endif

        @media print {
            body {
                margin: 0;
                padding: 10px;
                background: white;
            }
            .card {
                box-shadow: none;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="card-container">
        @foreach($assets as $asset)
            <div class="card">
                @if($includeLogo)
                    <div class="logo">LOGO</div>
                @endif

                <div class="card-header">
                    <div class="asset-info">
                        <div class="asset-name">{{ $asset['name'] }}</div>
                        <span class="asset-type">{{ $asset['type_label'] }}</span>
                    </div>
                </div>

                <div class="qr-section">
                    <div class="qr-code">
                        {!! $asset['model']->getQrCodeSvg($qrSize === 'small' ? 120 : ($qrSize === 'medium' ? 150 : 180)) !!}
                    </div>
                    <div class="scan-instruction">
                        Scan this QR code to complete your booking
                    </div>
                </div>

                @if($includeAssetInfo)
                    <div class="details">
                        <div class="detail-row">
                            <span class="detail-label">Asset ID:</span> {{ $asset['real_id'] }}
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Details:</span> {{ $asset['details'] }}
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Total Bookings:</span> {{ $asset['total_bookings'] }}
                        </div>
                        @if($asset['last_booking'])
                            <div class="detail-row">
                                <span class="detail-label">Last Used:</span> {{ $asset['last_booking']->format('M j, Y') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</body>
</html>