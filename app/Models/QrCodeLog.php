<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class QrCodeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_type',
        'asset_id',
        'qr_identifier',
        'action',
        'user_id',
        'ip_address',
        'user_agent',
        'booking_id',
        'metadata',
        'scanned_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'scanned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related booking if applicable
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the asset that this QR code belongs to
     */
    public function asset()
    {
        return match($this->asset_type) {
            'App\Models\Vehicle' => Vehicle::find($this->asset_id),
            'App\Models\MeetingRoom' => MeetingRoom::find($this->asset_id),
            'App\Models\ItAsset' => ItAsset::find($this->asset_id),
            default => null
        };
    }

    /**
     * Scope to get logs for a specific asset
     */
    public function scopeForAsset($query, $assetType, $assetId)
    {
        return $query->where('asset_type', $assetType)->where('asset_id', $assetId);
    }

    /**
     * Scope to get logs by action type
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get recent logs
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get scan-related logs only
     */
    public function scopeScans($query)
    {
        return $query->whereIn('action', ['scanned', 'booking_completed', 'scan_failed']);
    }

    /**
     * Log a QR code action
     */
    public static function logAction(
        string $assetType,
        int $assetId,
        string $qrIdentifier,
        string $action,
        ?int $userId = null,
        ?string $bookingId = null,
        array $metadata = []
    ): self {
        $request = request();

        return self::create([
            'asset_type' => $assetType,
            'asset_id' => $assetId,
            'qr_identifier' => $qrIdentifier,
            'action' => $action,
            'user_id' => $userId ?? auth()->id(),
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
            'booking_id' => $bookingId,
            'metadata' => $metadata,
            'scanned_at' => in_array($action, ['scanned', 'booking_completed']) ? now() : null,
        ]);
    }

    /**
     * Get analytics data for QR code usage
     */
    public static function getAnalytics(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return [
            'total_scans' => self::scans()->where('created_at', '>=', $startDate)->count(),
            'unique_users' => self::scans()->where('created_at', '>=', $startDate)->distinct('user_id')->count('user_id'),
            'successful_completions' => self::byAction('booking_completed')->where('created_at', '>=', $startDate)->count(),
            'failed_attempts' => self::byAction('scan_failed')->where('created_at', '>=', $startDate)->count(),
            'daily_scans' => self::scans()
                ->where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray(),
            'top_assets' => self::scans()
                ->where('created_at', '>=', $startDate)
                ->selectRaw('asset_type, asset_id, COUNT(*) as scan_count')
                ->groupBy('asset_type', 'asset_id')
                ->orderByDesc('scan_count')
                ->take(10)
                ->get()
                ->toArray(),
            'peak_hours' => self::scans()
                ->where('created_at', '>=', $startDate)
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderByDesc('count')
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'hour' => str_pad($item->hour, 2, '0', STR_PAD_LEFT),
                        'count' => $item->count
                    ];
                })
                ->toArray(),
        ];
    }

    /**
     * Get recent activity for an asset
     */
    public static function getRecentActivityForAsset(string $assetType, int $assetId, int $limit = 10)
    {
        return self::forAsset($assetType, $assetId)
            ->with('user')
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();
    }

    /**
     * Get asset display name for logging
     */
    public function getAssetDisplayName(): string
    {
        $asset = $this->asset();
        if (!$asset) {
            return 'Unknown Asset';
        }

        return method_exists($asset, 'getAssetDisplayName')
            ? $asset->getAssetDisplayName()
            : ($asset->name ?? $asset->model ?? 'Unknown Asset');
    }

    /**
     * Format action for display
     */
    public function getFormattedAction(): string
    {
        return match($this->action) {
            'generated' => 'QR Code Generated',
            'regenerated' => 'QR Code Regenerated',
            'scanned' => 'QR Code Scanned',
            'booking_completed' => 'Booking Completed via QR',
            'scan_failed' => 'QR Scan Failed',
            default => ucfirst(str_replace('_', ' ', $this->action))
        };
    }
}