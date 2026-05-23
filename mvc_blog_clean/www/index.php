<?php
require __DIR__ . '/../vendor/autoload.php';

use MyProject\Controllers\MainController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');
$parts = explode('/', $uri);

$action = $parts[0] ?? '';
$name = isset($parts[1]) ? urldecode($parts[1]) : 'Guest';

$controller = new MainController();

switch ($action) {
    case '':
        $controller->main();
        break;
    case 'hello':
        $controller->sayHello($name);
        break;
    case 'bye':
        $controller->sayBye($name);
        break;
    case 'about-me':
        $controller->aboutMe();
        break;
    default:
        http_response_code(404);
        echo 'Page not found';
}
