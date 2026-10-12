<?php
declare(strict_types=1);

namespace App\Presentation\Core;

use App\Presentation\Exceptions\CsrfTokenException;
use App\Infrastructure\Logging\FileLogger;
use InvalidArgumentException;
use Throwable;

class ExceptionHandler
{
    public static function handle(Throwable $exception): void
    {
        $logger = new FileLogger();

        // Log the secure, detailed error for the developer
        $logger->error($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]);

        // Clear any half-rendered HTML
        if (ob_get_length()) {
            ob_clean();
        }

        if ($exception instanceof CsrfTokenException) {
            http_response_code(403);

            if (
                isset($_SERVER['HTTP_ACCEPT']) &&
                str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')
            ) {
                Response::json([
                    'error' => 'The security token is invalid or has expired.'
                ], 403);
            }

            Response::view('errors/403');
        }

        if ($exception instanceof \InvalidArgumentException) {
            http_response_code(400);
            if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
                Response::json(['error' => $exception->getMessage()], 400);
            }
            try {
                Response::view('errors/400');
            } catch (Throwable) {
                echo "<h1>400 - Bad Request</h1><p>{$exception->getMessage()}</p>";
            }
            exit;
        }

        // Show a safe, generic response to the user
        http_response_code(500);

        // If it's an API/JSON request, return JSON
        if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
            Response::json(['error' => 'An internal server error occurred. Please try again later.'], 500);
        }

        // Otherwise, attempt to render the dedicated error view
        try {
            Response::view('errors/500');
        } catch (Throwable) {
            // Hard fallback if the view template engine itself is broken
            echo "<h1>500 - Internal Server Error</h1><p>Something went wrong. Our team has been notified.</p>";
        }
        exit;
    }
}