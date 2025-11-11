<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Livewire\Users\UserIndex;
use App\Livewire\Users\UserCreate;
use App\Livewire\Users\UserEdit;
use App\Livewire\Users\UseShow;
use App\Livewire\Roles\RoleIndex;
use App\Livewire\Roles\RoleCreate;
use App\Livewire\Roles\RoleEdit;
use App\Livewire\Roles\RoleShow;
use App\Livewire\Bookings\BookingIndex;      // Admin: all bookings  
use App\Livewire\Bookings\BookingCreate;
use App\Livewire\Bookings\BookingShow;
use App\Livewire\Bookings\BookingEdit;
use App\Livewire\Bookings\BookingMyIndex;    // User: own bookings
use App\Livewire\Bookings\BookingMyEdit;
use App\Livewire\Bookings\BookingMyShow;
use App\Livewire\Admin\AssetManagement;
use App\Livewire\Admin\Reports;
use App\Livewire\VehicleFuelManagement;
use App\Livewire\VehicleOdometerManagement;
use App\Livewire\VehicleMaintenanceManagement;
use App\Livewire\VehicleCheckupManagement;
use App\Livewire\CheckupTemplateManagement;
use App\Livewire\VehicleAnalytics;
use App\Livewire\VehicleDetail;
use App\Models\ReportLog;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/chart-data', [App\Http\Controllers\Api\CalendarController::class, 'getChartData'])
    ->middleware(['auth'])
    ->name('dashboard.chart-data');

Route::get('/dashboard/booking-status-data', [App\Http\Controllers\Api\CalendarController::class, 'getBookingStatusData'])
    ->middleware(['auth'])
    ->name('dashboard.booking-status-data');

Route::get('/user-manual', function () {
    return response()->file(public_path('user-manual.html'));
});

// Demo route for session expired page
Route::get('/session-expired-demo', function () {
    return response()->view('errors.419', [], 419);
});

Route::get('/process-flow', function () {
    return response()->file(public_path('process-flow.html'));
});

