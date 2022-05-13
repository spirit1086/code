<?php
namespace App\Service\VacancySteps\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Vacancy;

class VacancyNotifyUsers extends Model
{
    protected $table = 'vacancy_notify_users';

    protected $fillable = [
        'vacancy_id',
        'step',
        'email',
        'message',
        'is_head',
        'is_send'
    ];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function insVacancyNotifyUsers(array $data){
        return VacancyNotifyUsers::insert($data);
    }

    public function vacancyNotifyCheckSend(array $ids){
        return VacancyNotifyUsers::whereIn('id',$ids)
                                 ->update(['is_send'=>1]);
    }

    public function vacancySendNotifyForUsers(){
       return VacancyNotifyUsers::whereNull('is_send');
    }
}