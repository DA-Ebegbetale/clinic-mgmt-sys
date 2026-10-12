<?php
declare(strict_types=1);

namespace App\Presentation\Middleware;

use App\Presentation\Core\Request;
use App\Presentation\Core\Response;
use App\Presentation\Core\Session;

class AdminMiddleware
{
    public static function check(Request $request): void
    {
        Session::start();

        if (empty($_SESSION['user_uuid']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            Session::setFlash('error', 'Unauthorized action. Administrator privileges required.');
            Response::redirect('/login');
        }
    }
}