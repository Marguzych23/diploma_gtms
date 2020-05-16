<?php


namespace App\Service;


class DataLoadService
{
    /**
     * @param string $filename
     *
     * @return false|string
     */
    public static function loadFromFile(string $filename)
    {
        return file_get_contents($filename);
    }
}