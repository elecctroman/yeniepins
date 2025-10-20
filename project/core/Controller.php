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
}
