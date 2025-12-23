<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Database\migrations\Migration;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=================================\n";
echo "  Database Migration\n";
echo "=================================\n\n";

try {
    // Load environment
    echo "→ Loading environment variables...\n";
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
    
    // Check required env vars
    $required = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS'];
    foreach ($required as $var) {
        if (empty($_ENV[$var])) {
            throw new Exception("Missing environment variable: {$var}");
        }
    }
    
    echo "✓ Environment loaded\n\n";
    
    // Display connection info (hide password)
    echo "→ Database Configuration:\n";
    echo "  Host: {$_ENV['DB_HOST']}\n";
    echo "  Port: {$_ENV['DB_PORT']}\n";
    echo "  Database: {$_ENV['DB_NAME']}\n";
    echo "  User: {$_ENV['DB_USER']}\n\n";
    
    // Connect to MySQL without database
    echo "→ Connecting to MySQL server...\n";
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
    
    echo "✓ Connected to MySQL\n\n";
    
    // Create database if not exists
    $dbName = $_ENV['DB_NAME'];
    echo "→ Creating database '{$dbName}' if not exists...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` 
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbName}`");
    
    echo "✓ Database ready\n\n";
    
    // Run migrations
    echo "→ Running migrations...\n";
    $migration = new Migration($pdo);
    
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
    echo "\n→ Seeding data...\n";
    $migration->seed();
    echo "✓ Admin user created\n";
    echo "  Email: admin@helpdesk.com\n";
    echo "  Password: admin123\n";
    
    echo "\n=================================\n";
    echo "✓ Migration completed!\n";
    echo "=================================\n\n";
    
    exit(0); // Success
    
} catch (PDOException $e) {
    echo "\n❌ Database Error:\n";
    echo "  " . $e->getMessage() . "\n\n";
    exit(1); // Error
    
} catch (Exception $e) {
    echo "\n❌ Error:\n";
    echo "  " . $e->getMessage() . "\n\n";
    exit(1); // Error
}