<?php


namespace App\Service;


class DataSaveService
{

    /**
     * @param string $filename
     * @param string $data
     */
    public static function saveToFile(string $filename, string $data)
    {
        file_put_contents($filename, $data);
    }

    /**
     * @param string $filename
     * @param        $data
     */
    public static function saveToFileAsJSON(string $filename, $data)
    {
        file_put_contents($filename, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}