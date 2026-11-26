<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactMessage extends Mailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly string $naam,
        public readonly string $contact,
        public readonly ?string $onderwerp = null,
        public readonly ?string $bericht = null,
    ) {}

    /**
     * Get the message envelope. When the visitor left an e-mail address, replies go straight to them.
     */
    public function envelope(): Envelope
    {
        $replyTo = filter_var($this->contact, FILTER_VALIDATE_EMAIL) === false
            ? []
            : [new Address($this->contact, $this->naam)];

        return new Envelope(
            subject: 'Contactformulier: '.($this->onderwerp ?? 'nieuw bericht').' — '.$this->naam,
            replyTo: $replyTo,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            text: 'mail.contact-message',
        );
    }
}
