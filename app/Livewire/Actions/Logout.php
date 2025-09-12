<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        try {
            // Get current user before logout (for any cleanup if needed)
            $user = Auth::user();
            
            // Logout the user
            Auth::guard('web')->logout();

            // Clear session data with error handling
            if (Session::isStarted()) {
                Session::invalidate();
                Session::regenerateToken();
            }

            // Force garbage collection to clean up session
            Session::save();
            
        } catch (\Exception $e) {
            // Log the error but continue with redirect
            \Log::error('Logout error: ' . $e->getMessage());
            
            // Force clear auth even if session fails
            Auth::forgetUser();
        }

        return redirect('/')->with('message', 'You have been logged out successfully.');
    }
}
