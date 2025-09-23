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
     * Generate QR code PNG as base64 (requires imagick extension)
     */
    public function getQrCodePng($size = 200): string
    {
        return QrCode::format('png')->size($size)->generate($this->getQrCodeUrl());
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