<?php

namespace Marrios\Router\Entities;

class Url
{
    private String $url;

    public function __construct(String $url)
    {
        $this->url = $this->clearUrl($url);
    }

    public function get()
    {
        return $this->url;
    }

    /**
     * This function removes the "/" from the beginning 
     * of the string and from the end of the passed string
     * 
     * @example [
     * clearUrl("/site/image/") -> "site/image" 
     * 
     * @param String $url
     * @return String $url
     */
    public function clearUrl(String $url)
    {
        if(strlen($url) <= 1)
        {
            return "/";
        }
        
        if($url[0] == "/"){
            $url[0] = " ";
        }

        if($url[strlen($url)-1] == "/"){
            $url[strlen($url)-1] = " ";
        }

        return trim($url);
    }
}