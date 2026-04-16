<?php

namespace App\Mail;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketPurchasedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket
    ) {
        $this->ticket->loadMissing('event');
    }

    public function build()
    {
        // PNG via GD : affichage inline dans le HTML + pièce jointe dédiée
        $qrPng = Builder::create()
            ->data($this->ticket->qr_code)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(280)
            ->margin(8)
            ->build()
            ->getString();

        $eventTitle = $this->ticket->event?->title ?? 'Événement';

        $pdfBinary = Pdf::loadView('pdf.ticket', [
            'ticket' => $this->ticket,
            'qrPngBase64' => base64_encode($qrPng),
        ])->output();

        $qrAttachmentName = 'qr-billet-'.$this->ticket->id.'.png';

        return $this->subject('Votre billet : '.$eventTitle)
            ->view('emails.ticket-purchased')
            ->text('emails.ticket-purchased-plain')
            ->with([
                'ticket' => $this->ticket,
                'qrPng' => $qrPng,
                'qrAttachmentName' => $qrAttachmentName,
            ])
            ->attachData(
                $qrPng,
                $qrAttachmentName,
                ['mime' => 'image/png']
            )
            ->attachData(
                $pdfBinary,
                'billet-'.$this->ticket->id.'.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
