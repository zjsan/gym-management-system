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
        // Generate token and create base64 Data URI for inline display in the email body
        $token = $this->member->qr_token ?? $this->member->membership_no;
        
       // Generate raw binary string for both the inline view and the download attachment
        $qrImageBinary = (string) QrCode::format('png')
            ->size(400)
            ->errorCorrection('H')
            ->generate($token);

        return new Content(
            view: 'emails.member-qr-code',
            with: [
                'qrImageBinary' => $qrImageBinary,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $token = $this->member->qr_token ?? $this->member->membership_no;

        // Ensure raw string conversion for attachment payload as well
        $qrImageBinary = (string) QrCode::format('png')
            ->size(400)
            ->errorCorrection('H')
            ->generate($token);

        return [
            Attachment::fromData(fn () => $qrImageBinary, 'gym-access-qr.png')
                ->withMime('image/png'),
        ];
    }
}