<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../lib/MercadoLivre/meli.php';

use Symfony\Component\Debug\Debug;

error_reporting(-1);
Debug::enable();

ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_depth', -1);

if ('cli-server' === php_sapi_name() && isset($_SERVER['REQUEST_URI'])) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = urldecode($path);
    $file = __DIR__.$path;
    if ($path !== '/' && file_exists($file)) {
        return false;
    }
}

$app = new PromotedListings\Http\Application();

$app->run();
