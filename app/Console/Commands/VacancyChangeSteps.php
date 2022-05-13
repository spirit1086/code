<?php

namespace App\Console\Commands;

use App\Service\VacancySteps\Mail\VacancyNotifyUsers;
use App\Service\VacancySteps\Services\MailService;
use App\Service\VacancySteps\Services\MessageEmailService;
use App\Vacancy;
use Illuminate\Support\Facades\Log;
use App\Service\VacancySteps\Repositories\{MessageEmail, User, VacancySubscribe};
use App\Service\VacancySteps\Services\VacancyService;
use Illuminate\Console\Command;

class VacancyChangeSteps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vacancy:steps';
    private $vacancyService;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Перекидывает вакансии по этапам';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->vacancyService = new VacancyService(new VacancySubscribe(),new User());
        $this->messageEmailService = new MessageEmailService(new MessageEmail());
        $this->mailService = new MailService();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $current_datetime = date('Y-m-d H:i:s');
        $search_domain=['internal','dzo','external'];
        for($i=0;$i<count($search_domain);$i++){
            $this->vacancyService->getItems(new Vacancy(),$search_domain[$i],$current_datetime)->chunk(100, function ($vacancies)
            {
                foreach($vacancies as $vacancy){
                    $this->vacancyService->nextSearchDomainStep($vacancy);
                    $message_users_subscribes = $this->messageEmailService->subscribes($vacancy);
                    $users_subscribe_emails = $this->vacancyService->notifyEmailUsers($vacancy);
                    if($users_subscribe_emails){
                        foreach($users_subscribe_emails as $email){
                                $this->mailService->vacancyEachNotify($email,new VacancyNotifyUsers($message_users_subscribes));
                        }
                    }
                }
            });
        }
    }


}
