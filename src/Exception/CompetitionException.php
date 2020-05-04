<?php


namespace App\Exception;


class CompetitionException extends BaseException
{
    protected $code    = 100;
    protected $message = 'Default competition exception';

    protected array $additionalCodeMessageArray = [
        100 => 'Default competition exception',
        110 => 'Can\'t load data from PASC Service',
        111 => 'New load data from PASC Service isn\'t',
    ];

}