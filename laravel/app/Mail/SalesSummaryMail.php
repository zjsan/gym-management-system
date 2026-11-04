<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalesSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $summaryData, 
        public string $rangeLabel, 
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Gym Sales Summary Report (' . $this->rangeLabel . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sales-summary',
        );
    }
}
