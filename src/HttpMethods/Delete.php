<?php

namespace Marrios\Router\HttpMethods;

use Marrios\Router\Route;

trait Delete
{
    public function delete(String $routePath, Array $routeAction)
    {
        $httpRoute  = new Route("DELETE", $routePath, $routeAction);
        $this->definedRoute = $httpRoute->set("DELETE", $routePath, $routeAction);
        return $this;
    }
}