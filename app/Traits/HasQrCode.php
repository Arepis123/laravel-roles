<?php

namespace App\Traits;

use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\QrCodeLog;

trait HasQrCode
{
    /**
     * Generate a unique QR code identifier for this asset
     */
    public function generateQrCodeIdentifier(): string
    {
        if (empty($this->qr_code_identifier)) {
            $this->qr_code_identifier = Str::uuid()->toString();
            $this->save();

            // Log the QR code generation
            QrCodeLog::logAction(
                assetType: get_class($this),
                assetId: $this->id,
                qrIdentifier: $this->qr_code_identifier,
                action: 'generated',
                metadata: [
                    'asset_name' => $this->getAssetDisplayName(),
                    'generation_method' => 'auto_generate'
                ]
            );
        }

        return $this->qr_code_identifier;
    }

    /**
     * Force regenerate a new QR code identifier for this asset
     */
    public function regenerateQrCodeIdentifier(): string
    {
        $oldIdentifier = $this->qr_code_identifier;
        $this->qr_code_identifier = Str::uuid()->toString();
        $this->save();

        // Log the QR code regeneration
        QrCodeLog::logAction(
            assetType: get_class($this),
            assetId: $this->id,
            qrIdentifier: $this->qr_code_identifier,
            action: 'regenerated',
            metadata: [
                'asset_name' => $this->getAssetDisplayName(),
                'old_identifier' => $oldIdentifier,
                'new_identifier' => $this->qr_code_identifier,
                'generation_method' => 'manual_regenerate'
            ]
        );

        return $this->qr_code_identifier;
    }

    /**
     * Get the QR code identifier for this asset
     */
    public function getQrCodeIdentifier(): ?string
    {
        return $this->qr_code_identifier;
    }

    /**
     * Generate QR code URL for completing booking
     */
    public function getQrCodeUrl(): string
    {
        $identifier = $this->generateQrCodeIdentifier();
        return route('booking.complete-qr', [
            'type' => class_basename(static::class),
            'identifier' => $identifier
        ]);
    }

    /**
     * Generate QR code SVG
     */
    public function getQrCodeSvg($size = 200): string
    {
        return QrCode::size($size)->generate($this->getQrCodeUrl());
    }

    /**
     * Generate QR code PNG as base64 data URL for PDFs
     */
    public function getQrCodePng($size = 200): string
    {
        try {
            $pngData = QrCode::format('png')->size($size)->generate($this->getQrCodeUrl());
            return 'data:image/png;base64,' . base64_encode($pngData);
        } catch (\Exception $e) {
            // Fallback to SVG if PNG fails
            return $this->getQrCodeSvg($size);
        }
    }

