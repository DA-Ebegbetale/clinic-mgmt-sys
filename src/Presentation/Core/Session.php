<?php
declare(strict_types=1);

namespace App\Presentation\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_strict_mode', '1');

            session_set_cookie_params([
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
                'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            ]);

            session_start();
        }
    }

    public static function touch(): void
    {
        self::start();
        $_SESSION['last_activity'] = time();
    }

    public static function setFlash(string $key, mixed $value): void
    {
        self::start();
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key): mixed
    {
        self::start();

        if (!isset($_SESSION['_flash'][$key])) {
            return null;
        }

        $value = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);

        return $value;
    }

    public static function hasFlash(string $key): bool
    {
        self::start();
        return isset($_SESSION['_flash'][$key]);
    }

    public static function destroy(): void
    {
        self::start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}