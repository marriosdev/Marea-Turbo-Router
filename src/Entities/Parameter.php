<?php

namespace Marrios\Router\Entities;

class Parameter
{

    private Mixed $parameter;

    private Mixed $dirtyParameter;

    public function __construct(Mixed $parameter)
    {
        $this->dirtyParameter   = $parameter; 
        $this->parameter        = $this->clear($parameter);  

    }

    public function clear($parameter)
    {
        return preg_replace("/[{}]/", "", $parameter);
    }
    
    public function get()
    {
        return $this->parameter;
    }
    
    public function valid()
    {
        if(preg_match("/[{}]/", $this->dirtyParameter)){
            return true;
        }
    }
}