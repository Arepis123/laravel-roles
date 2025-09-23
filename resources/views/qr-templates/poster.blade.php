<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code Poster</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
            background: white;
        }

        .poster-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1976d2;
        }

        .title {
            font-size: 36px;
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }

        @if($includeLogo)
        .logo-section {
            margin-bottom: 20px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: #f0f0f0;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #999;
            margin: 0 auto;
        }
        @endif

        .instruction {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-size: 16px;
            color: #1976d2;
            margin-bottom: 40px;
        }

        .qr-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            justify-items: center;
        }

        .qr-item {
            text-align: center;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            background: #fafafa;
            width: 100%;
            max-width: 280px;
        }

        .qr-code {
            margin-bottom: 15px;
            background: white;
            padding: 10px;
            border-radius: 8px;
            display: inline-block;
        }

        .qr-code svg {
            width: {{ $qrSize === 'small' ? '150px' : ($qrSize === 'medium' ? '200px' : '250px') }};
            height: {{ $qrSize === 'small' ? '150px' : ($qrSize === 'medium' ? '200px' : '250px') }};
        }

        .asset-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .asset-type {
            display: inline-block;
            background: #1976d2;
            color: white;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .asset-details {
            font-size: 14px;
            color: #666;
            line-height: 1.4;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 14px;
            color: #888;
        }

        @media print {
            body {
                margin: 0;
                padding: 20px;
            }

            .poster-header {
                page-break-after: avoid;
            }

            .qr-item {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="poster-header">
        @if($includeLogo)
            <div class="logo-section">
                <div class="logo">COMPANY LOGO</div>
            </div>
        @endif

        <div class="title">Asset QR Codes</div>
        <div class="subtitle">Scan to Complete Your Booking</div>

        <div class="instruction">
            📱 Simply scan the QR code with your phone camera after completing your booking
        </div>
    </div>

    <div class="qr-grid">
        @foreach($assets as $asset)
            <div class="qr-item">
                <div class="qr-code">
                    {!! $asset['model']->getQrCodeSvg($qrSize === 'small' ? 150 : ($qrSize === 'medium' ? 200 : 250)) !!}
                </div>

                <div class="asset-name">{{ $asset['name'] }}</div>
                <div class="asset-type">{{ $asset['type_label'] }}</div>

                @if($includeAssetInfo)
                    <div class="asset-details">
                        {{ $asset['details'] }}
                        @if($asset['total_bookings'] > 0)
                            <br><strong>{{ $asset['total_bookings'] }}</strong> total bookings
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y') }} • Total Assets: {{ count($assets) }}</p>
        <p>For technical support, contact your IT administrator</p>
    </div>
</body>
</html>