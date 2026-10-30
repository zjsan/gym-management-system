<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemporaryPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $user, public string $tempPassword) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Gym System Account Has Been Created',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.temporary-password',
        );
    }
}
