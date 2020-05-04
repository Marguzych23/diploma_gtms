<?php


namespace App\Repository;


use App\Entity\CompetitionLoadDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

class CompetitionLoadDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompetitionLoadDate::class);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getLastDate()
    {
        return $this->findOneBy(['status' => 1]);
    }
}