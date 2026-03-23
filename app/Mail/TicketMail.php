<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    public $qrImage;

    public function __construct($transaction, $qrImage)
    {
        $this->transaction = $transaction;
        $this->qrImage = $qrImage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎟️ E-Ticket PWL Capstone 1 Kamu Udah Ready!',
        );
    }

    // ... bagian attachments ...
    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.ticket', [
            'transaction' => $this->transaction,
            'qrImage' => $this->qrImage
        ])->setPaper('a4', 'portrait')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'E-Ticket_PWLCapstone1_TRX-' . $this->transaction->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
