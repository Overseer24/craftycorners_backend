<?php

namespace App\Mail;

use App\Models\Mentor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MentorshipApplicationStatus extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $mentor;
    public $status;
    public $user;

    public function __construct(Mentor $mentor, $status, $user)
    {
        $this->mentor = $mentor;
        $this->status = $status;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mentorship Application Status',
        );
    }

    /**
     * Get the message content definition.
     */


    public function content(): Content
    {
        return new Content(

            markdown: 'emails.mentorship-application-status',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
