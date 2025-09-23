<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;

class TestVehicleQr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-vehicle-qr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test vehicle QR code generation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Vehicle QR Code Generation...');

        $vehicle = Vehicle::first();
        if (!$vehicle) {
            $this->error('No vehicles found in database');
            return;
        }

        $this->info("Vehicle: {$vehicle->model} ({$vehicle->plate_number})");
        $this->info("Current QR Identifier: " . ($vehicle->qr_code_identifier ?? 'NULL'));

        // Generate QR identifier
        $identifier = $vehicle->generateQrCodeIdentifier();
        $this->info("Generated QR Identifier: {$identifier}");

        // Test URL generation
        $url = $vehicle->getQrCodeUrl();
        $this->info("QR URL: {$url}");

        // Test finding vehicle by identifier
        $foundVehicle = Vehicle::where('qr_code_identifier', $identifier)->first();
        if ($foundVehicle) {
            $this->info("✓ Vehicle found by identifier: {$foundVehicle->model}");
        } else {
            $this->error("✗ Vehicle NOT found by identifier");
        }

        // Test the controller method logic
        $type = class_basename($vehicle);
        $this->info("Class basename (type): {$type}");

        $testFound = match ($type) {
            'Vehicle' => Vehicle::where('qr_code_identifier', $identifier)->first(),
            'MeetingRoom' => null,
            'ItAsset' => null,
            default => null
        };

        if ($testFound) {
            $this->info("✓ Controller logic test passed");
        } else {
            $this->error("✗ Controller logic test failed");
        }

        // Check for bookings
        $bookings = \App\Models\Booking::where('asset_type', get_class($vehicle))
            ->where('asset_id', $vehicle->id)
            ->get();

        $this->info("Total bookings for this vehicle: " . $bookings->count());

        $activeBookings = $bookings->whereIn('status', ['approved', 'pending']);
        $this->info("Active bookings: " . $activeBookings->count());

        // Check for bookings available for QR completion (future bookings now included)
        $qrCompletableBookings = $bookings->where('status', 'approved')
            ->where('end_time', '>=', now()->subHours(1));
        $this->info("QR completable bookings: " . $qrCompletableBookings->count());

        if ($qrCompletableBookings->count() > 0) {
            foreach ($qrCompletableBookings as $booking) {
                $timeStatus = $booking->start_time > now() ? 'FUTURE' :
                             ($booking->end_time < now() ? 'PAST' : 'CURRENT');
                $this->info("  - Booking ID {$booking->id}: {$booking->status} [{$timeStatus}] ({$booking->start_time} to {$booking->end_time})");
            }
        } else {
            // Create multiple test bookings to demonstrate selection workflow
            $this->info("Creating multiple test bookings...");

            $user = \App\Models\User::first();
            if ($user) {
                // Create past booking (within grace period)
                $pastBooking = \App\Models\Booking::create([
                    'user_id' => $user->id,
                    'booked_by' => $user->id,
                    'asset_type' => get_class($vehicle),
                    'asset_id' => $vehicle->id,
                    'start_time' => now()->subHours(2),
                    'end_time' => now()->subMinutes(30), // 30 min ago (within 1hr grace period)
                    'purpose' => 'Past QR Test Booking - Forgot to complete',
                    'status' => 'approved'
                ]);

                // Create current booking
                $currentBooking = \App\Models\Booking::create([
                    'user_id' => $user->id,
                    'booked_by' => $user->id,
                    'asset_type' => get_class($vehicle),
                    'asset_id' => $vehicle->id,
                    'start_time' => now()->subMinutes(15),
                    'end_time' => now()->addMinutes(45),
                    'purpose' => 'Current QR Test Booking - In progress',
                    'status' => 'approved'
                ]);

                // Create future booking
                $futureBooking = \App\Models\Booking::create([
                    'user_id' => $user->id,
                    'booked_by' => $user->id,
                    'asset_type' => get_class($vehicle),
                    'asset_id' => $vehicle->id,
                    'start_time' => now()->addMinutes(30),
                    'end_time' => now()->addHours(2),
                    'purpose' => 'Future QR Test Booking - Starting soon',
                    'status' => 'approved'
                ]);

                $this->info("✓ Multiple test bookings created:");
                $this->info("  - Past booking: ID {$pastBooking->id} (PAST - within grace period)");
                $this->info("  - Current booking: ID {$currentBooking->id} (CURRENT - in progress)");
                $this->info("  - Future booking: ID {$futureBooking->id} (FUTURE - starts soon)");
                $this->info("  User: {$user->name} ({$user->email})");
                $this->info("  QR URL to test: {$url}");
                $this->info("");
                $this->info("💡 When you scan this QR code, you should see a SELECTION PAGE");
                $this->info("   showing all 3 bookings for the user to choose from!");
            }
        }
    }
}
