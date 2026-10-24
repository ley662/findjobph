<?php

/**
 * Vercel Serverless Function Entry Point for Laravel
 */

// Check if request is for a static asset inside /public/
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
$publicFile = __DIR__ . '/../public' . $uri;

if (!empty($uri) && $uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    $mime = match ($ext) {
        'css' => 'text/css; charset=utf-8',
        'js' => 'application/javascript; charset=utf-8',
        'json' => 'application/json',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        default => 'application/octet-stream',
    };

    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=31536000, immutable');
    header('Content-Length: ' . filesize($publicFile));
    readfile($publicFile);
    exit;
}

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
        $sourceDb = __DIR__ . '/../database/database.sqlite';
        if (file_exists($sourceDb)) {
            @copy($sourceDb, $sqliteDb);
        } else {
            @touch($sqliteDb);
        }
    }
}

require __DIR__ . '/../public/index.php';
