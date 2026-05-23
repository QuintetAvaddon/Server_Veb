<?php
namespace MyProject\Controllers;

use MyProject\View\View;

class MainController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View(__DIR__ . '/../../../templates');
    }

    public function main(): void
    {
        $articles = [
            ['title' => 'Article 1', 'text' => 'Hello, this is text of first article'],
            ['title' => 'Article 2', 'text' => 'Hello, this is text of second article']
        ];
        $this->view->renderHtml('main.php', ['articles' => $articles]);
    }

    public function sayHello(string $name): void
    {
        $this->view->renderHtml('hello.php', ['name' => $name]);
    }

    public function sayBye(string $name): void
    {
        $this->view->renderHtml('bye.php', ['name' => $name]);
    }

    public function aboutMe(): void
{
    $this->view->renderHtml('about-me.php');
}
}

