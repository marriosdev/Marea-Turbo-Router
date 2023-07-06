<?php

namespace Marrios\Router\Logs;

use DateTime;
use Marrios\Router\Entities\Url;

class Log
{
    public function __construct(
        public String $ip,
        public DateTime $dateTime,
        public Url $url,
        public float $timeTaskExecution,
        public float $allProcessExecutionTime,
    ){
    }
}
