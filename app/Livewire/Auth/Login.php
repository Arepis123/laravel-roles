<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */     

    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Check if user is inactive or deleted after successful authentication
        $user = Auth::user();

        if ($user->deleted) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'This account has been deleted. Please contact an administrator.',
            ]);
        }

        if ($user->status !== 'active') {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Your account is inactive. Please contact an administrator to activate your account.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Set custom remember token duration if "remember me" was checked
        if ($this->remember) {
            $user = Auth::user();
            $rememberToken = Str::random(60);
            $user->forceFill([
                'remember_token' => hash('sha256', $rememberToken),
            ])->save();

            // Create a remember cookie with 1-month expiry (43200 minutes)
            Cookie::queue(
                Auth::guard()->getRecallerName(),
                $user->id . '|' . $rememberToken . '|' . $user->getAuthPassword(),
                43200 // 30 days in minutes
            );
        }

        // Check if we have QR completion parameters in session
        $qrParams = session('qr_completion_params');
        \Log::info('Login: Checking QR completion params', [
            'qr_params' => $qrParams,
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'request_url' => request()->fullUrl()
        ]);

        if ($qrParams && isset($qrParams['action']) && $qrParams['action'] === 'qr_complete') {
            session()->forget('qr_completion_params'); // Clear it after using

            $redirectUrl = route('booking.complete-qr', [
                'type' => $qrParams['type'],
                'identifier' => $qrParams['identifier']
            ]);

            \Log::info('Login: Redirecting to QR completion', [
                'redirect_url' => $redirectUrl,
                'params' => $qrParams
            ]);

            // Use Livewire redirect
            $this->redirect($redirectUrl, navigate: true);
            return;
        }

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }


    public function render()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('livewire.auth.login');
    }    

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
