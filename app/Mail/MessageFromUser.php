<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MessageFromUser extends Mailable
{
    use Queueable, SerializesModels;

    protected $messageContent;
    protected $userEmail;

    public function __construct(string $messageContent, string $userEmail)
    {
        $this->messageContent = $messageContent;
        $this->userEmail = $userEmail;
    }

    public function build()
    {
        return $this->from('bilazajeff9@gmail.com', 'InBills_Platform_Contact')
                    ->replyTo($this->userEmail)
                    ->subject('Facture')
                    ->html("<p>{$this->messageContent}</p>");
    }
}