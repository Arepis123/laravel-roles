<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plainPassword;
    public $role;

    public function __construct(User $user, string $plainPassword, string $role)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
        $this->role = $role;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your ' . config('app.name') . ' Account Has Been Created',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user-created',
            with: [
                'user' => $this->user,
                'plainPassword' => $this->plainPassword,
                'role' => $this->role,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