// Test routes outside auth middleware for debugging
Route::get('/reports/test/{report}', function (ReportLog $report) {
    return response()->json([
        'report_id' => $report->id,
        'file_name' => $report->file_name,
        'file_path' => $report->file_path,
        'format' => $report->report_format,
        'full_path' => storage_path('app/' . $report->file_path),
        'file_exists' => file_exists(storage_path('app/' . $report->file_path)),
    ]);
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('users', UserIndex::class)->name('users.index')->middleware('permission:user.view|user.create|user.edit|user.delete');
    Route::get('users/create', UserCreate::class)->name('users.create')->middleware('permission:user.create');
    Route::get('users/{id}/edit', UserEdit::class)->name('users.edit')->middleware('permission:user.edit');
    Route::get('users/{id}', UseShow::class)->name('users.show')->middleware('permission:user.view');

    Route::get('roles', RoleIndex::class)->name('roles.index')->middleware('permission:role.view|role.create|role.edit|role.delete');
    Route::get('roles/create', RoleCreate::class)->name('roles.create')->middleware('permission:role.create');
    Route::get('roles/{id}/edit', RoleEdit::class)->name('roles.edit')->middleware('permission:role.edit');
    Route::get('roles/{id}', RoleShow::class)->name('roles.show')->middleware('permission:role.view');

    Route::get('bookings', function () {
        if (auth()->user()->hasAnyRole(['Super Admin','Admin'])) {
            return redirect()->route('bookings.index.admin');
        }
        return redirect()->route('bookings.index.user');
    })->name('bookings.index');

    // Admin route – sees all bookings
    Route::get('bookings/all', BookingIndex::class)->name('bookings.index.admin')->middleware('role:Super Admin|Admin');

    // User route – sees only own bookings
    Route::get('bookings/my', BookingMyIndex::class)->name('bookings.index.user');
    Route::get('bookings/my/{booking}/edit', BookingMyEdit::class)->name('bookings.edit.user');
    Route::get('bookings/my/{id}', BookingMyShow::class)->name('bookings.show.user');

    // All roles can use create if they have permission
    Route::get('bookings/create', BookingCreate::class)->name('bookings.create')->middleware('permission:book.create');
    Route::get('bookings/{id}', BookingShow::class)->name('bookings.show')->middleware('permission:book.view');
    Route::get('bookings/{booking}/edit', BookingEdit::class)->name('bookings.edit')->middleware('permission:book.edit');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('assets/assets', AssetManagement::class)->name('assets')->middleware('permission:asset.view|asset.create|asset.edit|asset.delete');
    Route::get('assets/qr-codes', \App\Livewire\Admin\QrCodeManagement::class)->name('assets.qr-codes')->middleware('permission:asset.view|asset.create|asset.edit|asset.delete');
    
    // Vehicle Management Routes
    Route::get('vehicles/fuel', VehicleFuelManagement::class)->name('vehicles.fuel')->middleware('permission:vehicle.view|vehicle.create|vehicle.edit|vehicle.delete');
    Route::get('vehicles/odometer', VehicleOdometerManagement::class)->name('vehicles.odometer')->middleware('permission:vehicle.view|vehicle.create|vehicle.edit|vehicle.delete');
    Route::get('vehicles/maintenance', VehicleMaintenanceManagement::class)->name('vehicles.maintenance')->middleware('permission:vehicle.view|vehicle.create|vehicle.edit|vehicle.delete');
    Route::get('vehicles/checkup', VehicleCheckupManagement::class)->name('vehicles.checkup')->middleware('permission:vehicle.view|vehicle.create|vehicle.edit|vehicle.delete');
    Route::get('vehicles/checkup-templates', CheckupTemplateManagement::class)->name('vehicles.checkup-templates')->middleware('permission:vehicle.view|vehicle.create|vehicle.edit|vehicle.delete');
    Route::get('vehicles/analytics', VehicleAnalytics::class)->name('vehicles.analytics')->middleware('permission:vehicle.view|vehicle.create|vehicle.edit|vehicle.delete');
    Route::get('vehicle-analytics/export', [App\Http\Controllers\VehicleAnalyticsController::class, 'export'])->name('vehicle.analytics.export')->middleware('permission:vehicle.view');
    Route::get('vehicle-checkup/export', [App\Http\Controllers\VehicleCheckupExportController::class, 'export'])->name('vehicle.checkup.export')->middleware('permission:vehicle.view');
    
    // Test route for PDF debugging
    Route::get('test-pdf', function() {
        try {
            $pdf = Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test PDF</h1><p>This is a test PDF file.</p>');
            return $pdf->download('test.pdf');
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    })->middleware('auth');
    Route::get('vehicles/{vehicleId}/detail', VehicleDetail::class)->name('vehicles.detail')->middleware('permission:asset.view|asset.create|asset.edit|asset.delete');
    
    Route::prefix('api')->name('api.')->group(function () {
        // Original calendar endpoints
        Route::get('calendar-bookings', [App\Http\Controllers\Api\CalendarController::class, 'getBookings'])
             ->name('calendar.bookings');
        
        Route::get('calendar-stats', [App\Http\Controllers\Api\CalendarController::class, 'getStats'])
             ->name('calendar.stats');
        
        Route::get('chart-data', [App\Http\Controllers\Api\CalendarController::class, 'getChartData'])
             ->name('chart.data');
             
        // Enhanced calendar endpoints with vehicle management integration
        Route::get('calendar-enhanced', [App\Http\Controllers\Api\CalendarController::class, 'getEnhancedBookings'])
             ->name('calendar.enhanced');
        
        // Vehicle management calendar endpoints (Admin/Super Admin only)
        Route::middleware(['role:Admin|Super Admin'])->group(function () {
            Route::get('calendar-vehicles', [App\Http\Controllers\Api\CalendarController::class, 'getVehicleEvents'])
                 ->name('calendar.vehicles');
                 
            Route::get('calendar-vehicle-stats', [App\Http\Controllers\Api\CalendarController::class, 'getVehicleStats'])
                 ->name('calendar.vehicle-stats');
        });
    }); 

    // Reports page
    Route::get('reports/reports', Reports::class)->name('reports')->middleware('permission:asset.view|asset.create|asset.edit|asset.delete');

    // Reminder Settings (Super Admin only)
    Route::get('admin/reminder-settings', \App\Livewire\Admin\ReminderSettings::class)->name('admin.reminder-settings')->middleware('role:Super Admin');

    // Announcement Management (Super Admin only)
    Route::get('admin/announcements', \App\Livewire\Admin\AnnouncementManagement::class)->name('admin.announcements')->middleware('role:Super Admin');

    // Announcement dismiss route - API endpoint for dismissing announcements
    Route::post('api/announcements/{id}/dismiss', function ($id) {
        $announcement = \App\Models\Announcement::find($id);
        if ($announcement && auth()->check()) {
            $announcement->markAsReadBy(auth()->user());
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    })->name('api.announcements.dismiss');

    // Secure announcement image serving
    Route::get('secure/announcements/images/{imagePath}', [App\Http\Controllers\AnnouncementImageController::class, 'show'])
        ->name('announcements.image.show');

    // Report view route - View report in browser  
    Route::get('/reports/view/{id}', function ($id) {
        try {
            $report = ReportLog::findOrFail($id);
            
            \Log::info('Route view attempt', [
                'report_id' => $report->id,
                'file_path' => $report->file_path,
                'file_name' => $report->file_name,
                'format' => $report->report_format
            ]);
            
            $fullPath = storage_path('app/' . $report->file_path);
            \Log::info('Full file path', ['full_path' => $fullPath, 'exists' => file_exists($fullPath)]);
            
            if (!file_exists($fullPath)) {
                \Log::error('Physical file does not exist', ['full_path' => $fullPath]);
                return response('<h1>Error</h1><p>Report file not found: ' . htmlspecialchars($report->file_name) . '</p>', 404);
            }
            
            \Log::info('File exists, checking format and returning response');
            
            // Get file contents for debugging
            $fileSize = filesize($fullPath);
            \Log::info('File info', ['size' => $fileSize, 'format' => $report->report_format]);
            
            // Handle different formats for viewing
            $mimeType = match($report->report_format) {
                'pdf' => 'application/pdf',
                'csv' => 'text/csv', 
                'json' => 'application/json',
                'xml' => 'application/xml',
                'html' => 'text/html',
                'txt' => 'text/plain',
                'excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                default => 'application/octet-stream'
            };
            
            \Log::info('About to return file response', ['mime_type' => $mimeType]);
            
            // For formats that can be viewed in browser, display inline
            if (in_array($report->report_format, ['html', 'json', 'xml', 'txt', 'csv', 'pdf'])) {
                return response()->file($fullPath, [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $report->file_name . '"'
                ]);
            } else {
                // For other formats, still download
                return response()->download($fullPath, $report->file_name, [
                    'Content-Type' => $mimeType
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Route view error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response('<h1>Error</h1><p>Error viewing file: ' . htmlspecialchars($e->getMessage()) . '</p>', 500);
        }
    })->name('reports.view');

    // Report download route - ADD THIS NEW ROUTE
    Route::get('/reports/download/{report}', function (ReportLog $report) {
        try {
            \Log::info('Route download attempt', [
                'report_id' => $report->id,
                'file_path' => $report->file_path,
                'file_name' => $report->file_name
            ]);
            
            if (!Storage::exists($report->file_path)) {
                \Log::error('File not found in storage', ['file_path' => $report->file_path]);
                abort(404, 'Report file not found.');
            }
            
            $fullPath = storage_path('app/' . $report->file_path);
            \Log::info('Full file path', ['full_path' => $fullPath]);
            
            if (!file_exists($fullPath)) {
                \Log::error('Physical file does not exist', ['full_path' => $fullPath]);
                abort(404, 'Report file does not exist on disk.');
            }
            
            \Log::info('File exists, returning download response');
            
            return response()->download($fullPath, $report->file_name, [
                'Content-Type' => match($report->report_format) {
                    'pdf' => 'application/pdf',
                    'csv' => 'text/csv', 
                    'json' => 'application/json',
                    'xml' => 'application/xml',
                    'html' => 'text/html',
                    'txt' => 'text/plain',
                    'excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    default => 'application/octet-stream'
                }
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Route download error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            abort(500, 'Error downloading file: ' . $e->getMessage());
        }
    })->name('reports.download');

});

// QR Code Booking Routes (Outside auth middleware so users can scan without logging in first)
Route::get('/qr-booking/complete/{type}/{identifier}', [App\Http\Controllers\QrBookingController::class, 'completeBooking'])
    ->name('booking.complete-qr');

// QR redirect now handled via session parameters in Login component



// QR completion now uses existing booking show workflow
// Old completion form routes removed - now redirects to booking-my-show with modal auto-open

require __DIR__.'/auth.php';
