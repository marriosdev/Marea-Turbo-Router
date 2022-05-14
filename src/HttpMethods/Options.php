<?php

namespace Marrios\Router\HttpMethods;

use Marrios\Router\Route;

trait Options
{
    public function options(String $routePath, Array $routeAction)
    {
        $httpRoute  = new Route("OPTIONS", $routePath, $routeAction);
        $this->definedRoute = $httpRoute->set("OPTIONS", $routePath, $routeAction);
        return $this;
    }
}