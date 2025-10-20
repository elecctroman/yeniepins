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

        ob_start();
        include $viewPath;
        return ob_get_clean() ?: '';
    }
}
