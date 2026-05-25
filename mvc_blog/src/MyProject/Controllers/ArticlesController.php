<?php
namespace MyProject\Controllers;

use MyProject\Models\Article;
use MyProject\View\View;

class ArticlesController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View(__DIR__ . '/../../../templates');
    }

    public function show(int $id): void
    {
        $article = Article::getById($id);

        if ($article === null) {
            $this->view->renderHtml('errors/404.php', [], 404);
            return;
        }

        $this->view->renderHtml('articles/view.php', [
            'article' => $article,
            'title' => $article->getName()
        ]);
    }

    public function edit(int $id): void
    {
        $article = Article::getById($id);

        if ($article === null) {
            $this->view->renderHtml('errors/404.php', [], 404);
            return;
        }

        $this->view->renderHtml('articles/edit.php', [
            'article' => $article,
            'title' => 'Edit: ' . $article->getName()
        ]);
    }

    public function update(int $id): void
    {
        $article = Article::getById($id);

        if ($article === null) {
            $this->view->renderHtml('errors/404.php', [], 404);
            return;
        }

        $article->setName($_POST['name']);
        $article->setText($_POST['text']);
        $article->save();

        header('Location: /articles/' . $id);
        exit();
    }
}
