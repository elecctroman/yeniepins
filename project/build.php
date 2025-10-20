<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo 'Bu betik sadece komut satırından çalıştırılabilir.';
    return;
}

$root = __DIR__;
$cssDir = $root . '/public/assets/css';
$jsDir = $root . '/public/assets/js';
$cacheDir = $root . '/storage/cache';

$rawArgv = $argv ?? ($_SERVER['argv'] ?? []);
$options = is_array($rawArgv) ? array_slice($rawArgv, 1) : [];
$clearCache = in_array('--clear-cache', $options, true);

if ($clearCache) {
    echo "[build] Cache temizleniyor...\n";
    clearCacheDirectory($cacheDir);
}

minifyDirectory($cssDir, 'css');
minifyDirectory($jsDir, 'js');

echo "[build] Tamamlandı.\n";

function minifyDirectory(string $directory, string $type): void
{
    if (!is_dir($directory)) {
        echo "[build] Klasör bulunamadı: {$directory}\n";
        return;
    }

    $pattern = $type === 'css' ? '*.css' : '*.js';
    foreach (glob($directory . '/' . $pattern) as $path) {
        if (preg_match('/\.min\.' . $type . '$/i', $path)) {
            continue;
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            echo "[build] Dosya okunamadı: {$path}\n";
            continue;
        }

        $minified = $type === 'css' ? minifyCss($contents) : minifyJs($contents);
        $target = preg_replace('/\.' . $type . '$/i', '.min.' . $type, $path);
        if ($target === null) {
            echo "[build] Çıktı yolu üretilemedi: {$path}\n";
            continue;
        }

        file_put_contents($target, $minified);
        echo "[build] \033[32m✓\033[0m {$target} üretildi.\n";
    }
}

function minifyCss(string $contents): string
{
    $contents = preg_replace('/\/\*[^!][\s\S]*?\*\//', '', $contents); // /*! ... */ hariç yorumları sil
    $contents = preg_replace('/\s+/', ' ', $contents);
    $contents = preg_replace('/\s*([{};:,>])\s*/', '$1', $contents);
    $contents = preg_replace('/;}/', '}', $contents);
    return trim($contents);
}

function minifyJs(string $contents): string
{
    $contents = preg_replace('!/\*.*?\*/!s', '', $contents); // blok yorumlar
    $lines = preg_split('/\r?\n/', $contents) ?: [];
    $buffer = [];
    foreach ($lines as $line) {
        $line = preg_replace('/(^|\s)\/\/.*$/', '$1', $line); // satır sonu yorumları
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $buffer[] = $line;
    }
    $contents = implode(' ', $buffer);
    $contents = preg_replace('/\s*([{}();,:+\-\*<>=])\s*/', '$1', $contents);
    return trim($contents);
}

function clearCacheDirectory(string $directory): void
{
    if (!is_dir($directory)) {
        echo "[build] Cache klasörü bulunamadı: {$directory}\n";
        return;
    }

    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $fileInfo) {
        $path = $fileInfo->getPathname();
        if ($fileInfo->isDir()) {
            if (@rmdir($path)) {
                echo "[build] Klasör silindi: {$path}\n";
            }
            continue;
        }
        if (basename($path) === '.gitignore') {
            continue;
        }
        if (@unlink($path)) {
            echo "[build] Dosya silindi: {$path}\n";
        }
    }
}
