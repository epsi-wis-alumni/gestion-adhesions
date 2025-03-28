<?php

namespace App\Service;

use App\Entity\JobOffer;
use App\Entity\JobQuestion;
use App\Entity\User;

final class JobQuestionManager
{
   public function create(JobQuestion $jobQuestion , string $description, JobOffer $jobOffer, User $createdBy): void
   {
        $jobQuestion
            ->setCreatedBy($createdBy)
            ->setDescription($description)
            ->setJobOffer($jobOffer)
            ->setCreatedAt(new \DateTimeImmutable())
        ;
   }
}
