<?php

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Phalcon\Loader;
use function Niden\Functions\appPath;

// Register the auto loader
require __DIR__ . '/functions.php';

$loader     = new Loader();
$namespaces = [
    'Niden'                 => appPath('/library'),
    'Niden\Api\Controllers' => appPath('/api/controllers'),
    'Niden\Tests'           => appPath('/tests'),
];

$loader->registerNamespaces($namespaces);

$loader->register();

/**
 * Composer Autoloader
 */
require appPath('/vendor/autoload.php');

// Load environment
(new Dotenv(appPath()))->load();
