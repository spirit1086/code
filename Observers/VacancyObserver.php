<?php

namespace App\Service\VacancySteps\Observers;

use App\Holiday;
use Illuminate\Support\Facades\Log;
use App\Service\VacancySteps\Repositories\{MessageEmail,User,VacancySubscribe,VacancyNotifyUsers as VacancyNotifyUsersData};
use App\Vacancy;
use App\Service\VacancySteps\Mail\{VacancyNotifyHeads,VacancyNotifyUsers};
use App\Service\VacancySteps\Services\{MailService,MessageEmailService,VacancyService};

class VacancyObserver
{
    private $messageEmailService;
    private $vacancyService;
    private $mailService;
    private $holidaysObject;

    public function __construct()
    {
        $this->vacancyService = new VacancyService(new VacancySubscribe(),new User());
        $this->messageEmailService = new MessageEmailService(new MessageEmail());
        $this->mailService = new MailService();
        $this->holidaysObject = new Holiday();
    }

    public function creating(Vacancy $vacancy){
        $holidays = $this->holidays();
        $this->vacancyService->insertItem($vacancy,$holidays);
    }

    public function created(Vacancy $vacancy)
    {
        // получаем шаблоны рассылок
        $message_heads = $this->messageEmailService->publicationMessage($vacancy);
        $message_users_subscribes = $this->messageEmailService->subscribes($vacancy);
        // получаем email руководителей
        $heads_email = $this->vacancyService->notifyEmailHeads($vacancy);
        $users_subscribe_emails = $this->vacancyService->notifyEmailUsers($vacancy);

        if($heads_email){
            $this->vacancyService->notifyUsers(new VacancyNotifyUsersData(),$heads_email,$vacancy,json_encode($message_heads),true);
        }

        if($users_subscribe_emails){
            $this->vacancyService->notifyUsers(new VacancyNotifyUsersData(),$users_subscribe_emails,$vacancy,json_encode($message_users_subscribes));
        }
    }

    public function saving(Vacancy $vacancy){
        if(isset($vacancy->id)){
            $holidays = $this->holidays();
            $this->vacancyService->changeExternalDays($vacancy,$holidays);
        }
    }

    private function holidays(){
        $holidays = [];
        $items = $this->holidaysObject->items();
        foreach($items as $item){
            $holidays[] = $item->holiday;
        }
        return $holidays;
    }
}
