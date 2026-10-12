<?php
declare(strict_types=1);

namespace App\Application\Auth\Services;

use App\Domain\Security\SecurityAuditLog;
use App\Domain\Security\SecurityAuditRepositoryInterface;
use App\Infrastructure\Security\SecurityContextHelper;
use App\Application\Auth\DTOs\ChangePasswordDTO;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Security\BcryptPasswordHasher;
use RuntimeException;

class ChangePasswordService
{
    private UserRepositoryInterface $userRepository;
    private BcryptPasswordHasher $hasher;
    private SecurityContextHelper $securityContextHelper;
    private SecurityAuditRepositoryInterface $securityAuditRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        BcryptPasswordHasher $hasher,
        SecurityAuditRepositoryInterface $securityAuditRepository
    ) {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
        $this->securityAuditRepository = $securityAuditRepository;
    }
    public function execute(ChangePasswordDTO $dto): void
    {
        $user = $this->userRepository->findByUuid($dto->userUuid);

        if (!$user) {
            throw new RuntimeException("User not found.");
        }

        $deviceInfo = [
            'user_agent' => SecurityContextHelper::getUserAgent(),
            'ip_address' => SecurityContextHelper::getClientIp(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Verify current password is correct
        if (!$this->hasher->verify($dto->currentPassword, $user->getPasswordHash())) {
            // Log failed password change attempt (incorrect current password)
            $log = SecurityAuditLog::logEvent(
                eventType: 'PASSWORD_CHANGE_FAILED',
                userId: $user->getId(),
                userUuid: $user->getUuid(),
                ipAddress: SecurityContextHelper::getClientIp(),
                userAgent: SecurityContextHelper::getUserAgent(),
                deviceInfo: json_encode($deviceInfo),
                details: 'Failed password change - incorrect current password'
            );
            $this->securityAuditRepository->save($log);
            throw new RuntimeException("The current password provided is incorrect.");
        }

        // Current password verified - change to new password
        $user->changePassword($this->hasher->hash($dto->newPassword));
        $this->userRepository->update($user);
        
        // Log successful password change
        $log = SecurityAuditLog::logEvent(
            eventType: 'PASSWORD_CHANGE',
            userId: $user->getId(),
            userUuid: $user->getUuid(),
            ipAddress: SecurityContextHelper::getClientIp(),
            userAgent: SecurityContextHelper::getUserAgent(),
            deviceInfo: json_encode($deviceInfo),
            details: 'Password changed successfully'
        );
        $this->securityAuditRepository->save($log); 
    }
}