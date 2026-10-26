<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Security Alert: Your Gym System Password Was Changed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-changed', // We will create this simple view next
        );
    }
}
