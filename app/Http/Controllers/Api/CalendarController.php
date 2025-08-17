<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Get bookings formatted for FullCalendar
     */
    public function getBookings(Request $request)
    {
        $query = Booking::with(['bookedBy', 'asset']);

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by asset type if provided
        if ($request->has('asset_type') && $request->asset_type !== 'all') {
            $query->where('asset_type', 'like', '%' . $request->asset_type . '%');
        }

        // Filter by date range if provided (FullCalendar sends start/end dates)
        if ($request->has('start') && $request->has('end')) {
            $start = Carbon::parse($request->start);
            $end = Carbon::parse($request->end);
            
            $query->where(function($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhereBetween('end_time', [$start, $end])
                  ->orWhere(function($subQ) use ($start, $end) {
                      $subQ->where('start_time', '<=', $start)
                           ->where('end_time', '>=', $end);
                  });
            });
        }

        $bookings = $query->orderBy('start_time')->get();

        // Transform bookings to FullCalendar event format
        $events = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => $this->generateEventTitle($booking),
                'start' => $booking->start_time->toISOString(),
                'end' => $booking->end_time->toISOString(),
                'backgroundColor' => $this->getStatusColor($booking->status),
                'borderColor' => $this->getStatusBorderColor($booking->status),
                'textColor' => $this->getTextColor($booking->status),
                'extendedProps' => [
                    'bookingId' => $booking->id,
                    'status' => $booking->status,
                    'assetType' => class_basename($booking->asset_type),
                    'assetTypeLabel' => $booking->asset_type_label,
                    'bookedBy' => $booking->bookedBy?->name ?? 'Unknown User',
                    'bookedByEmail' => $booking->bookedBy?->email ?? '',
                    'purpose' => $booking->purpose,
                    'capacity' => $booking->capacity,
                    'additionalBooking' => $booking->additional_booking,
                    'refreshmentDetails' => $booking->refreshment_details,
                    'statusHistory' => $booking->status_history,
                    'timeRange' => $this->formatTimeRange($booking->start_time, $booking->end_time),
                    'createdAt' => $booking->created_at->format('M j, Y g:i A'),
                    'isUpcoming' => $booking->isUpcoming(),
                    'isActive' => $booking->isActive(),
                    'isPast' => $booking->isPast(),
                    'assetName' => $booking->asset->name,
                    'assetModel' => $booking->asset->model, // for vehicles
                    'assetPlateNumber' => $booking->asset->plate_number, // for vehicles
                    'assetTag' => $booking->asset->asset_tag, // for IT assets                    
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::pending()->count(),
            'approved' => Booking::approved()->count(),
            'rejected' => Booking::rejected()->count(),
            'cancelled' => Booking::cancelled()->count(),
            'done' => Booking::done()->count(),
            'today_total' => Booking::whereDate('start_time', today())->count(),
            'today_approved' => Booking::whereDate('start_time', today())->approved()->count(),
            'upcoming' => Booking::approved()->where('start_time', '>', now())->count(),
            'active' => Booking::approved()
                              ->where('start_time', '<=', now())
                              ->where('end_time', '>=', now())
                              ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Generate event title for calendar display
     */
    private function generateEventTitle($booking)
    {
        $assetName = $booking->asset ? 
            (method_exists($booking->asset, 'name') ? $booking->asset->name : 
            (method_exists($booking->asset, 'title') ? $booking->asset->title : 'Asset')) : 
            'Unknown Asset';

        $userName = $booking->bookedBy?->name ?? 'Unknown';
        
        return "{$assetName} - {$userName}";
    }

    /**
     * Format time range for display
     */
    private function formatTimeRange($startTime, $endTime)
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        
        if ($start->isSameDay($end)) {
            return $start->format('M j, Y') . ' (' . $start->format('g:i A') . ' - ' . $end->format('g:i A') . ')';
        } else {
            return $start->format('M j, Y g:i A') . ' - ' . $end->format('M j, Y g:i A');
        }
    }

    /**
     * Get background color based on status
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'pending' => '#fbbf24',     // yellow-400
            'approved' => '#10b981',    // green-500
            'rejected' => '#ef4444',    // red-500
            'cancelled' => '#6b7280',   // gray-500
            'done' => '#3b82f6',        // blue-500
            default => '#9ca3af'        // gray-400
        };
    }

    /**
     * Get border color based on status
     */
    private function getStatusBorderColor($status)
    {
        return match($status) {
            'pending' => '#f59e0b',     // yellow-500
            'approved' => '#059669',    // green-600
            'rejected' => '#dc2626',    // red-600
            'cancelled' => '#4b5563',   // gray-600
            'done' => '#2563eb',        // blue-600
            default => '#6b7280'        // gray-500
        };
    }

    /**
     * Get text color based on status
     */
    private function getTextColor($status)
    {
        return match($status) {
            'pending' => '#000000',     // black text for yellow background
            'approved' => '#ffffff',    // white text
            'rejected' => '#ffffff',    // white text
            'cancelled' => '#ffffff',   // white text
            'done' => '#ffffff',        // white text
            default => '#ffffff'        // white text
        };
    }
}