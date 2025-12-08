<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Database\migrations\Migration;

// Load environment
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

try {
    // Connect to MySQL without database
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};charset={$_ENV['DB_CHARSET']}",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Create database if not exists
    $dbName = $_ENV['DB_NAME'];
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` 
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbName}`");
    
    echo "✓ Database '{$dbName}' created/selected\n";
    
    // Run migrations
    $migration = new Migration($pdo);
    
    echo "Running migrations...\n";
    $migration->createUsersTable();
    echo "✓ Users table created\n";
    
    $migration->createTicketsTable();
    echo "✓ Tickets table created\n";
    
    $migration->createCommentsTable();
    echo "✓ Comments table created\n";
    
    $migration->createAttachmentsTable();
    echo "✓ Attachments table created\n";
    
    $migration->createSessionsTable();
    echo "✓ Sessions table created\n";
    
    // Seed data
    echo "\nSeeding data...\n";
    $migration->seed();
    echo "✓ Default admin created (admin@helpdesk.com / admin123)\n";
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}