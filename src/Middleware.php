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

        if(!is_null($middlewares)) {
            foreach($middlewares as $middleware) {
                if (class_exists($middleware)) {
                    $middlewareInstance = new $middleware();
                    if(method_exists($middlewareInstance, "handle")) {
                        $this->middlewareAccess = $middlewareInstance->handle();
                        if($this->middlewareAccess) {
                            $access = true;
                        }else{
                            $access = false;
                        }
                    }else{
                        throw new RouterException($middleware." not implemented correctly");
                    }
                }else{
                    throw new RouterException($middleware." does not exists");
                }
            }

            if($access) {
                $this->middlewareAccess = true;
            }else{
                $this->middlewareAccess = false;
            }
            return $this;
        }
    }
}