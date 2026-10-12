<?php
declare(strict_types=1);

namespace App\Infrastructure\Logging;

class FileLogger
{
    private string $logFile;

    public function __construct()
    {
        // Points to /storage/logs/error.log at the root
        $this->logFile = dirname(__DIR__, 3) . '/storage/logs/error.log';
        
        // Ensure directory exists
        if (!is_dir(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }

    public function error(string $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? json_encode($context) : '';
        $logEntry = "[{$date}] ERROR: {$message} {$contextString}" . PHP_EOL;
        
        // FILE_APPEND ensures we don't overwrite previous logs
        // LOCK_EX prevents two concurrent requests from corrupting the file
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}