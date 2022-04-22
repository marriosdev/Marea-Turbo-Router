<?php

class Router {
 
    private $routes;

    public function set(String $verb, String $path, Array $execute)
    {
        $this->routes[] = [
            "path"  => $path,
            "verb"  => $verb,
            "execute" => $execute
        ];
    }

    public function run()
    {
        $currentUrl = $this->_clearUrl($_SERVER["REQUEST_URI"]);

        foreach($this->routes as $key=>$value)
        {
            $definedUrl = $this->_clearUrl($value["path"]);
            $urlsMatched = $this->_matched($currentUrl, $definedUrl);
            
            if($urlsMatched){
                $this->_executeMethod($value["execute"][0], $value["execute"][1]);
            }
        }
    }

    private function _executeMethod($class, $method, $param = null)
    {
        $class = new $class();
        $class->$method();
    }

    private function _clearUrl($url)
    {
        if($url[0] == "/"){
            $url[0] = " ";
            $url = trim($url);
        }

        if($url[strlen($url)-1] == "/"){
            $url[strlen($url)-1] = " ";
            $url = trim($url);
        }
        return $url;
    }

    public function _matched($currentUrl, $definedUrl)
    {
   
        $currentUrl = explode("/", $currentUrl);
        $definedUrl = explode("/", $definedUrl);
        
        if(count($currentUrl) != count($definedUrl)){
            return false;
        }

        $countMatch = count($definedUrl);

        for($i=0; $i < $countMatch; $i++){
            if($definedUrl[$i] !=  $currentUrl[$i]){
                if(!preg_match("/[{}]/", $definedUrl[$i])){
                    return false;;
                }
            }
        }
        return true;
    }
}