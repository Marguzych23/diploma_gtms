<?php


namespace App\Service;


use Symfony\Component\HttpClient\HttpClient;

class PascService
{
    const USER_SUBSCRIBE_PATH   = '';
    const GET_COMPETITIONS_PATH = '/ajax/competition/get_all';


    public static function addCompetitions()
    {
        try {

            $client   = HttpClient::create();
            $response = $client->request('GET', self::getURL() . self::GET_COMPETITIONS_PATH);

            $content = $response->toArray();

            return $content;
        } catch (\Throwable $throwable) {
            var_dump($throwable->getMessage());
        }
    }

    public static function subscribeUser()
    {

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