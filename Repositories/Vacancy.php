<?php
namespace App\Service\VacancySteps\Repositories;

use App\Department;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    protected $table = 'vacancies';

    protected $fillable = [
        'prof_id',
        'department_id',
        'title',
        'full',
        'short',
        'full_eng',
        'add_date',
        'last_mod',
        'end_date',
        'testing_norm',
        'parent_id',
        'comments',
        'education',
        'skills',
        'language',
        'computer_skills',
        'manager_request_id',
        'duties',
        'is_notification',
        'created_at'
    ];

    /**
     * Get department relation instance
     * @return Department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function headsEmails()
    {
        return $this->department->heads->pluck('email')->toArray();
    }
}