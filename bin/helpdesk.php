#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

$command = $argv[1] ?? null;

switch ($command) {
    case 'migrate':
        require __DIR__ . '/../database/migrations/migrate.php';
        break;

    default:
        echo "Helpdesk CLI\n";
        echo "Commands available:\n";
        echo "  migrate   Run database migration\n";
        break;
}
