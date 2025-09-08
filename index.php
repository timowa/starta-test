<?php
session_start();
ini_set('display_errors', 0);
require_once 'vendor/autoload.php';;
try {
    require_once __DIR__ . '/src/core/database.php';
    require_once __DIR__ . '/functions.php';
    include_once __DIR__ . '/src/routes.php';

} catch (Throwable $e) {
    echo "<pre>";
    print_r('[' . $e->getCode() . '] ' . $e->getMessage() . "\n");
    print_r($e->getFile() . ':' . $e->getLine() . "\n");
    echo "</pre>";
    die();
}
