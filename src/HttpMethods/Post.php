<?php

namespace Marrios\Router\HttpMethods;

use Marrios\Router\Route;

trait Post
{
    public function post(String $routePath, Array $routeAction)
    {
        $httpRoute  = new Route("POST", $routePath, $routeAction);
        $this->definedRoute = $httpRoute->set("POST", $routePath, $routeAction);
        return $this;
    }
}