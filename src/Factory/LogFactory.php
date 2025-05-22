<?php 

namespace Sifat\WebScrapingCurl\Factory;

class LogFactory
{
    public static function saveLog($message)
    {
        $baseDir = dirname(__DIR__, 2);
        $logs = $baseDir . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($logs)) {
            mkdir($logs, 0777, true);
        }
        $date = date('Y-m-d');
        $dateTime = date('Y-m-d_H-i-s');
        $logFile = $logs . DIRECTORY_SEPARATOR . "$date.log";
        
        if (!file_exists($logFile)) {
            touch($logFile);
        }
        
        $logEntry = "[$dateTime] \n" . ' ' . $message . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}