<?php


namespace App\Service;


class PascService
{
    public function __construct()
    {
    }


    public static function addCompetitions()
    {

    }

    public static function subscribeUser()
    {

    }

    /**
     * @return string
     */
    public static function getHost()
    {
        return $_ENV['PASC_HOST'];
    }

    /**
     * @return string
     */
    public static function getToken()
    {
        return $_ENV['PASC_TOKEN'];
    }
}