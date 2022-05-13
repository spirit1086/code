<?php

namespace App\Service\VacancySteps\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VacancyNotifyUsers extends Mailable
{
    use Queueable, SerializesModels;

    private $subject_email;
    private $text;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->subject_email = $data['subject'];
        $this->text = $data['text'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from = config('mail.username');
        $name = config('mail.app_name');
        return $this->subject($this->subject_email)->from($from, $name)
            ->view('emails.notify_users')->with(['text'=>$this->text]);
    }
}
