<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberQrCodeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $member;

    /**
     * Create a new message instance.
     */
    public function __construct(Member $member)
    {
        //
        $this->member = $member;
    }
    
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Gym Access QR Pass Code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.member-qr-code',
        );
    }

    public function attachments(): array
    {
        $token = $this->member->qr_token ?? $this->member->membership_no;

        // Generates clean PNG binary stream using the newly installed Imagick extension
        $qrImage = QrCode::format('png')
            ->size(400)
            ->errorCorrection('H')
            ->generate($token);

        return [
           Attachment::fromData(fn () => $qrImage, 'gym-access-qr.png')
                ->withMime('image/png'), // Changed from ->mime() to ->withMime()
        ];
    }
}
