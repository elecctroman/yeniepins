<?php

namespace Core;

class View
{
    public function render(string $template, array $data = []): string
    {
        $viewPath = __DIR__ . '/../app/Views/' . $template . '.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException('Şablon bulunamadı: ' . $template);
        }

        extract($data, EXTR_SKIP);
        $view = $this;

        ob_start();
        include $viewPath;
        return ob_get_clean() ?: '';
    }

    public function partial(string $template, array $data = []): string
    {
        return $this->render('partials/' . ltrim($template, '/'), $data);
    }

    public function escape(mixed $value): string
    {
        return Security::escape((string)$value);
    }
}
