<?php
namespace MyProject\Controllers;

use MyProject\Models\Article;
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
        $articles = Article::findAll();
        $this->view->renderHtml('articles/main.php', ['articles' => $articles]);
    }

    public function sayHello(string $name): void
    {
        $this->view->renderHtml('hello.php', [
            'name' => $name,
            'title' => 'Page of greeting'
        ]);
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
