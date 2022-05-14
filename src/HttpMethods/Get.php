<?php

namespace Marrios\Router\HttpMethods;

use Marrios\Router\Route;

trait Get
{
    public function get(String $routePath, Array $routeAction)
    {
        $httpRoute  = new Route("GET", $routePath, $routeAction);
        $this->definedRoute = $httpRoute->set("GET", $routePath, $routeAction);
        return $this;
    }
}