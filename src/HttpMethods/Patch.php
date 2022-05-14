<?php

namespace Marrios\Router\HttpMethods;

use Marrios\Router\Route;

trait Patch
{
    public function patch(String $routePath, Array $routeAction)
    {
        $httpRoute  = new Route("PATCH", $routePath, $routeAction);
        $this->definedRoute = $httpRoute->set("PATCH", $routePath, $routeAction);
        return $this;
    }
}