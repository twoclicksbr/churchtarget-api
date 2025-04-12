<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $verificationCode;

    public function __construct(string $userName, string $verificationCode)
    {
        $this->userName = $userName;
        $this->verificationCode = $verificationCode;
    }

    public function build()
    {
        return $this->subject('Verificação de E-mail')
                    ->view('emails.verify');
    }
}
