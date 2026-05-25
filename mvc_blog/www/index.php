<?php
require __DIR__ . '/../vendor/autoload.php';

use MyProject\Controllers\MainController;
use MyProject\Controllers\ArticlesController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

// Route: article/{id}/edit
if (preg_match('~^article/(\d+)/edit$~', $uri, $matches)) {
    $controller = new ArticlesController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->update((int) $matches[1]);
    } else {
        $controller->edit((int) $matches[1]);
    }
    exit();
}

$parts = explode('/', $uri);
$action = $parts[0] ?? '';
$name = isset($parts[1]) ? urldecode($parts[1]) : 'Guest';

switch ($action) {
    case '':
        $controller = new MainController();
        $controller->main();
        break;
    case 'hello':
        $controller = new MainController();
        $controller->sayHello($name);
        break;
    case 'bye':
        $controller = new MainController();
        $controller->sayBye($name);
        break;
    case 'about-me':
        $controller = new MainController();
        $controller->aboutMe();
        break;
    case 'articles':
        $id = (int) $name;
        $controller = new ArticlesController();
        $controller->show($id);
        break;
    default:
        http_response_code(404);
        echo 'Page not found';
}
