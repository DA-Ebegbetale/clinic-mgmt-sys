<?php
declare(strict_types=1);

namespace App\Application\Auth\Services;

use App\Domain\Security\SecurityAuditLog;
use App\Domain\Security\SecurityAuditRepositoryInterface;
use App\Infrastructure\Security\SecurityContextHelper;
use App\Application\Auth\DTOs\UpdateUserProfileDTO;
use App\Domain\User\UserRepositoryInterface;
use RuntimeException;

class UpdateUserProfileService
{
    private UserRepositoryInterface $userRepository;
    private SecurityContextHelper $securityContextHelper;
    private SecurityAuditRepositoryInterface $securityAuditRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        SecurityAuditRepositoryInterface $securityAuditRepository
    ) {
        $this->userRepository = $userRepository;
        $this->securityAuditRepository = $securityAuditRepository;
    }
    public function execute(UpdateUserProfileDTO $dto): void
    {
        $user = $this->userRepository->findByUuid($dto->userUuid);

        if (!$user) {
            throw new RuntimeException("User not found.");
        }

        // Check if new email belongs to someone else
        $existingEmailUser = $this->userRepository->findByEmail($dto->email);
        if ($existingEmailUser && $existingEmailUser->getUuid() !== $user->getUuid()) {
            throw new RuntimeException("This email address is already in use.");
        }

        // Update profile
        $user->updateProfile(
            $dto->firstName,
            $dto->lastName,
            $dto->middleName,
            $dto->email,
            $dto->phone
        );

        $this->userRepository->update($user);

        $deviceInfo = [
            'user_agent' => SecurityContextHelper::getUserAgent(),
            'ip_address' => SecurityContextHelper::getClientIp(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        // Log profile update (only on success)
        $log = SecurityAuditLog::logEvent(
            eventType: 'PROFILE_UPDATE',
            userId: $user->getId(),
            userUuid: $user->getUuid(),
            ipAddress: SecurityContextHelper::getClientIp(),
            userAgent: SecurityContextHelper::getUserAgent(),
            deviceInfo: json_encode($deviceInfo),
            details: 'User profile updated successfully from' . SecurityContextHelper::getClientIp() . ' using ' . substr(SecurityContextHelper::getUserAgent() ?? '', 0, 50)
        );
        $this->securityAuditRepository->save($log);
    }
}