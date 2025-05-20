<?php

namespace router\viewLoader;

class ViewLoader
{
    public static function load(string $viewPath, array $data = []): string
    {
        $viewPath;

        if (!file_exists($viewPath)) {
            throw new \Exception("View não encontrada: $viewPath");
        }

        $ext = pathinfo($viewPath, PATHINFO_EXTENSION);

        if ($ext === "php") {
            extract($data);
            ob_start();
            include $viewPath;
            return ob_get_clean();
        }

        return file_get_contents($viewPath);
    }
}
