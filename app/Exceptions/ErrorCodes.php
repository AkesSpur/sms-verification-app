<?php

namespace App\Exceptions;

Use App\Exceptions\RequestError;

class ErrorCodes extends RequestError
{
    public function checkExist($errorCode)
    {
        return array_key_exists($errorCode, $this->errorCodes);
    }
}