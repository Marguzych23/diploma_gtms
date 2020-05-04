<?php


namespace App\Service;


class DataUtilService
{

    /**
     * @param string $xml
     *
     * @return array
     */
    public static function convertXMLtoArray(string $xml = '')
    {
        return json_decode(json_encode((array) simplexml_load_string($xml)), 1);
    }

    public static function convertJSONtoArray(string $json = '')
    {
        return json_decode($json);
    }

    /**
     * @param string $text
     * @param string $patten
     * @param string $replacement
     *
     * @return string|string[]|null
     */
    public static function pregReplace(string $text, string $patten = "/[^\s\p{Cyrillic}]/ui", string $replacement = " ")
    {
        return preg_replace($patten, $replacement, $text);
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @return string|string[]
     */
    public static function replace(string $search = "", string $replace = "", string $subject = "")
    {
        return str_replace($search, $replace, $subject);
    }

    /**
     * @param string $string
     *
     * @return false|string|string[]|null
     */
    public static function stringToLower(string $string)
    {
        return mb_strtolower($string);
    }

    /**
     * @param array $data
     * @param array $columns
     *
     * @return array
     */
    public static function getColumnsValuesArrays(array $data, array $columns)
    {
        $result = [];

        foreach ($columns as $column) {
            $result = array_merge($result, array_column($data, $column));
        }

        return $result;
    }

    /**
     * Only for php 7.4
     *
     * @param array  $data
     * @param string $glue
     *
     * @return string
     */
    public static function implode(array $data, string $glue = " ")
    {
        return implode($data, $glue);
    }

    /**
     * @param string $delimiter
     * @param string $string
     *
     * @return array
     */
    public static function explode(string $delimiter, string $string)
    {
        return explode($delimiter, $string);
    }

    /**
     * @param array  $data
     *
     * @param string $needle
     *
     * @return array
     */
    public static function clearArray(array $data, string $needle = "")
    {
        while (($key = array_search($needle, $data)) !== false) {
            unset($data[$key]);
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public static function getUnique(array $data)
    {
        return array_unique($data);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public static function naturalOrderSort(array $data)
    {
        natsort($data);

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public static function trimTextInArray(array $data)
    {
        $result = [];

        foreach ($data as $value) {
            $res = urldecode(self::replace("%C2%A0", "", urlencode($value)));
            if (strlen($res) !== 0) {
                $result[] = $res;
            }
        }

        return $result;
    }


}