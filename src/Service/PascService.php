<?php


namespace App\Service;


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PascService
{
    const USER_SUBSCRIBE_PATH   = '/ajax/subscribe/user';
    const GET_COMPETITIONS_PATH = '/ajax/competition/get_all';

    /**
     * @param \DateTime $date
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function getNewCompetitions(\DateTime $date)
    {
        $client   = HttpClient::create();
        $response = $client->request('POST',
            self::getURL() . self::GET_COMPETITIONS_PATH,
            [
                'body' => [
                    'token' => self::getToken(),
                    'date'  => $date,
                ],
            ]);

        return $response->toArray();
    }

    /**
     * @param string $email
     * @param array  $industries
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function subscribeEmail(string $email, array $industries)
    {
        $client   = HttpClient::create();
        $response = $client->request('POST',
            self::getURL() . self::USER_SUBSCRIBE_PATH,
            [
                'body' => [
                    'token'      => self::getToken(),
                    'email'      => $email,
                    'industries' => $industries,
                ],
            ]);

        return $response->toArray();
    }

    /**
     * @return string
     */
    public static function getURL()
    {
        return $_ENV['PASC_URL'];
    }

    /**
     * @return string
     */
    public static function getToken()
    {
        return $_ENV['PASC_TOKEN'];
    }
}