<?php
namespace App\Service\VacancySteps\Repositories;

use Illuminate\Database\Eloquent\Model;

class MessageEmail extends Model
{
    protected $table = 'email_messages';

    public function getItem(string  $value)
    {
        return MessageEmail::where('key', $value)->first();
    }
}