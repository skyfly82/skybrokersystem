<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,
        public string $emailContent,
        public array $data = []
    ) {}

    public function build()
    {
        return $this->subject($this->emailSubject)
                    ->view('emails.generic-notification')
                    ->with([
                        'content' => $this->emailContent,
                        'data' => $this->data
                    ]);
    }
}