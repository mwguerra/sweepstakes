<?php

namespace App\Mail;

use App\Models\Sweepstakes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WinnerNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected Sweepstakes $sweepstakes)
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Winner Notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.winner-notification',
            with: [
                'winnerEmailMessage' => $this->sweepstakes->winner_email_message
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
        $attachments = [];

        foreach ($this->sweepstakes->winnerEmailFiles as $file) {
            $attachments[] = Attachment::fromStorage($file->path)
                ->as($file->original_name)
                ->withMime($file->mime_type);
        }

        return $attachments;
    }

    // For testing purposes only.
    public function getSweepstakes(): Sweepstakes
    {
        return $this->sweepstakes;
    }
}
