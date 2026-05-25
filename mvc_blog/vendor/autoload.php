<?php
spl_autoload_register(function (string $className) {
    $path = __DIR__ . '/../src/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});
