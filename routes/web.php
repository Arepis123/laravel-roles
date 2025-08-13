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
use App\Livewire\Admin\AssetManagement;
use App\Livewire\Admin\Reports;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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

    // All roles can use create if they have permission
    Route::get('bookings/create', BookingCreate::class)->name('bookings.create')->middleware('permission:book.create');
    Route::get('bookings/{id}', BookingShow::class)->name('bookings.show')->middleware('permission:book.view');
    Route::get('bookings/{booking}/edit', BookingEdit::class)->name('bookings.edit')->middleware('permission:book.edit');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('assets/assets', AssetManagement::class)->name('assets')->middleware('permission:asset.view|asset.create|asset.edit|asset.delete');
    
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('calendar-bookings', [App\Http\Controllers\Api\CalendarController::class, 'getBookings'])
             ->name('calendar.bookings');
        
        Route::get('calendar-stats', [App\Http\Controllers\Api\CalendarController::class, 'getStats'])
             ->name('calendar.stats');
    }); 

    // Reports page
    Route::get('reports/reports', Reports::class)->name('reports')->middleware('permission:asset.view|asset.create|asset.edit|asset.delete');
    
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


require __DIR__.'/auth.php';
