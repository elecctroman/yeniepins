<?php

namespace Core;

abstract class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    protected function render(string $template, array $data = []): void
    {
        echo $this->view->render($template, $data);
    }

    protected function json(array $data, int $status = 200): void
    {
        Response::json($data, $status);
    }

    protected function redirect(string $url): void
    {
        Response::redirect($url);
    }

    protected function input(): array
    {
        $data = array_merge($_GET ?? [], $_POST ?? []);
        return Security::sanitizeArray($data);
    }
}
