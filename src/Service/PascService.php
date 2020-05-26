<?php


namespace App\Service;


use DateTime;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PascService
{
    const USER_SUBSCRIBE_PATH              = '/api/subscribe/emails';
    const GET_COMPETITIONS_AND_NOTIFY_PATH = '/api/get';

    /**
     * @param DateTime $date
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function getNewCompetitions(DateTime $date)
    {
        $client   = HttpClient::create();
        $response = $client->request('POST',
            self::getURL() . self::GET_COMPETITIONS_AND_NOTIFY_PATH,
            [
                'body' => [
                    'app_name'     => self::getAppName(),
                    'token'        => self::getToken(),
                    'time'         => $date->getTimestamp(),
                    'competitions' => 1,
                ],
            ]
        );

        return $response->toArray();
    }

    /**
     * @param DateTime $date
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function getNotificationsForEmails(DateTime $date)
    {
        $client   = HttpClient::create();
        $response = $client->request('POST',
            self::getURL() . self::GET_COMPETITIONS_AND_NOTIFY_PATH,
            [
                'body' => [
                    'app_name' => self::getAppName(),
                    'token'    => self::getToken(),
                    'date'     => $date->getTimestamp(),
                    'emails'   => 1,
                ],
            ]
        );

        return $response->toArray();
    }

    /**
     * @param string $email
     * @param array  $industries
     *
     * @param bool   $sub
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function subscribeEmail(string $email, array $industries, bool $sub = true)
    {
        $client   = HttpClient::create();
        $response = $client->request('POST',
            self::getURL() . self::USER_SUBSCRIBE_PATH,
            [
                'body' => [
                    'app_name' => self::getAppName(),
                    'token'    => self::getToken(),
                    'type'     => $sub ? 'subscribe' : 'unsubscribe',
                    'emails'   => [
                        'email'      => $email,
                        'industries' => $industries,
                    ],
                ],
            ]
        );

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

    /**
     * @return string
     */
    public static function getAppName()
    {
        return str_replace('_', ' ', $_ENV['PASC_APP_NAME']);
    }
}