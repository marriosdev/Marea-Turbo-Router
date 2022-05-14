<?php

namespace Marrios\Router\HttpMethods;

use Marrios\Router\Route;

trait Put
{
    public function put(String $routePath, Array $routeAction)
    {
        $httpRoute  = new Route("PUT", $routePath, $routeAction);
        $this->definedRoute = $httpRoute->set("PUT", $routePath, $routeAction);
        return $this;
    }
}