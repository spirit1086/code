<?php
namespace App\Service\VacancySteps\Interfaces;

use App\Vacancy;

interface MessageEmailInterface
{
   public function publicationMessage(Vacancy $vacancy);
   public function subscribes(Vacancy $vacancy);
}