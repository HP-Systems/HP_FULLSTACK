<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMailActivation extends Mailable
{
    use Queueable, SerializesModels;

    protected $url;
    protected $id;
    protected $random;
    /**
     * Create a new message instance.
     */
    public function __construct( $url, $id, $random)
    {
        //
        $this->url = $url;
        $this->id = $id;
        $this->random = $random;
    }
   

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hotel- Confirmacion de Correo Electronico ',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email',
            with: [
                'url'  => $this->url,
                'random' => $this->random,
                'id' => $this->id->id,
            ],
            
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
