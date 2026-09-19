<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Normalize localhost / XAMPP subfolder environment
if (isset($_SERVER['HTTP_HOST']) && (str_contains($_SERVER['HTTP_HOST'], 'localhost') || str_contains($_SERVER['HTTP_HOST'], '127.0.0.1'))) {
    // Normalize case of base directory in REQUEST_URI for Windows/XAMPP
    if (isset($_SERVER['REQUEST_URI']) && preg_match('#^/ida(/.*)?$#i', $_SERVER['REQUEST_URI'], $matches)) {
        $_SERVER['REQUEST_URI'] = '/ida' . ($matches[1] ?? '');
    }

    // Ensure bare /ida without trailing slash matches home route
    if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/ida') {
        $_SERVER['REQUEST_URI'] = '/ida/';
    }

    // If request was rewritten to public/index.php without public/ in REQUEST_URI,
    // normalize SCRIPT_NAME and PHP_SELF so Symfony accurately detects the /ida subfolder base URL
    if (isset($_SERVER['SCRIPT_NAME']) && str_contains($_SERVER['SCRIPT_NAME'], '/public/index.php') && !str_contains($_SERVER['REQUEST_URI'] ?? '', '/public/')) {
        $_SERVER['SCRIPT_NAME'] = str_replace('/public/index.php', '/index.php', $_SERVER['SCRIPT_NAME']);
        if (isset($_SERVER['PHP_SELF'])) {
            $_SERVER['PHP_SELF'] = str_replace('/public/index.php', '/index.php', $_SERVER['PHP_SELF']);
        }
    }
}


// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
