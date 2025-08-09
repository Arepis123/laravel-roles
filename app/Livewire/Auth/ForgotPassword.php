<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ForgotPassword extends Component
{
    public string $email = '';
    public bool $loading = false;

    protected $rules = [
        'email' => ['required', 'string', 'email', 'max:255'],
    ];

    public function sendPasswordResetLink(): void
    {
        $this->loading = true;
        
        $this->validate();

        // Send the reset link
        $status = Password::sendResetLink($this->only('email'));

        $this->loading = false;

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', __('A password reset link has been sent to your email address.'));
            $this->reset('email');
        } else {
            // For security, show generic message even if email doesn't exist
            session()->flash('status', __('A reset link will be sent if the account exists.'));
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
