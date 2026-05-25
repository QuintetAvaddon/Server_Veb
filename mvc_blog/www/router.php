<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);
$file = __DIR__ . $uri;

if ($uri !== '/' && file_exists($file) && !str_ends_with($uri, '.php')) {
    return false;
}

require __DIR__ . '/index.php';
