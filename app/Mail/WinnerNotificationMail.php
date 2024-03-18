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

    public $sweepstakes;

    /**
     * Create a new message instance.
     *
     * @param Sweepstakes $sweepstakes
     * @return void
     */
    public function __construct(Sweepstakes $sweepstakes)
    {
        $this->sweepstakes = $sweepstakes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Winner Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.winner-notification',
            with: $this->sweepstakes->prize,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->sweepstakes->files as $file) {
            Attachment::fromStorage('app/'. $file->path)
                ->as($file->original_name)
                ->withMime($file->mime_type);
        }

        return $attachments;
    }
}
