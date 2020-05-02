<?php


namespace App\Repository;


use App\Entity\Competition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

class CompetitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Competition::class);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getActualCompetitions()
    {
        return $this->_em
            ->createQuery("SELECT u FROM App\Entity\Competition u WHERE u.deadline >= CURRENT_DATE()")
            ->getResult();
    }
}