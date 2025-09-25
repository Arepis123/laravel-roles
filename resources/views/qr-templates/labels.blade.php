<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code Labels</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Helvetica, sans-serif;
        }

        .label-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            page-break-inside: avoid;
        }

        .label {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
        }

        .qr-code {
            margin-bottom: 8px;
        }

        .qr-code svg {
            width: {{ $qrSize === 'small' ? '80px' : ($qrSize === 'medium' ? '100px' : '120px') }};
            height: {{ $qrSize === 'small' ? '80px' : ($qrSize === 'medium' ? '100px' : '120px') }};
        }

        .asset-info {
            font-size: 10px;
            color: #333;
            text-align: center;
            line-height: 1.2;
        }

        .asset-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .asset-type {
            color: #666;
            font-size: 8px;
        }

        .company-logo {
            max-width: 96px;
            max-height: 48px;
            margin-bottom: 8px;
            opacity: 0.8;
        }

        @media print {
            body { margin: 0; padding: 10px; }
            .label { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="label-grid">
        @foreach($assets as $asset)
            <div class="label">
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

                <div class="qr-code">
                    @php
                        $size = $qrSize === 'small' ? 80 : ($qrSize === 'medium' ? 100 : 120);
                        $qrCode = $asset['model']->getQrCodeForPdf($size);
                    @endphp
                    @if(str_starts_with($qrCode, 'data:image/'))
                        <img src="{{ $qrCode }}" alt="QR Code" style="width: {{ $size }}px; height: {{ $size }}px;" />
                    @else
                        {!! $qrCode !!}
                    @endif
                </div>

                @if($includeAssetInfo)
                    <div class="asset-info">
                        <div class="asset-name">{{ $asset['name'] }}</div>
                        <div class="asset-type">{{ $asset['type_label'] }}</div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</body>
</html>