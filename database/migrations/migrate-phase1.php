<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Database\migrations\Phase1Migration;

// Load environment
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']};charset={$_ENV['DB_CHARSET']}",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    echo "=====================================\n";
    echo "  PHASE 1 MIGRATION - HELPDESK      \n";
    echo "=====================================\n\n";
    
    // Run Phase 1 migrations
    $migration = new Phase1Migration($pdo);
    $results = $migration->runAll();
    
    echo "\n=====================================\n";
    echo "✓ Phase 1 migration completed!\n";
    echo "=====================================\n\n";
    
    echo "New features available:\n";
    echo "  • Search & Filter Tickets\n";
    echo "  • Ticket Categories\n";
    echo "  • Activity Logs\n";
    echo "  • Email Notifications\n";
    echo "  • User Profile Management\n\n";
    
} catch (PDOException $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}