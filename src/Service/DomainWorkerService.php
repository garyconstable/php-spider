<?php

namespace App\Service;

class DomainWorkerService
{
    /**
     * Execute command and return PID
     * ==
     * @param string $url
     * @return string
     */
    public function run($url = '')
    {
        $dir =  rtrim(dirname(__DIR__, 2), '/') ;
        $command = "php " . $dir . "/bin/console spider:worker:domain ".$url." > /dev/null 2>&1 & echo $!;";
        $pid = exec($command, $output);
        return $pid;
    }
}