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
        $token = $this->member->qr_token 
            ?? $this->member->membership_no 
            ?? 'PREVIEW-FALLBACK-' . ($this->member->id ?? 'unknown');

        // Generate PNG instead of SVG
        $qrImagePng = QrCode::format('png')
            ->size(250)
            ->errorCorrection('H')
            ->generate($token);

        // Convert to base64 data URI so email clients can render it safely
        $qrCodeDataUri = 'data:image/png;base64,' . base64_encode($qrImagePng);

        return new Content(
            view: 'emails.member-qr-code',
            with: [
                'qrCodeDataUri' => $qrCodeDataUri,
            ],
        );
}

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
