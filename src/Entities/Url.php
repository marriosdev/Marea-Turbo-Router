<?php

namespace Marrios\Router\Entities;

class Url
{
    private String $url;

    public function __construct(String $url)
    {
        $this->url = $url;
    }

    public function get()
    {
        return $this->url;
    }
}