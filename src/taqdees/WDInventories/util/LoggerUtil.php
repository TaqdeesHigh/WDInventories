<?php
declare(strict_types=1);

namespace taqdees\WDInventories\util;

use pocketmine\plugin\PluginLogger;

class LoggerUtil {
    /** @var PluginLogger */
    private PluginLogger $logger;
    
    public function __construct(PluginLogger $logger) {
        $this->logger = $logger;
    }
    
    /**
     * Log info level message
     */
    public function info(string $message): void {
        $this->logger->info($message);
    }
    
    /**
     * Log error level message
     */
    public function error(string $message): void {
        $this->logger->error($message);
    }
    
    /**
     * Log debug level message
     */
    public function debug(string $message): void {
        $this->logger->debug($message);
    }
    
    /**
     * Log warning level message
     */
    public function warning(string $message): void {
        $this->logger->warning($message);
    }
}