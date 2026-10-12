<?php
declare(strict_types=1);

namespace App\Presentation\Core;

class Request
{
    public function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);
        $uri = '/' . trim($uri, '/');

        return $uri === '/' ? '/' : $uri;
    }

    public function getBody(): array
    {
        $body = [];
        if ($this->getMethod() === 'GET') {
            foreach ($_GET as $key => $value) {
                $body[$key] = is_string($value) ? trim($value) : $value;
            }
        } elseif ($this->getMethod() === 'POST') {
            foreach ($_POST as $key => $value) {
                $body[$key] = is_string($value) ? trim($value) : $value;
            }
        }
        return $body;
    }

    public function getQueryParam(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }
}