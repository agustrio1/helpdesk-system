#!/usr/bin/env php
<?php
/**
 * PHP Development Server dengan Auto Kill Process
 */

$host = '0.0.0.0';
$port = 8000;
$docRoot = __DIR__ . '/public';

define('GREEN', "\033[0;32m");
define('RED', "\033[0;31m");
define('YELLOW', "\033[1;33m");
define('RESET', "\033[0m");

echo GREEN . "╔════════════════════════════════════════════════════════╗\n";
echo "║         Helpdesk Ticketing System Server              ║\n";
echo "╚════════════════════════════════════════════════════════╝" . RESET . "\n\n";

// Validasi folder
if (!is_dir($docRoot)) {
    echo RED . "Error: Directory $docRoot tidak ditemukan!" . RESET . "\n";
    exit(1);
}

// FORCE KILL semua process di port 8000
echo YELLOW . "Checking and killing process on port $port..." . RESET . "\n";
if (PHP_OS_FAMILY !== 'Windows') {
    // Kill semua process di port 8000
    exec("lsof -ti:$port 2>/dev/null | xargs kill -9 2>/dev/null");
    exec("fuser -k $port/tcp 2>/dev/null");
    sleep(2); // Tunggu process benar-benar mati
} else {
    exec("for /f \"tokens=5\" %a in ('netstat -aon ^| findstr :$port') do taskkill /F /PID %a 2>nul");
    sleep(2);
}

// Verify port sudah kosong
exec("lsof -ti:$port 2>/dev/null", $check);
if (!empty($check)) {
    echo RED . "Failed to kill process on port $port. Please kill manually:" . RESET . "\n";
    echo YELLOW . "pkill -f 'php -S'" . RESET . "\n";
    exit(1);
}

echo GREEN . "✓ Port $port is free" . RESET . "\n";
echo GREEN . "Starting server at http://$host:$port" . RESET . "\n";
echo YELLOW . "Press Ctrl+C to stop\n" . RESET . "\n";

// Jalankan server
chdir($docRoot);
passthru("php -S $host:$port", $exitCode);

if ($exitCode !== 0) {
    echo RED . "Server stopped with error" . RESET . "\n";
    exit($exitCode);
}