<?php


namespace App\Service;


use App\Entity\Competition;
use App\Entity\Industry;
use App\Entity\KfuAuth;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UserService
{
    private ?EntityManagerInterface $entityManager;

    /**
     * UserService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $code
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function auth(string $code)
    {
        $kfuAuth = KfuAuthService::getUserTokens($code);
        $kfuAuth = KfuAuthService::getUserData($kfuAuth);
        self::setKfuAuth($kfuAuth);

        $email = $kfuAuth->getUserData()['email'];

        $user = $this->getUserByEmail($email);

        self::setUser(
            $user instanceof User
                ? $user
                : $this->createUserByKfuAuth($kfuAuth)
        );
    }

    /**
     * @return array
     */
    public function getCompetitions()
    {
        $user = $this->getUserByEmail(self::getUser()->getEmail());

        $competitions = [];

        /** @var Industry $industry */
        foreach ($user->getIndustries() as $industry) {
            $competitions = array_merge($competitions, $industry->getCompetitions()->getValues());
        }

        return $competitions;
    }

    /**
     * @return array
     */
    public function getCompetitionsIdForNotify()
    {
        $user = $this->getUserByEmail(self::getUser()->getEmail());

        $competitions = [];

        /** @var Competition $competition */
        foreach ($user->getCompetitions() as $competition) {
            $competitions[] = $competition->getId();
        }

        return $competitions;
    }

    /**
     * @param array $industries
     */
    public function addIndustries(array $industries)
    {
        $user = $this->getUserByEmail(self::getUser()->getEmail());

        foreach ($industries as $industry) {
            $tempIndustry = $this->entityManager
                ->getRepository(Industry::class)
                ->find((int) $industry);
            if ($tempIndustry instanceof Industry) {
                $user->addIndustry($tempIndustry);
            }
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        self::setUser($user);
    }

    /**
     * @return  array $industriesId
     */
    public function getIndustriesId()
    {
        $industriesId = [];

        /** @var Industry $industry */
        foreach ($this->getUserByEmail(self::getUser()->getEmail())->getIndustries() as $industry) {
            $industriesId[] = $industry->getId();
        }

        return $industriesId;
    }

    /**
     * @param bool $subscribe
     */
    public function emailSubscribe(bool $subscribe = false)
    {
        $user = $this->getUserByEmail(self::getUser()->getEmail());

        $user->setEmailSubscribe($subscribe);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        self::setUser($user);
    }

    /**
     * @param KfuAuth $kfuAuth
     *
     * @return User|object|null
     */
    public function createUserByKfuAuth(KfuAuth $kfuAuth)
    {
        $email = $kfuAuth->getUserData()['email'];
        $name  = $kfuAuth->getUserData()['last_name']
            . ' ' . $kfuAuth->getUserData()['first_name']
            . ' ' . $kfuAuth->getUserData()['middle_name'];

        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setLastNotifyDate(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->getUserByEmail($email);
    }

    /**
     * @param string $email
     *
     * @return User|object|null
     */
    public function getUserByEmail(string $email)
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(
                [
                    'email' => $email,
                ]
            );
    }

    /**
     * @param KfuAuth $kfuAuth
     */
    public static function setKfuAuth(KfuAuth $kfuAuth)
    {
        $_SESSION['kfu_auth'] = serialize($kfuAuth);
    }

    /**
     * @return KfuAuth
     */
    public static function getKfuAuth()
    {
        return unserialize($_SESSION['kfu_auth']);
    }

    /**
     * @param User $user
     */
    public static function setUser(User $user)
    {
        $_SESSION['user_'] = serialize($user);
    }

    /**
     * @return User|null
     */
    public static function getUser() : ?User
    {
        return isset($_SESSION['user_']) ? unserialize($_SESSION['user_']) : null;
    }
}