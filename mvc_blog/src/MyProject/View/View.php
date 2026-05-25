<?php
namespace MyProject\View;

class View
{
    private string $templatesPath;

    public function __construct(string $templatesPath)
    {
        $this->templatesPath = $templatesPath;
    }

    public function renderHtml(string $templateName, array $vars = [], int $code = 200): void
    {
        http_response_code($code);
        extract($vars);
        $title = $vars['title'] ?? 'My Blog';
        include $this->templatesPath . '/' . $templateName;
    }
}
