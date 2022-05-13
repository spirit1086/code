<?php
namespace App\Service\VacancySteps\Services;

use App\Service\VacancySteps\Interfaces\MailServiceInterface;
use App\Service\VacancySteps\Services\Mail\VacancyNotifyHeads;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService implements MailServiceInterface
{
   public function vacancyNotify(array $emails, Mailable $notifyMailable)
   {
       Mail::to($emails)->send($notifyMailable);

       if (Mail::failures()) {
           Log::alert('LOG_MAIL_ALERT:',Mail::failures());
       }
   }

   public function vacancyEachNotify(string $email, Mailable $notifyMailable)
   {
       Mail::to($email)->send($notifyMailable);

       if (Mail::failures()) {
           Log::alert('LOG_MAIL_ALERT_EACH:',Mail::failures());
       }
   }
}