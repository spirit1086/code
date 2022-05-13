<?php
namespace App\Service\VacancySteps\Repositories;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';
    public function heads()
    {
        return $this->belongsToMany(User::class, 'department_head')
            ->using(DepartmentHead::class);
    }

}