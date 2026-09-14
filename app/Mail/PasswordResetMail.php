<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $token;

    public User $user;

    public function __construct(string $token, User $user)
    {
        $this->token = $token;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Restablece tu contraseña - Catedral Cristiana',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'token' => $this->token,
                'user' => $this->user,
                'resetUrl' => url('/reset-password?token='.$this->token),
            ],
        );
    }
}
