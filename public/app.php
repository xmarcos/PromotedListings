<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../lib/MercadoLivre/meli.php';

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
