<?php


namespace App\Exception;


class SubscribeException extends BaseException
{
    protected $code    = 300;
    protected $message = 'Default competition exception';

    protected array $additionalCodeMessageArray = [
        300 => 'Default subscribe exception',
        310 => 'User hasn\'t industries to subscribe',
    ];
}