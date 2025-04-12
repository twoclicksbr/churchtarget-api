<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynamicEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $htmlContent;
    public string $subjectText;

    public function __construct(string $htmlContent, string $subjectText)
    {
        $this->htmlContent = $htmlContent;
        $this->subjectText = $subjectText;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                    ->html($this->htmlContent);
    }
}

