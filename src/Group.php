<?php

namespace Marrios\Router;

trait Group
{
    private bool $runGroup = true;

    public function group(Array $routes) : void
    {
        foreach($routes as $route) {
            $route->run();
        }
        
        $this->runGroup = false;
        $this->middlewareAccess = true;
    }
}
