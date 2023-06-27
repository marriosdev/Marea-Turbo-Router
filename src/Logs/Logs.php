<?php

namespace Marrios\Router\Logs;
use Marrios\Router\Exceptions\RouterLogsException;
use Marrios\Router\HttpRouter;

trait Logs
{
    public bool $activeLogs;
    public string $storageLogs;
    
    public function setStorageLogs(String $path) 
    {
        $this->storageLogs = (substr($path, -1) != '/') ? $path . '/' : $path  ;
        $this->storageLogs .= "router.log";
        fopen($this->storageLogs, "a+");
        return $this;
    }
    
    public function logs(bool $logs) 
    {
        $this->active = $logs;
        return $this;
    }

    public function startLogs(HttpRouter $routerInstance ) 
    {
        if(!$this->active) {
            return false;
        }

        if($this->storageLogs == "") {
            throw new RouterLogsException("Storage log undefined");
        }

        $log = new Log(
            ip: $_SERVER["REMOTE_ADDR"],
            dateTime: new \DateTime(),
            url: $routerInstance->currentUrl
        );

        $this->register($log);
    }

    private function formatLog(Log $log) : String 
    {
        $log = "[{$log->dateTime->format('Y-m-d H:i:s')}] - {$log->ip} - {$log->url->get()}\n";
        return $log;
    }

    private function register(Log $log) : void 
    {
        $log = $this->formatLog($log);
        file_put_contents($this->storageLogs, $log, FILE_APPEND);
    }
}
