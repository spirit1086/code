<?php
namespace App\Service\VacancySteps\Services;

use App\Vacancy;
use App\Service\VacancySteps\Interfaces\MessageEmailInterface;
use App\Service\VacancySteps\Repositories\MessageEmail;

class MessageEmailService implements MessageEmailInterface
{
    private $vacancy;
    private $message_email;

    public function __construct(MessageEmail $message_email)
    {
        $this->message_email = $message_email;
    }

    public function publicationMessage(Vacancy $vacancy)
    {
       $message = $this->message_email->getItem('publication_notice');
       $subject = str_replace('#VACANCY#', $vacancy->title, $message->title);
       $text = str_replace('#VACANCY#', $vacancy->title, $message->message);
       $text = str_replace('#ADD_DATE#', $vacancy->add_date, $text);
       $text = str_replace('#END_DATE#', $vacancy->end_date, $text);
       $text = str_replace('#LINK#', url('/#/vacancies/view?id=' . $vacancy->id), $text);
       return ['subject' => $subject,'text'   => $text];
    }

   public function subscribes(Vacancy $vacancy)
   {
       $message = $this->message_email->getItem('subscribes');
       $subject = $message->title;
       $text = $message->message;
       $text = str_replace('#VACANCY#', $vacancy->title, $text);
       $text = str_replace('#LINK#', url('/#/vacancies/view?id=' . $vacancy->id), $text);
       return ['subject' => $subject,'text'   => $text];
   }
}