<?php

namespace Marrios\Router;

trait ErrorMessage
{
    public function renderErrorMessage(\Exception $exception)
    {
        session_start();
        $_SESSION["exception"] = $exception;
        require_once("ViewErrors/Exception.php");
    }
}