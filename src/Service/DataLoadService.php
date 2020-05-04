<?php


namespace App\Service;


use intro_inf_retrieval\exception\BaseException;

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

    /**
     * @param string $url
     *
     * @return string
     * @throws BaseException
     */
    public static function loadFromURL($url)
    {
        $result = false;

        if ($url !== null) {
            $optionsArray = [
                CURLOPT_AUTOREFERER    => true,
                CURLOPT_COOKIESESSION  => false,
                CURLOPT_HTTPGET        => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_BINARYTRANSFER => true,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36',
            ];

            $ch = curl_init($url);
            curl_setopt_array($ch, $optionsArray);

            $result = curl_exec($ch);
            curl_close($ch);
        }

        if ($result === false) {
            throw new BaseException('Empty result', 110);
        } else {
            return $result;
        }
    }

    /**
     * @param string|null $url
     *
     * @return string
     * @throws BaseException
     */
    public static function loadHTMLFromURL($url)
    {
        return self::loadFromURL($url);
    }
}