<?php


namespace App\Repository;


use App\Entity\Competition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
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


    public function getCompetitionsBy(
        ?\DateTime $deadlineStart = null, ?\DateTime $deadlineEnd = null, array $industries = []
    ) {
        $query = $this->_em->getRepository(Competition::class)
            ->createQueryBuilder('c')
            ->orderBy('c.deadline', 'DESC');

        $parameters = [];

        if ($deadlineStart !== null) {
            $query->andWhere('c.deadline >= :deadlineStart');
            $parameters['deadlineStart'] = $deadlineStart;
        }

        if ($deadlineEnd !== null) {
            $query->andWhere('c.deadline <= :deadlineEnd');
            $parameters['deadlineEnd'] = $deadlineEnd;
        }

        if (count($industries) !== 0) {
            $query
                ->innerJoin('c.industries', 'ic',
                    Join::WITH, 'ic.id IN (:industries)');
            $parameters['industries'] = implode(',', $industries);
        }

        return $query
            ->setParameters($parameters)
            ->getQuery()
            ->getResult();
    }
}