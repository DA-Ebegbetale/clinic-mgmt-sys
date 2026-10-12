<?php
declare(strict_types=1);

namespace App\Presentation\Middleware;

use App\Presentation\Exceptions\CsrfTokenException;
use App\Presentation\Core\Request;

class CsrfMiddleware
{
    public static function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            // Standard cryptographic token generation
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function verify(Request $request): void
    {
        // Only verify state-changing requests (POST, PUT, DELETE)
        if ($request->getMethod() === 'GET') {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $body = $request->getBody();
        $token = $body['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            // hash_equals() is required by to prevent timing attacks
            throw new CsrfTokenException('CSRF token validation failed. Request denied.');
        }
    }
}