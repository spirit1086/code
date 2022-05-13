<?php
namespace App\Service\VacancySteps\Services;

use App\Helpers\Setting\Setting;
use App\Service\VacancySteps\Interfaces\VacancyServiceInterface;
use App\Service\VacancySteps\Repositories\MessageEmail;
use App\Service\VacancySteps\Repositories\VacancyNotifyUsers;
use App\Vacancy;
use App\Service\VacancySteps\Repositories\User;
use Illuminate\Support\Facades\Log;
use App\Service\VacancySteps\Repositories\VacancySubscribe;

class VacancyService implements VacancyServiceInterface
{
   private $vacancy;
   private $vacancy_subscribe;
   private $user;

   public function __construct(VacancySubscribe $vacancy_subscribe,User $user)
   {
       $this->vacancy_subscribe = $vacancy_subscribe;
       $this->user = $user;
   }

   public function insertItem(Vacancy $vacancy,array $holidays)
   {
       $vacancy->search_domain = 'internal';
       $vacancy->status = 'published';
       $vacancy->add_date = date('Y-m-d H:i:s');
       $vacancy->end_date = $this->calcDays('end_date',$vacancy,$holidays);
       $vacancy->internal_date = $this->calcDays('internal_date',$vacancy,$holidays);
       $vacancy->dzo_date = $this->calcDays('dzo_date',$vacancy,$holidays);
       $vacancy->external_date = $this->calcDays('external_date',$vacancy,$holidays);
       $vacancy->new_scheduler = 1;
   }

   public function notifyEmailHeads(Vacancy $vacancy)
   {
       $emails = $vacancy->headsEmails();
       if(empty($emails)){
           Log::alert('LOG_ALERT getHeadsEmail:',['id'=>$vacancy->id,'empty_heads'=>true]);
           return false;
       }
       return $emails;
   }

   public function notifyEmailUsers(Vacancy $vacancy)
   {
       $emails=[];
       $usersCity = isset($vacancy->city_id) ? $this->vacancy_subscribe->subscribes($vacancy->city_id,'city')  : [];
       $usersDzo = isset($vacancy->department->dzo_id) ? $this->vacancy_subscribe->subscribes($vacancy->department->dzo_id, 'dzo') : [];
       $usersProf = isset($vacancy->prof_id) ? $this->vacancy_subscribe->subscribes($vacancy->prof_id,'prof') : [];
       $ids = array_merge($usersCity, $usersDzo, $usersProf);

       if(!empty($ids)){
           if($vacancy->search_domain=='internal'){
               $emails = $this->user->usersInternal($ids,$vacancy->department->dzo_id);
           }elseif($vacancy->search_domain=='dzo'){
               $emails = $this->user->usersDzo($ids);
           }else{ 
               $emails = $this->user->usersExternal($ids);
           }
       }

       if(empty($emails)){
           Log::alert('LOG_ALERT getSubscrubersEmail:',['id'=>$vacancy->id,'empty_users'=>true]);
           return false;
       }

       return $emails;
   }

   public function calcDays(string $key,Vacancy $vacancy,array $holidays)
   {
       $datetime = null;
       $internal_days = Setting::data('internal_days');
       $dzo_days = Setting::data('dzo_days');
       if($key=='end_date'){
           $days = $internal_days+$dzo_days+$vacancy->external_duration;
       }elseif($key=='internal_date'){ // дата завершения на внутреннем
           $days = $internal_days;
       }elseif($key=='dzo_date'){ // дата завершения на дзо
           $days = $internal_days+$dzo_days;
       }elseif($key=='external_date'){ // дата завершения на внешке
           $days = $internal_days+$dzo_days+$vacancy->external_duration;
       }
       $dates=[];

       for($i=1;$i<=$days;$i++){
           $datetime = date('Y-m-d H:i:s', strtotime($vacancy->add_date . ' +'.$i.' days'));
           $day_of_week = date('w', strtotime($datetime));
           $date = date('Y-m-d',strtotime($datetime));
           if(in_array($date,$holidays) || ($day_of_week==0 || $day_of_week==6)){
               $days++;
           }
           else{
               $dates[] = $datetime;
           }
       }

       return $dates[count($dates)-1];
   }

   public function nextSearchDomainStep(Vacancy $vacancy){
       if($vacancy->search_domain=='internal'){
           $vacancy->search_domain = 'dzo';
           $vacancy->internal_close=1;
       }elseif($vacancy->search_domain=='dzo'){
           $vacancy->search_domain = 'external';
           $vacancy->dzo_close=1;
       }elseif($vacancy->search_domain=='external'){
           $vacancy->status = 'closed';
           $vacancy->external_close=1;
       }
       $vacancy->save();
   }

   public function changeExternalDays(Vacancy $vacancy,array $holidays){
      $vacancy_before_save = $this->getItem($vacancy);
      if($vacancy_before_save->external_duration<$vacancy->external_duration){ //если увеличелось кол-во дней на внешке, то пересчитываем дни
          $vacancy->end_date = $this->calcDays('end_date',$vacancy,$holidays);
          $vacancy->external_date = $this->calcDays('external_date',$vacancy,$holidays);
          $vacancy->external_close = null;
      }
      else{
          $vacancy->end_date = $vacancy_before_save->end_date;
      }
   }

   public function reOpen(Vacancy $vacancy){
      return $vacancy->reOpenVacancy($vacancy);
   }

   private function getItem(Vacancy $vacancy)
   {
       return $vacancy->getVacancy($vacancy->id);
   }

   public function getItems(Vacancy $vacancy,string $search_domain, string $datetime)
   {
       return $vacancy->items($search_domain,$datetime);
   }

   public function notifyUsers(VacancyNotifyUsers $vacancy_notify_user,array $recipients,Vacancy $vacancy,string $message, bool $is_head=false)
   {
      $data=[];
      foreach($recipients as $recipient){
          $data[] = ['vacancy_id'=>$vacancy->id,'step'=>'internal','email'=>$recipient,'message'=>$message,'is_head'=>($is_head ? 1 : null)];
      }
      return $vacancy_notify_user->insVacancyNotifyUsers($data);
   }

   public function getVacancyNotifyUsers(VacancyNotifyUsers $vacancy_notify_user)
   {
       return $vacancy_notify_user->vacancySendNotifyForUsers();
   }

   public function setStatusSendVacancyNotifyUsers(VacancyNotifyUsers $vacancy_notify_user,array $ids)
   {
       return $vacancy_notify_user->vacancyNotifyCheckSend($ids);
   }
}