<?php

namespace App\Exception;

class BadParamsException extends \Exception
{
    public function __construct(array $arrayOfMessage)
    {
        parent::__construct(json_encode($arrayOfMessage) ?? '', 400);
    }
}