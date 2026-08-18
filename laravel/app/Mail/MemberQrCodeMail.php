<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberQrCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $qrSvg;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
        $this->member = $member;
        $token = $member->qr_token ?? $member->member_code;
        
        // Generate SVG string for inline embedding
        $this->qrSvg = (string) QrCode::size(250)->format('svg')->generate($token);
    }

    public function build()
    {
        return $this->subject('Your Gym Access QR Pass Code')
                    ->view('emails.member-qr-code');
    }
    
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Member Qr Code Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
