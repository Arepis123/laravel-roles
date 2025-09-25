<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code Information Cards</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Helvetica, sans-serif;
            background-color: #ffffff;
        }

        .card-container {
            display: block;
        }

        .card {
            background: #4a9b8e;
            color: white;
            padding: 20px;
            margin-bottom: 40px;
            width: {{ $qrSize === 'small' ? '190px' : ($qrSize === 'medium' ? '240px' : '300px') }};
            text-align: center;
            border-radius: 15px;
        }

        .qr-section {
            background: white;
            padding: 20px;
            margin: 0 0 0 0;
            border-radius: 15px;
        }

        .qr-code {
            display: block;
            margin: 0 auto;
        }

        .scan-title {
            font-size: 24px;
            font-weight: bold;
            margin: 15px 0 5px 0;
            color: white;
        }

        .scan-instruction {
            font-size: 14px;
            color: white;
            margin: 0;
        }

        .asset-info {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.3);
        }

        .asset-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 6px;
            color: white;
        }

        .asset-details {
            font-size: 12px;
            color: rgba(255,255,255,0.9);
            line-height: 1.4;
        }

        .detail-row {
            margin-bottom: 4px;
        }

        .company-logo {
            max-width: 144px;
            max-height: 72px;
            margin-bottom: 15px;
            filter: brightness(0) invert(1); /* Make logo white for dark background */
        }
    </style>
</head>
<body>
    <div class="card-container">
        @foreach($assets as $asset)
            <div class="card">
                @if($includeLogo)
                    @php
                        $logoPath = public_path('image/company-logo.png');
                        $logoExists = file_exists($logoPath);
                        $logoBase64 = null;

                        if ($logoExists) {
                            try {
                                $logoData = file_get_contents($logoPath);
                                $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                            } catch (Exception $e) {
                                $logoBase64 = null;
                            }
                        }
                    @endphp

                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Company Logo" class="company-logo" />
                    @endif
                @endif

                <!-- QR Code Section -->
                <div class="qr-section">
                    @php
                        $size = $qrSize === 'small' ? 150 : ($qrSize === 'medium' ? 180 : 260);
                        $qrCode = $asset['model']->getQrCodeForPdf($size);
                    @endphp
                    @if(str_starts_with($qrCode, 'data:image/'))
                        <img src="{{ $qrCode }}" alt="QR Code" class="qr-code" style="width: {{ $size }}px; height: {{ $size }}px;" />
                    @else
                        <div class="qr-code" style="width: {{ $size }}px; height: {{ $size }}px; margin: 0 auto;">
                            {!! $qrCode !!}
                        </div>
                    @endif
                </div>

                <!-- Scan Instructions -->
                <div class="scan-title">Scan me</div>
                <div class="scan-instruction">
                    @php
                        $assetType = strtolower($asset['type_label']);
                        $instruction = match($assetType) {
                            'vehicle' => 'Please scan QR Code after done using vehicle',
                            'meeting room', 'meetingroom' => 'Please scan QR Code after done using meeting room',
                            'it asset', 'itasset' => 'Please scan QR Code after done using equipment',
                            default => 'Please scan QR Code after done using this asset'
                        };
                    @endphp
                    {{ $instruction }}
                </div>

                <!-- Asset Information -->
                @if($includeAssetInfo)
                    <div class="asset-info">
                        <div class="asset-name">{{ $asset['name'] }}</div>
                        <div class="asset-details">
                            <div class="detail-row">{{ $asset['type_label'] }} • ID: {{ $asset['real_id'] }}</div>
                            {{-- @if($asset['last_booking'])
                                <div class="detail-row">Last used: {{ $asset['last_booking']->format('M j, Y') }}</div>
                            @endif --}}
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</body>
</html>