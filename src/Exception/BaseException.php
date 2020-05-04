<?php


namespace App\Exception;


use Exception;
use Throwable;

class BaseException extends Exception
{
    protected $code    = 0;
    protected $message = 'Application base exception';

    protected array $codeMessageArray = [
        //        0-99      simple exceptions
        0 => 'Application base exception',
        //        100-199   competition exceptions
        //        200-299   TODO exceptions
        //        300-399   subscribe exceptions
    ];

    protected array $additionalCodeMessageArray = [];

    /**
     * Base constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        if ($message === "") {
            if (isset($this->codeMessageArray[$code])) {
                $message = $this->codeMessageArray[$code];
            } elseif (isset($this->additionalCodeMessageArray[$code])) {
                $message = $this->additionalCodeMessageArray[$code];
            } else {
                $code    = $this->code;
                $message = $this->message;
            }
        }

        parent::__construct($message, $code, $previous);
    }
}