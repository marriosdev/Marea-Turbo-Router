<?php

namespace Marrios\Router\HttpMethods;

use Marrios\Router\Route;

trait Head
{
    public function head(String $routePath, Array $routeAction)
    {
        $httpRoute  = new Route("HEAD", $routePath, $routeAction);
        $this->definedRoute = $httpRoute->set("HEAD", $routePath, $routeAction);
        return $this;
    }
}