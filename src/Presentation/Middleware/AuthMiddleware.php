<?php
declare(strict_types=1);

namespace App\Presentation\Middleware;

use App\Presentation\Core\Request;
use App\Presentation\Core\Response;
use App\Presentation\Core\Session;

class AuthMiddleware
{
    public static function check(Request $request): void
    {
        Session::start();

        if (empty($_SESSION['user_uuid'])) {
            Session::setFlash('error', 'You must be logged in to access this page.');
            Response::redirect('/login'); // Sends them back to the login page
        }
    }
}