<?php

namespace Marrios\Router\Exceptions;

use Exception;
use Throwable;

class RouterLogsException extends Exception
{
    public function __construct(String $message, $code = 0, Throwable $previous = null){
        parent::__construct($message, $code, $previous);
    }
}