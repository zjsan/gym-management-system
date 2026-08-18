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
        //runs later when the background worker actually executes the email
        //to keep avoid bloating the database with a potentially large QR code string
        $token = $this->member->qr_token ?? $this->member->member_code;
        $qrSvg = (string) QrCode::size(250)->format('svg')->generate($token);

        return new Content(
            view: 'emails.member-qr-code',
            with: [
                'qrSvg' => $qrSvg, // Safely passes the code to your Blade view
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
