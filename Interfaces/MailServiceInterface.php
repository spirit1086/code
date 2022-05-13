<?php
namespace App\Service\VacancySteps\Interfaces;

use Illuminate\Mail\Mailable;

interface MailServiceInterface
{
   public function vacancyNotify(array $emails,Mailable $mailable);
   public function vacancyEachNotify(string $email,Mailable $mailable);
}