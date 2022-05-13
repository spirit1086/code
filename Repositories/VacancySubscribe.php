<?php
namespace App\Service\VacancySteps\Repositories;

use Illuminate\Database\Eloquent\Model;

class VacancySubscribe extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'type',
        'subscribe_id',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'user_id'
    ];

    public function subscribes(int $subscribe_id, string $type){
        return VacancySubscribe::where('subscribe_id', $subscribe_id)->where('type', $type)->pluck('user_id')->toArray();
    }
}