<?php

declare(strict_types=1);

namespace App\Presentation\Core;

class Response
{
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function view(string $viewPath, array $data = []): void
    {
        $originalData = $data;

        if (isset($data['data']) && is_array($data['data'])) {
            $data = array_merge($data, $data['data']);
        }

        extract($data);

        $data = $originalData;

        $fullPath = dirname(__DIR__, 3) . '/views/' . $viewPath . '.php';

        if (!file_exists($fullPath)) {
            http_response_code(404);
            echo "View not found: " . $viewPath;
            exit;
        }

        require $fullPath;
        exit;
    }

    public static function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}