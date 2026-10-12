<?php
declare(strict_types=1);

namespace App\Application\Admin\Services;

use App\Application\Admin\DTOs\RestoreUserDTO;
use App\Domain\Admin\AdminAuditLog;
use App\Domain\Admin\AdminAuditRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use RuntimeException;

class RestoreUserService
{
    private UserRepositoryInterface $userRepository;
    private AdminAuditRepositoryInterface $auditRepository;

    public function __construct(UserRepositoryInterface $userRepository, AdminAuditRepositoryInterface $auditRepository)
    {
        $this->userRepository = $userRepository;
        $this->auditRepository = $auditRepository;
    }

    public function execute(RestoreUserDTO $dto): void
    {
        $admin = $this->userRepository->findByUuid($dto->adminUuid);
        if (!$admin || $admin->getRole() !== 'admin') {
            throw new RuntimeException("Unauthorized administrative action.");
        }

        $targetUser = $this->userRepository->findByUuid($dto->targetUserUuid);
        if (!$targetUser) {
            throw new RuntimeException("Target user not found.");
        }

        $targetUser->activate();
        $this->userRepository->update($targetUser); 

        $log = AdminAuditLog::logAction(
            action: 'RESTORE_USER',
            targetType: 'users',
            targetId: $targetUser->getId(),
            targetUuid: $targetUser->getUuid(),
            adminId: $admin->getId(),
            reason: $dto->reason,
            meta: ['email' => $targetUser->getEmail()]
        );
        $this->auditRepository->save($log);
    }
}