<?php


namespace App\Service;


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
     * @param KfuAuth $kfuAuth
     *
     * @return User|object|null
     */
    public function createUserByKfuAuth(KfuAuth $kfuAuth)
    {
        $email = $kfuAuth->getUserData()['email'];

        $user = new User();
        $user->setEmail($email);
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
     * @return User
     */
    public static function getUser()
    {
        return unserialize($_SESSION['user_']);
    }
}