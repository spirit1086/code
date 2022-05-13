<?php
namespace App\Service\VacancySteps\Interfaces;

use App\Service\VacancySteps\Repositories\VacancyNotifyUsers;
use App\Vacancy;

interface VacancyServiceInterface
{
    public function notifyEmailHeads(Vacancy $vacancy);
    public function notifyEmailUsers(Vacancy $vacancy);
    public function calcDays(string $key,Vacancy $vacancy,array $holidays);
    public function changeExternalDays(Vacancy $vacancy,array $holidays);
    public function nextSearchDomainStep(Vacancy $vacancy);
    public function reOpen(Vacancy $vacancy);
    public function insertItem(Vacancy $vacancy,array $holidays);
    public function getItems(Vacancy $vacancy,string $search_domain,string $datetime);
    public function notifyUsers(VacancyNotifyUsers $vacancy_notify_user,array $recipients,Vacancy $vacancy,string $message, bool $is_head=false);
    public function getVacancyNotifyUsers(VacancyNotifyUsers $vacancy_notify_user);
    public function setStatusSendVacancyNotifyUsers(VacancyNotifyUsers $vacancy_notify_user,array $ids);
}