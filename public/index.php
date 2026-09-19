<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Normalize case of base directory in REQUEST_URI for Windows/XAMPP case-insensitivity
if (isset($_SERVER['REQUEST_URI']) && preg_match('#^/ida(/.*)?$#i', $_SERVER['REQUEST_URI'], $matches)) {
    $_SERVER['REQUEST_URI'] = '/ida' . ($matches[1] ?? '');
}

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
