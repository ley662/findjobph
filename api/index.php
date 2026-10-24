<?php

/**
 * Vercel Serverless Function Entry Point for Laravel
 */

$tmpDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// If using SQLite and database does not exist in /tmp, copy or create it
if (getenv('DB_CONNECTION') === 'sqlite' || empty(getenv('DB_CONNECTION'))) {
    $sqliteDb = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';
    if (!file_exists($sqliteDb) && str_starts_with($sqliteDb, '/tmp')) {
        @touch($sqliteDb);
    }
}

require __DIR__ . '/../public/index.php';
