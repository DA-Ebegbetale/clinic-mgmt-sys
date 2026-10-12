<?php
declare(strict_types=1);

namespace App\Application\Auth\Services;

use App\Domain\Security\SecurityAuditLog;
use App\Domain\Security\SecurityAuditRepositoryInterface;
use App\Infrastructure\Security\SecurityContextHelper;
use App\Application\Auth\DTOs\LoginDTO;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Security\BcryptPasswordHasher;
use RuntimeException;

class LoginService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private BcryptPasswordHasher $hasher,
        private SecurityAuditRepositoryInterface $securityAuditRepository
    ) {
    }
    public function execute(LoginDTO $dto): User
    {
        $user = $this->userRepository->findByEmail($dto->email);

        $deviceInfo = [
            'user_agent' => SecurityContextHelper::getUserAgent(),
            'ip_address' => SecurityContextHelper::getClientIp(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Check if user exists AND password is valid
        if (!$user || !$this->hasher->verify($dto->password, $user->getPasswordHash())) {
            // Log failed login attempt (invalid credentials)
            $log = SecurityAuditLog::logEvent(
                eventType: 'LOGIN_FAILED',
                userId: $user ? $user->getId() : null,
                userUuid: null,
                ipAddress: SecurityContextHelper::getClientIp(),
                userAgent: SecurityContextHelper::getUserAgent(),
                deviceInfo: json_encode($deviceInfo),
                details: 'Failed login attempt from' . SecurityContextHelper::getClientIp() . ' using ' . substr(SecurityContextHelper::getUserAgent() ?? '', 0, 50) .  '(Invalid Credentials)'
            );
            if ($user) {
                $log = SecurityAuditLog::logEvent(
                    eventType: 'LOGIN_FAILED',
                    userId: $user->getId(),
                    userUuid: $user->getUuid(),
                    ipAddress: SecurityContextHelper::getClientIp(),
                    userAgent: SecurityContextHelper::getUserAgent(),
                    deviceInfo: json_encode($deviceInfo),
                    details: 'Failed login attempt from' . SecurityContextHelper::getClientIp() . ' using ' . substr(SecurityContextHelper::getUserAgent() ?? '', 0, 50) .  '(Invalid Credentials)'
                );
            }
            $this->securityAuditRepository->save($log);
            throw new RuntimeException('Invalid credentials.');
        }

        // Credentials valid, now check if account is active
        if ($user->getIsActive() === 0) {
            // Log blocked login (account deactivated)
            $log = SecurityAuditLog::logEvent(
                eventType: 'LOGIN_BLOCKED',
                userId: $user->getId(),
                userUuid: $user->getUuid(),
                ipAddress: SecurityContextHelper::getClientIp(),
                userAgent: SecurityContextHelper::getUserAgent(),
                deviceInfo: json_encode($deviceInfo),
                details: 'Blocked login from' . SecurityContextHelper::getClientIp() . ' using ' . substr(SecurityContextHelper::getUserAgent() ?? '', 0, 50) .  '(Account Deactivated)'
            );
            $this->securityAuditRepository->save($log);
            throw new RuntimeException('Your account has been deactivated. Please contact an admin.');
        }

        // All checks passed - record successful login
        $user->recordLogin();
        $this->userRepository->update($user);

        // Log successful login
        $log = SecurityAuditLog::logEvent(
            eventType: 'LOGIN_SUCCESS',
            userId: $user->getId(),
            userUuid: $user->getUuid(),
            ipAddress: SecurityContextHelper::getClientIp(),
            userAgent: SecurityContextHelper::getUserAgent(),
            deviceInfo: json_encode($deviceInfo),
            details: 'Successful login from' . SecurityContextHelper::getClientIp() . ' using ' . substr(SecurityContextHelper::getUserAgent() ?? '', 0, 50)
        );
        $this->securityAuditRepository->save($log);

        return $user;
    }
}