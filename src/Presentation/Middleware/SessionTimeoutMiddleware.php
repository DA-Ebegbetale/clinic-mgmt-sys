<?php
declare(strict_types=1);

namespace App\Presentation\Middleware;

use App\Presentation\Core\Request;
use App\Presentation\Core\Response;
use App\Presentation\Core\Session;

class SessionTimeoutMiddleware
{
    private const TIMEOUT_SECONDS = 900;

    public static function check(Request $request): void
    {
        Session::start();

        if (empty($_SESSION['user_uuid'])) {
            return;
        }

        $lastActivity = (int) ($_SESSION['last_activity'] ?? time());
        $secondsInactive = time() - $lastActivity;

        if ($secondsInactive >= self::TIMEOUT_SECONDS) {
            Session::destroy();
            Session::start();
            Session::setFlash('error', 'Your session has expired due to inactivity. Please log in again.');
            Response::redirect('/login');
        }

        Session::touch();
    }
}