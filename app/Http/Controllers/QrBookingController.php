<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\MeetingRoom;
use App\Models\ItAsset;
use App\Models\QrCodeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class QrBookingController extends Controller
{
    /**
     * Handle QR code scan for booking completion
     */
    public function completeBooking(Request $request, string $type, string $identifier): View|RedirectResponse
    {
        // Check if user is authenticated, if not redirect to login with intended URL
        if (!Auth::check()) {
            // Encode the QR parameters to pass them through the auth flow
            $encodedParams = base64_encode(json_encode([
                'type' => $type,
                'identifier' => $identifier,
                'action' => 'qr_complete'
            ]));

            \Log::info('QR Code: Redirecting to login', [
                'type' => $type,
                'identifier' => $identifier,
                'encoded_params' => $encodedParams
            ]);

            // Store QR parameters directly in session with a persistent key
            $request->session()->put('qr_completion_params', [
                'type' => $type,
                'identifier' => $identifier,
                'action' => 'qr_complete'
            ]);

            \Log::info('QR Code: Stored params in session', [
                'session_key' => 'qr_completion_params',
                'session_id' => $request->session()->getId(),
                'params' => [
                    'type' => $type,
                    'identifier' => $identifier,
                    'action' => 'qr_complete'
                ]
            ]);

            return redirect()->route('login')
                ->with('info', 'Please log in to complete your booking.');
        }

        \Log::info('QR Code: User authenticated, processing booking', [
            'user_id' => Auth::id(),
            'type' => $type,
            'identifier' => $identifier
        ]);

        // Find the asset by QR code identifier
        $asset = $this->findAssetByIdentifier($type, $identifier);

        if (!$asset) {
            // Log failed scan attempt
            QrCodeLog::logAction(
                assetType: "App\\Models\\{$type}",
                assetId: 0, // Unknown asset ID
                qrIdentifier: $identifier,
                action: 'scan_failed',
                metadata: [
                    'error' => 'Asset not found',
                    'scanned_type' => $type,
                    'scanned_identifier' => $identifier,
                    'user_agent' => $request->userAgent(),
                ]
            );

            return redirect()->route('dashboard')
                ->with('error', 'Asset not found or invalid QR code.');
        }

        // Log successful QR scan
        QrCodeLog::logAction(
            assetType: get_class($asset),
            assetId: $asset->id,
            qrIdentifier: $identifier,
            action: 'scanned',
            metadata: [
                'asset_name' => $asset->getAssetDisplayName(),
                'scanned_type' => $type,
                'user_agent' => $request->userAgent(),
            ]
        );

        // Find all active bookings for this asset
        $allActiveBookings = $this->findAllActiveBookingsForAsset($asset);

        if ($allActiveBookings->isEmpty()) {
            return redirect()->route('dashboard')
                ->with('error', 'No active bookings found for this asset.');
        }

        // Filter bookings the current user can complete
        $userCompletableBookings = $allActiveBookings->filter(function ($booking) {
            return $this->canUserCompleteBooking($booking, Auth::id());
        });

        if ($userCompletableBookings->isEmpty()) {
            // Log failed scan - no permissions
            QrCodeLog::logAction(
                assetType: get_class($asset),
                assetId: $asset->id,
                qrIdentifier: $identifier,
                action: 'scan_failed',
                metadata: [
                    'error' => 'No permission to complete bookings',
                    'asset_name' => $asset->getAssetDisplayName(),
                    'total_active_bookings' => $allActiveBookings->count(),
                ]
            );

            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to complete any bookings for this asset.');
        }

        $activeBookings = $userCompletableBookings;

        // If there's only one booking, redirect to booking show page with auto-open modal
        if ($activeBookings->count() === 1) {
            $booking = $activeBookings->first();

            \Log::info('QR Code: Redirecting to booking show page', [
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'asset_type' => $type,
                'identifier' => $identifier
            ]);

            return redirect()->route('bookings.show.user', ['id' => $booking->id])
                ->with('auto_open_completion_modal', true)
                ->with('success', 'Please complete your booking by filling in the remarks.');
        }

        // If multiple bookings, show selection page
        return view('qr-booking.select-booking', [
            'asset' => $asset,
            'bookings' => $activeBookings
        ]);
    }

    /**
     * Handle post-login redirect for QR code scanning
     */
    public function handlePostLoginRedirect(string $encodedParams): RedirectResponse
    {
        try {
            $params = json_decode(base64_decode($encodedParams), true);

            \Log::info('QR Redirect: Processing post-login redirect', [
                'user_id' => Auth::id(),
                'params' => $params
            ]);

            if ($params && isset($params['action']) && $params['action'] === 'qr_complete') {
                return redirect()->route('booking.complete-qr', [
                    'type' => $params['type'],
                    'identifier' => $params['identifier']
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('QR Redirect: Failed to decode parameters', [
                'encoded_params' => $encodedParams,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('dashboard')
            ->with('error', 'Invalid QR code parameters.');
    }

    /**
     * Show booking completion form via QR
     */
    public function showCompletionForm(Booking $booking): View|RedirectResponse
    {
        // Verify user has permission to complete this booking
        if (!$this->canUserCompleteBooking($booking, Auth::id())) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to complete this booking.');
        }

        // Check if booking is in the right status
        if ($booking->status !== 'approved') {
            return redirect()->route('dashboard')
                ->with('error', 'This booking cannot be completed. Status: ' . $booking->status);
        }

        return view('qr-booking.complete-form', [
            'booking' => $booking,
            'asset' => $booking->asset
        ]);
    }

    /**
     * Process booking completion
     */
    public function processCompletion(Request $request, Booking $booking): RedirectResponse
    {
        // Verify user has permission to complete this booking
        if (!$this->canUserCompleteBooking($booking, Auth::id())) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to complete this booking.');
        }

        // Validate completion data based on asset type
        $validatedData = $this->validateCompletionData($request, $booking);

        // Update booking status and completion details
        $booking->update([
            'status' => 'done',
            'done_details' => $validatedData,
            'status_history' => array_merge($booking->status_history ?? [], [
                [
                    'status' => 'done',
                    'changed_at' => now()->toDateTimeString(),
                    'changed_by' => Auth::id(),
                    'method' => 'qr_scan'
                ]
            ])
        ]);

        // Log successful booking completion via QR
        QrCodeLog::logAction(
            assetType: $booking->asset_type,
            assetId: $booking->asset_id,
            qrIdentifier: $booking->asset->qr_code_identifier ?? 'unknown',
            action: 'booking_completed',
            bookingId: $booking->id,
            metadata: [
                'asset_name' => $booking->asset->getAssetDisplayName(),
                'booking_purpose' => $booking->purpose,
                'completion_method' => 'qr_scan',
                'done_details' => $validatedData,
            ]
        );

        return redirect()->route('dashboard')
            ->with('success', 'Booking completed successfully! Thank you for using the eBooking system.');
    }

    /**
     * Find asset by QR code identifier
     */
    private function findAssetByIdentifier(string $type, string $identifier)
    {
        return match ($type) {
            'Vehicle' => Vehicle::where('qr_code_identifier', $identifier)->first(),
            'MeetingRoom' => MeetingRoom::where('qr_code_identifier', $identifier)->first(),
            'ItAsset' => ItAsset::where('qr_code_identifier', $identifier)->first(),
            default => null
        };
    }

    /**
     * Find all active bookings for an asset (including future bookings)
     */
    private function findAllActiveBookingsForAsset($asset)
    {
        return Booking::where('asset_type', get_class($asset))
            ->where('asset_id', $asset->id)
            ->where('status', 'approved')
            ->where('end_time', '>=', now()->subHours(1)) // Allow 1 hour grace period after end time
            ->get();
    }

    /**
     * Find active bookings for an asset by user
     */
    private function findActiveBookingsForAsset($asset, int $userId)
    {
        return Booking::where('asset_type', get_class($asset))
            ->where('asset_id', $asset->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($userId) {
                $query->where('booked_by', $userId)
                    ->orWhereJsonContains('passengers', $userId);
            })
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now()->subHours(1)) // Allow 1 hour grace period
            ->get();
    }

    /**
     * Check if user can complete this booking
     */
    private function canUserCompleteBooking(Booking $booking, int $userId): bool
    {
        // User is the one who booked it
        if ($booking->booked_by === $userId) {
            return true;
        }

        // User is a passenger (for vehicle bookings)
        if ($booking->hasPassenger($userId)) {
            return true;
        }

        // Admin can complete any booking
        if (Auth::user()->hasRole(['Admin', 'Super Admin'])) {
            return true;
        }

        return false;
    }

    /**
     * Validate completion data based on asset type
     */
    private function validateCompletionData(Request $request, Booking $booking): array
    {
        $assetType = class_basename($booking->asset_type);

        return match ($assetType) {
            'Vehicle' => $this->validateVehicleCompletion($request),
            'MeetingRoom' => $this->validateMeetingRoomCompletion($request),
            'ItAsset' => $this->validateItAssetCompletion($request),
            default => []
        };
    }

    /**
     * Validate vehicle booking completion
     */
    private function validateVehicleCompletion(Request $request): array
    {
        return $request->validate([
            'odometer' => 'required|numeric|min:0',
            'gas_filled' => 'boolean',
            'gas_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:1000'
        ]);
    }

    /**
     * Validate meeting room booking completion
     */
    private function validateMeetingRoomCompletion(Request $request): array
    {
        return $request->validate([
            'remarks' => 'nullable|string|max:1000'
        ]);
    }

    /**
     * Validate IT asset booking completion
     */
    private function validateItAssetCompletion(Request $request): array
    {
        return $request->validate([
            'remarks' => 'nullable|string|max:1000'
        ]);
    }
}