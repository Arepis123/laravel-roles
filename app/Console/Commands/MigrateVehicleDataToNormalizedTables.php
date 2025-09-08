<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\VehicleFuelLog;
use App\Models\VehicleOdometerLog;
use App\Models\User;

class MigrateVehicleDataToNormalizedTables extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'vehicle:migrate-data 
                          {--dry-run : Show what would be migrated without actually doing it}
                          {--force : Force migration even if data already exists}';

    /**
     * The console command description.
     */
    protected $description = 'Migrate vehicle fuel and odometer data from booking done_details JSON to normalized tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🚗 Starting Vehicle Data Migration');
        $this->info('=====================================');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No data will be actually migrated');
        }

        // Check if done_details column exists
        if (!\Schema::hasColumn('bookings', 'done_details')) {
            $this->error('❌ done_details column does not exist in bookings table. Migration not needed.');
            return 1;
        }

        // Get all completed vehicle bookings with done_details
        $bookings = Booking::where('asset_type', Vehicle::class)
            ->where('status', 'done')
            ->whereNotNull('done_details')
            ->with(['asset', 'user'])
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('✅ No vehicle bookings with done_details found. Migration complete.');
            return 0;
        }

        $this->info("📊 Found {$bookings->count()} vehicle bookings with done_details to migrate");

        // Check if data already exists in new tables
        if (!$force) {
            $existingFuelLogs = VehicleFuelLog::count();
            $existingOdometerLogs = VehicleOdometerLog::count();
            
            if ($existingFuelLogs > 0 || $existingOdometerLogs > 0) {
                $this->warn("⚠️  Found existing data in normalized tables:");
                $this->warn("   - Fuel logs: {$existingFuelLogs}");
                $this->warn("   - Odometer logs: {$existingOdometerLogs}");
                
                if (!$this->confirm('Continue migration? This may create duplicates.')) {
                    $this->info('❌ Migration cancelled by user.');
                    return 0;
                }
            }
        }

        $migratedFuel = 0;
        $migratedOdometer = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($bookings->count());
        $progressBar->start();

        foreach ($bookings as $booking) {
            try {
                $doneDetails = $booking->done_details;
                $vehicle = $booking->asset;
                $user = $booking->user ?: User::first(); // Fallback to first user if not found

                if (!$vehicle || !$user) {
                    $this->newLine();
                    $this->warn("⚠️  Skipping booking {$booking->id} - missing vehicle or user");
                    $errors++;
                    continue;
                }

                // Migrate fuel data
                if (isset($doneDetails['gas_filled']) && $doneDetails['gas_filled'] && 
                    isset($doneDetails['gas_amount']) && $doneDetails['gas_amount'] > 0) {
                    
                    $fuelData = [
                        'booking_id' => $booking->id,
                        'vehicle_id' => $vehicle->id,
                        'fuel_amount' => (float) $doneDetails['gas_amount'],
                        'fuel_type' => 'petrol', // Default, can be updated later
                        'fuel_cost' => null, // Not available in old data
                        'fuel_station' => null, // Not available in old data
                        'odometer_at_fill' => isset($doneDetails['odometer']) ? (int) $doneDetails['odometer'] : null,
                        'filled_by' => $user->id,
                        'filled_at' => $booking->end_time ?: $booking->updated_at,
                        'notes' => 'Migrated from booking done_details',
                    ];

                    if (!$dryRun) {
                        VehicleFuelLog::create($fuelData);
                    }
                    $migratedFuel++;
                }

                // Migrate odometer data
                if (isset($doneDetails['odometer']) && $doneDetails['odometer'] > 0) {
                    $odometerData = [
                        'booking_id' => $booking->id,
                        'vehicle_id' => $vehicle->id,
                        'odometer_reading' => (int) $doneDetails['odometer'],
                        'reading_type' => 'end', // Assuming this was an end reading
                        'distance_traveled' => null, // Will be calculated by model
                        'recorded_by' => $user->id,
                        'recorded_at' => $booking->end_time ?: $booking->updated_at,
                        'notes' => 'Migrated from booking done_details',
                    ];

                    if (!$dryRun) {
                        VehicleOdometerLog::create($odometerData);
                    }
                    $migratedOdometer++;
                }

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("❌ Error migrating booking {$booking->id}: " . $e->getMessage());
                $errors++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('🎉 Migration Summary:');
        $this->info('===================');
        $this->info("📊 Total bookings processed: {$bookings->count()}");
        $this->info("⛽ Fuel logs " . ($dryRun ? 'would be' : '') . " migrated: {$migratedFuel}");
        $this->info("📏 Odometer logs " . ($dryRun ? 'would be' : '') . " migrated: {$migratedOdometer}");
        
        if ($errors > 0) {
            $this->warn("⚠️  Errors encountered: {$errors}");
        }

        if (!$dryRun) {
            $this->info('✅ Migration completed successfully!');
            $this->info('');
            $this->info('💡 Next steps:');
            $this->info('   1. Verify migrated data: php artisan tinker');
            $this->info('   2. Generate test reports to ensure everything works');
            $this->info('   3. Consider updating booking completion forms to use new tables');
        } else {
            $this->info('');
            $this->info('💡 To perform actual migration, run:');
            $this->info('   php artisan vehicle:migrate-data');
        }

        return 0;
    }
}