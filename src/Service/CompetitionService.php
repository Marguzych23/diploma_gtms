<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

class CompetitionService
{
    public function __construct(
        EntityManagerInterface $entityManagerInterface
    ) {
    }

    public function loadNewCompetitions()
    {

    }
}