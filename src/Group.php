<?php

namespace Marrios\Router;
use Closure;

trait Group
{
    public function group(Closure $closure) : void
    {
        $closure($this);
    }
}
