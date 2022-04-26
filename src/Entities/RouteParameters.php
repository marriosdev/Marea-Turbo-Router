<?php

namespace Marrios\Router\Entities;

use Marrios\Router\Entities\Parameter;

class RouteParameters
{

    public Int $count;

    public function __construct()
    {
        $this->count = 0;
    }

    public function setParameter(Parameter $parameterName, Parameter $parameterValue)
    {
        $this->count++;
        $parameterName = $parameterName->get();
        $this->$parameterName = $parameterValue->get();
    }
}