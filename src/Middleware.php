<?php

namespace Marrios\Router;

use Marrios\Router\Exceptions\RouterException;

trait Middleware 
{
    /**
     * @var Bool
     */
    public $middlewareAccess = true;

    /**
     * 
     */
    public function middleware(Array $middlewares) 
    {
        $access = false;

        if(is_null($middlewares)) {
           return false; 
        }

        foreach($middlewares as $middleware) {

            if (!class_exists($middleware)) {
                throw new RouterException($middleware ." does not exists");
            }

            $middlewareInstance = new $middleware();

            if(!method_exists($middlewareInstance, "handle")) {
                throw new RouterException($middleware ." not implemented correctly");
            }

            return $this->middlewareAccess = $middlewareInstance->handle();
        }
    }
}
