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

    // Route::get('bookings', BookingIndex::class)->name('bookings.index')->middleware('permission:book.view|book.create|book.edit|book.delete');
    // Route::get('bookings/create', BookingCreate::class)->name('bookings.create')->middleware('permission:book.create');   

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

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
