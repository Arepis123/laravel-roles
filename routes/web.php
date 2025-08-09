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

Route::get('/', function () {
    return view('welcome');
})->name('home');

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
        if (auth()->user()->hasRole('Super Admin')) {
            return redirect()->route('bookings.index.admin');
        }
        return redirect()->route('bookings.index.user');
    })->name('bookings.index');

    // Admin route – sees all bookings
    Route::get('bookings/all', BookingIndex::class)->name('bookings.index.admin')->middleware('role:Super Admin');

    // User route – sees only own bookings
    Route::get('bookings/my', BookingMyIndex::class)->name('bookings.index.user')->middleware('role:Admin');

    // All roles can use create if they have permission
    Route::get('bookings/create', BookingCreate::class)->name('bookings.create')->middleware('permission:book.create');
    Route::get('bookings/{id}', BookingShow::class)->name('bookings.show')->middleware('permission:book.view');
    Route::get('bookings/{booking}/edit', BookingEdit::class)->name('bookings.edit')->middleware('permission:book.edit');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::prefix('api')->name('api.')->group(function () {
        Route::get('calendar-bookings', [App\Http\Controllers\Api\CalendarController::class, 'getBookings'])
             ->name('calendar.bookings');
        
        Route::get('calendar-stats', [App\Http\Controllers\Api\CalendarController::class, 'getStats'])
             ->name('calendar.stats');
    });    

Route::get('/inspect-booking/{bookingId}', function ($bookingId) {
    try {
        $booking = \App\Models\Booking::findOrFail($bookingId);
        
        return response()->json([
            'booking_data' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'booked_by' => $booking->booked_by,
                'status_history' => $booking->status_history,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at,
            ],
            'user_data' => $booking->bookedBy ? [
                'id' => $booking->bookedBy->id,
                'name' => $booking->bookedBy->name,
                'email' => $booking->bookedBy->email,
            ] : null,
            'relationship_test' => [
                'direct_find' => \App\Models\User::find($booking->booked_by)?->email,
                'via_relationship' => $booking->bookedBy?->email,
                'relationship_exists' => method_exists($booking, 'bookedBy'),
            ],
            'current_auth' => [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name,
                'is_same_user' => auth()->id() === $booking->booked_by,
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('inspect.booking');    

Route::get('/test-booking-email/{bookingId}', function ($bookingId) {
    try {
        // Find the booking
        $booking = \App\Models\Booking::findOrFail($bookingId);
        
        // Get the booking owner
        $user = $booking->bookedBy;
        
        if (!$user) {
            return response()->json([
                'error' => 'No user found for this booking',
                'booking_id' => $bookingId,
                'booked_by' => $booking->booked_by
            ]);
        }
        
        \Log::info('Testing email for booking: ' . $bookingId);
        \Log::info('User email: ' . $user->email);
        
        // Send the notification
        $user->notify(new \App\Notifications\BookingStatusChanged(
            $booking,
            'pending',
            'approved',
            'Test Admin'
        ));
        
        return response()->json([
            'success' => true,
            'message' => 'Test email sent successfully',
            'booking_id' => $bookingId,
            'user_email' => $user->email,
            'user_name' => $user->name
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Test email failed: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Failed to send test email',
            'message' => $e->getMessage(),
            'booking_id' => $bookingId
        ]);
    }
})->name('test.booking.email');

// Simple mail test
Route::get('/test-mail', function () {
    try {
        \Mail::raw('This is a test email from ' . config('app.name'), function ($message) {
            $message->to(auth()->user()->email ?? 'arepis123@gmail.com')
                   ->subject('Test Email - ' . now());
        });
        
        return 'Test email sent! Check your inbox and spam folder.';
    } catch (\Exception $e) {
        return 'Mail test failed: ' . $e->getMessage();
    }
})->name('test.mail');    
});


require __DIR__.'/auth.php';
