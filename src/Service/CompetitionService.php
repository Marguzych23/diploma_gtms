<?php


namespace App\Service;


use App\Entity\Competition;
use App\Entity\CompetitionLoadDate;
use App\Entity\Industry;
use App\Entity\User;
use App\Exception\CompetitionException;
use App\Exception\SubscribeException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CompetitionService
{
    private EntityManagerInterface $entityManager;

    /**
     * CompetitionService constructor.
     *
     * @param EntityManagerInterface $entityManagerInterface
     */
    public function __construct(
        EntityManagerInterface $entityManagerInterface
    ) {
        $this->entityManager = $entityManagerInterface;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws CompetitionException
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function updateDataFromPASC()
    {
        $lastLoadDate             = null;
        $lastCompetitionsLoadDate = $this->entityManager
            ->getRepository(CompetitionLoadDate::class)
            ->findOneBy(['status' => 1]);

        if ($lastCompetitionsLoadDate instanceof CompetitionLoadDate) {
            $lastLoadDate = $lastCompetitionsLoadDate->getDate();
        }

        if ($lastLoadDate === null) {
            $lastLoadDate = new DateTime('10 September 2000');
        }

        $this->loadNewCompetitions($lastLoadDate);

        if ($lastCompetitionsLoadDate instanceof CompetitionLoadDate) {
            $lastCompetitionsLoadDate->setStatus(0);
            $this->entityManager->persist($lastCompetitionsLoadDate);
        }
        $this->entityManager->flush();

        $this->updateQuerySearchData();
        $this->loadNotifications($lastLoadDate);
    }

    /**
     * @param DateTime $lastLoadDate
     *
     * @throws ClientExceptionInterface
     * @throws CompetitionException
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function loadNewCompetitions(DateTime $lastLoadDate)
    {
        $competitions = PascService::getNewCompetitions($lastLoadDate)['result']['competitions'];

        if (count($competitions) === 0) {
            throw new CompetitionException('', 111);
        }

        $competitionsLoadDate = new CompetitionLoadDate();
        $competitionsLoadDate->setDate(new DateTime());
        $competitionsLoadDate->setStatus(1);
        $this->entityManager->persist($competitionsLoadDate);
        $this->entityManager->flush();

        /** @var CompetitionLoadDate $competitionsLoadDate */
        $competitionsLoadDate = $this->entityManager
            ->getRepository(CompetitionLoadDate::class)
            ->findOneBy(['status' => 1]);

        foreach ($competitions as $competition) {
            if ($this->entityManager
                    ->getRepository(Competition::class)
                    ->findOneBy(['name' => $competition['name']])
                instanceof Competition) {
                var_dump($competition['name']);
//                TODO
            } else {
                $newCompetition = new Competition();
                $newCompetition->setName($competition['name']);
                $newCompetition->setGrantSize($competition['grant_size']);
                $newCompetition->setUrl($competition['url']);
                $newCompetition->setDeadline(new DateTime($competition['deadline']));
                foreach ($competition['industries'] as $industryName) {
                    $industry = $this->entityManager->getRepository(Industry::class)
                        ->findOneBy(['name' => $industryName]);
                    if ($industry instanceof Industry) {
                        $newCompetition->addIndustry($industry);
                    }
                }
                $newCompetition->setCompetitionLoadDate($competitionsLoadDate);
                $this->entityManager->persist($newCompetition);
                $this->entityManager->flush();
            }
        }
    }

    /**
     * @param DateTime $lastLoadDate
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function loadNotifications(DateTime $lastLoadDate)
    {
        $emails = PascService::getNotificationsForEmails($lastLoadDate)['result']['emails'];

        foreach ($emails as $email) {
            $user = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['email' => $email['email']]);
            if ($user instanceof User) {
                foreach ($email as $competition) {
                    $existCompetition = $this->entityManager
                        ->getRepository(Competition::class)
                        ->findOneBy(['name' => $competition['name']]);
                    if ($existCompetition instanceof Competition) {
                        $user->addCompetition($competition);
                    }
                }
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }
    }

    /**
     * @param int $id
     *
     * @return Competition|object|null
     */
    public function getCompetitionById(int $id) : ?Competition
    {
        return $this->entityManager
            ->getRepository(Competition::class)
            ->find($id);
    }

    /**
     * Generate search matrix by all competitions name
     */
    public function updateQuerySearchData()
    {
        $competitions         = $this->entityManager->getRepository(Competition::class)->findAll();
        $preparedCompetitions = [];
        foreach ($competitions as $competition) {
            if ($competition instanceof Competition) {
                $preparedCompetitions[$competition->getId()] = $competition->getName();
            }
        }

        IndexService::generateSearchMatrix($preparedCompetitions);
    }

    /**
     * @param User $user
     *
     * @return array
     * @throws SubscribeException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function subscribeUser(User $user)
    {
        if (count($user->getIndustries()->toArray()) === 0) {
            throw new SubscribeException("", 310);
        }

        return PascService::subscribeEmail($user->getEmail(), $user->getIndustries()->toArray());
    }

    /**
     * @return object[]
     */
    public function getAllCompetitions()
    {
        return $this->entityManager->getRepository(Competition::class)->findAll();
    }

    /**
     * @param string $query
     * @param int    $deadlineStart
     * @param int    $deadlineEnd
     * @param array  $industries
     *
     * @return object[]
     * @throws Exception
     */
    public function getCompetitions(
        string $query = '', int $deadlineStart = 0, int $deadlineEnd = 0, array $industries = []
    ) {
        $deadlineStart = $deadlineStart !== 0 ? (new DateTime())->setTimestamp($deadlineStart) : null;
        $deadlineEnd   = $deadlineEnd !== 0 ? (new DateTime())->setTimestamp($deadlineEnd) : null;

        $result = $this->entityManager->getRepository(Competition::class)
            ->getCompetitionsBy($deadlineStart, $deadlineEnd, $industries);


        if (trim($query) !== '' && count($result) !== 0) {
            $docIds = IndexService::search($query);
            /** @var Competition $competition */
            foreach ($result as $id => $competition) {
                if (in_array($competition->getId(), $docIds) === false) {
                    unset($result[$id]);
                }
            }
        }

        return $result;
    }
}