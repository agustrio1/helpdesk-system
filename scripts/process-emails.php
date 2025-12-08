<?php
/**
 * Email Notification Processor
 * 
 * This script should be run via cron job every minute:
 * * * * * * php /path/to/your/project/scripts/process-emails.php >> /path/to/logs/email-cron.log 2>&1
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Services\EmailService;

// Load environment
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Start time
$startTime = microtime(true);
$logFile = __DIR__ . '/../storage/logs/email-cron-' . date('Y-m-d') . '.log';

// Ensure log directory exists
$logDir = dirname($logFile);
if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

// Log function
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[{$timestamp}] {$message}\n", FILE_APPEND);
    echo "[{$timestamp}] {$message}\n";
}

try {
    logMessage("Starting email processor...");
    
    // Initialize service
    $emailService = new EmailService();
    
    // Process pending emails (batch of 10)
    $results = $emailService->processPendingNotifications(10);
    
    logMessage("Processed {$results['total']} emails: {$results['sent']} sent, {$results['failed']} failed");
    
    // Calculate execution time
    $executionTime = round((microtime(true) - $startTime) * 1000, 2);
    logMessage("Completed in {$executionTime}ms");
    
    exit(0);
    
} catch (\Exception $e) {
    logMessage("ERROR: " . $e->getMessage());
    logMessage("Stack trace: " . $e->getTraceAsString());
    exit(1);
}