    /**
     * Get QR code for PDF generation (optimized for PDF libraries)
     */
    public function getQrCodeForPdf($size = 200): string
    {
        try {
            // Ensure we have a QR identifier
            if (empty($this->qr_code_identifier)) {
                $this->generateQrCodeIdentifier();
            }

            $url = $this->getQrCodeUrl();

            // Priority 1: Try multiple PNG generation approaches with available extensions
            $pngApproaches = [
                'gd' => function() use ($url, $size) {
                    return QrCode::format('png')->size($size)->writer('gd')->generate($url);
                },
                'default' => function() use ($url, $size) {
                    return QrCode::format('png')->size($size)->generate($url);
                }
            ];

            foreach ($pngApproaches as $method => $approach) {
                try {
                    $pngData = $approach();
                    if ($pngData && strlen($pngData) > 100) {
                        $base64 = base64_encode($pngData);
                        \Log::info('QR Code PNG generated for PDF', [
                            'asset_id' => $this->id,
                            'asset_type' => get_class($this),
                            'size' => $size,
                            'method' => $method,
                            'png_data_length' => strlen($pngData)
                        ]);
                        return 'data:image/png;base64,' . $base64;
                    }
                } catch (\Exception $e) {
                    // Don't log expected failures - just continue to next method
                    continue;
                }
            }

            // Priority 2: Use SVG converted to base64 data URL (better DomPDF compatibility)
            try {
                $svgContent = QrCode::format('svg')->size($size)->generate($url);
                if ($svgContent && strlen($svgContent) > 100) {
                    // Convert SVG to base64 data URL for better DomPDF compatibility
                    $base64Svg = base64_encode($svgContent);
                    \Log::info('QR Code SVG converted to base64 for PDF', [
                        'asset_id' => $this->id,
                        'asset_type' => get_class($this),
                        'size' => $size,
                        'svg_length' => strlen($svgContent),
                        'base64_length' => strlen($base64Svg)
                    ]);
                    return 'data:image/svg+xml;base64,' . $base64Svg;
                }
            } catch (\Exception $e) {
                \Log::error('SVG QR Code generation failed: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            \Log::error('QR Code generation failed, using fallbacks', [
                'asset_id' => $this->id,
                'asset_type' => get_class($this),
                'error' => $e->getMessage()
            ]);
        }

        // Priority 3: Use GD fallback (creates simple QR-like pattern)
        try {
            $image = $this->createFallbackQrImage($size);
            if ($image) {
                \Log::info('Using GD fallback QR image for PDF', ['asset_id' => $this->id, 'size' => $size]);
                return $image;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create fallback QR image', ['error' => $e->getMessage()]);
        }

        // Final fallback: Placeholder text
        \Log::warning('All QR Code generation methods failed, using placeholder', ['asset_id' => $this->id]);
        return '<div style="width:' . $size . 'px;height:' . $size . 'px;border:2px dashed #ccc;display:table-cell;vertical-align:middle;text-align:center;font-size:12px;color:#999;background:#f9f9f9;">QR Code<br>Unable to<br>Generate</div>';
    }

    /**
     * Create a simple QR-like fallback image using GD
     */
    private function createFallbackQrImage($size = 200): ?string
    {
        try {
            if (!function_exists('imagecreate')) {
                return null;
            }

            // Create a simple black and white pattern that looks QR-ish
            $image = imagecreate($size, $size);
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);

            // Fill with white background
            imagefill($image, 0, 0, $white);

            // Create a simple grid pattern
            $gridSize = max(5, intval($size / 25));
            for ($x = 0; $x < $size; $x += $gridSize) {
                for ($y = 0; $y < $size; $y += $gridSize) {
                    // Create a pseudo-random pattern based on position
                    if ((($x + $y) % ($gridSize * 3)) == 0 ||
                        (($x * 7 + $y * 11) % ($gridSize * 4)) == 0) {
                        imagefilledrectangle($image, $x, $y, $x + $gridSize - 1, $y + $gridSize - 1, $black);
                    }
                }
            }

            // Add corner squares (typical QR code feature)
            $cornerSize = intval($size / 7);
            $positions = [[0, 0], [$size - $cornerSize, 0], [0, $size - $cornerSize]];
            foreach ($positions as [$px, $py]) {
                imagefilledrectangle($image, $px, $py, $px + $cornerSize, $py + $cornerSize, $black);
                $innerSize = intval($cornerSize / 3);
                $innerPos = intval($cornerSize / 3);
                imagefilledrectangle($image, $px + $innerPos, $py + $innerPos,
                    $px + $cornerSize - $innerPos, $py + $cornerSize - $innerPos, $white);
            }

            // Capture output
            ob_start();
            imagepng($image);
            $imageData = ob_get_contents();
            ob_end_clean();
            imagedestroy($image);

            if ($imageData) {
                $base64 = base64_encode($imageData);
                \Log::info('Fallback QR image created', [
                    'asset_id' => $this->id,
                    'size' => $size,
                    'image_data_length' => strlen($imageData)
                ]);
                return 'data:image/png;base64,' . $base64;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create GD fallback image', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Create an HTML table-based QR pattern (DomPDF friendly)
     */
    private function createHtmlQrPattern($size): string
    {
        $cellSize = max(4, intval($size / 25)); // Each cell size
        $gridSize = intval($size / $cellSize);

        $html = '<table style="border-collapse:collapse;margin:0;padding:0;width:' . $size . 'px;height:' . $size . 'px;background:white;">';

        for ($row = 0; $row < $gridSize; $row++) {
            $html .= '<tr style="height:' . $cellSize . 'px;">';
            for ($col = 0; $col < $gridSize; $col++) {
                // Create a pseudo-random pattern based on position
                $isBlack = false;

                // Corner squares (typical QR code feature)
                if (($row < 7 && $col < 7) ||
                    ($row < 7 && $col >= $gridSize - 7) ||
                    ($row >= $gridSize - 7 && $col < 7)) {
                    $isBlack = ($row == 0 || $row == 6 || $col == 0 || $col == 6 ||
                               ($row >= 2 && $row <= 4 && $col >= 2 && $col <= 4));
                } else {
                    // Pseudo-random pattern for the rest
                    $isBlack = ((($row + $col) % 3) == 0) ||
                               ((($row * 7 + $col * 11) % 5) == 0);
                }

                $bgColor = $isBlack ? 'black' : 'white';
                $html .= '<td style="width:' . $cellSize . 'px;height:' . $cellSize . 'px;background:' . $bgColor . ';padding:0;border:0;"></td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html;
    }

    /**
     * Get asset display name for QR code
     */
    public function getAssetDisplayName(): string
    {
        return match (class_basename(static::class)) {
            'Vehicle' => "{$this->model} ({$this->plate_number})",
            'MeetingRoom' => $this->name,
            'ItAsset' => "{$this->name} ({$this->asset_tag})",
            default => $this->name ?? $this->model ?? 'Unknown Asset'
        };
    }

    /**
     * Get QR code logs for this asset
     */
    public function qrCodeLogs()
    {
        return QrCodeLog::forAsset(get_class($this), $this->id);
    }

    /**
     * Get recent QR code activity for this asset
     */
    public function getRecentQrActivity(int $limit = 10)
    {
        return QrCodeLog::getRecentActivityForAsset(get_class($this), $this->id, $limit);
    }

    /**
     * Get QR code usage statistics for this asset
     */
    public function getQrStatistics(int $days = 30): array
    {
        $logs = $this->qrCodeLogs()->where('created_at', '>=', now()->subDays($days));

        return [
            'total_scans' => $logs->scans()->count(),
            'successful_completions' => $logs->byAction('booking_completed')->count(),
            'failed_attempts' => $logs->byAction('scan_failed')->count(),
            'unique_users' => $logs->scans()->distinct('user_id')->count('user_id'),
            'last_scan' => $logs->scans()->latest()->first()?->created_at,
            'generation_count' => $logs->whereIn('action', ['generated', 'regenerated'])->count(),
        ];
    }
}