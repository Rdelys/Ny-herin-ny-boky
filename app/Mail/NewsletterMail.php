<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $sujet,
        public string $corps,
    ) {}

    public function build()
    {
        return $this->subject($this->sujet)
            ->view('emails.newsletter');
    }
}