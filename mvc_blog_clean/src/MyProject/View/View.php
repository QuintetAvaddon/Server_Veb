<?php
namespace MyProject\View;

class View
{
    private string $templatesPath;

    public function __construct(string $templatesPath)
    {
        $this->templatesPath = $templatesPath;
    }

    public function renderHtml(string $templateName, array $vars = [], ?string $title = null): void
    {
        extract($vars);
        if ($title === null) {
            $title = $vars['title'] ?? 'My Blog';
        }
        include $this->templatesPath . '/' . $templateName;
    }
}
