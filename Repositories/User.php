<?php
namespace App\Service\VacancySteps\Repositories;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    public function usersExternal(array $ids)
    {
        return User::select('email')->whereIn('id',$ids)->where('work_place', 'other')->pluck('email')->toArray();
    }

    public function usersDzo(array $ids){
        return User::select('email')->whereIn('id',$ids)->where('work_place', 'kap')->pluck('email')->toArray();
    }

    public function usersInternal(array $ids,int $dzo_id){
       return User::select('email')
            ->whereIn('id',$ids)
            ->where('dzo_id', $dzo_id)
            ->where('work_place', 'kap')
            ->pluck('email')
            ->toArray();
    }
}