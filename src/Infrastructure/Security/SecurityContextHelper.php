<?php
declare(strict_types=1);

namespace App\Infrastructure\Security;

class SecurityContextHelper
{
    public static function getClientIp(): ?string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = trim($_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = trim($_SERVER['REMOTE_ADDR']);
        } else {
            return null;
        }

        return $ip === '::1' ? '127.0.0.1' : $ip;
    }

    public static function getUserAgent(): ?string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }
}