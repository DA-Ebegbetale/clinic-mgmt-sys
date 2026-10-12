<?php
declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Domain\Security\SecurityAuditLog;
use App\Domain\Security\SecurityAuditRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Security\SecurityContextHelper;

use App\Application\Auth\DTOs\LoginDTO;
use App\Application\Auth\Services\LoginService;

use App\Application\Shared\Validators\Validator;
use App\Presentation\Core\Request;
use App\Presentation\Core\Response;
use App\Presentation\Core\Session;

class AuthController
{
    private LoginService $loginService;
    private SecurityAuditRepositoryInterface $securityAuditRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        LoginService $loginService,
        SecurityAuditRepositoryInterface $securityAuditRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->loginService = $loginService;
        $this->securityAuditRepository = $securityAuditRepository;
        $this->userRepository = $userRepository;
    }

    public function showLoginForm(Request $request): void
    {
        Response::view('auth/login');
    }

    public function login(Request $request): void
    {
        try {
            $data = $request->getBody();
            $email = Validator::validateEmail($data['email'] ?? '');
            $password = Validator::validateRequiredString($data['password'] ?? '', 'Password');

            $dto = new LoginDTO($email, $password);
            $user = $this->loginService->execute($dto);

            session_regenerate_id(true);
            $_SESSION['user_uuid'] = $user->getUuid();
            $_SESSION['user_role'] = $user->getRole();
            $_SESSION['last_activity'] = time();

            Session::setFlash('success', 'Welcome back!');
            Response::redirect('/dashboard');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/login');
        }
    }


    public function logout(Request $request): void
    {
        if (isset($_SESSION['user_uuid'])) {
            // Get user before destroying session
            $user = $this->userRepository->findByUuid($_SESSION['user_uuid']);

            if ($user) {
                $log = SecurityAuditLog::logEvent(
                    eventType: 'LOGOUT',
                    userId: $user->getId(),
                    userUuid: $user->getUuid(),
                    ipAddress: SecurityContextHelper::getClientIp(),
                    userAgent: SecurityContextHelper::getUserAgent(),
                    details: 'User logged out'
                );
                $this->securityAuditRepository->save($log);
            }
        }
        Session::destroy();
        Session::start();
        Session::setFlash('success', 'You have been securely logged out.');
        Response::redirect('/login');
    }
}