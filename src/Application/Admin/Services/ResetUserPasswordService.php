<?php
declare(strict_types=1);

namespace App\Application\Admin\Services;

use App\Application\Admin\DTOs\ResetUserPasswordDTO;
use App\Domain\Admin\AdminAuditLog;
use App\Domain\Admin\AdminAuditRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Security\BcryptPasswordHasher;
use RuntimeException;

class ResetUserPasswordService
{
    private UserRepositoryInterface $userRepository;
    private AdminAuditRepositoryInterface $auditRepository;
    private BcryptPasswordHasher $hasher;

    public function __construct(
        UserRepositoryInterface $userRepository, 
        AdminAuditRepositoryInterface $auditRepository,
        BcryptPasswordHasher $hasher
    ) {
        $this->userRepository = $userRepository;
        $this->auditRepository = $auditRepository;
        $this->hasher = $hasher;
    }

    public function execute(ResetUserPasswordDTO $dto): void
    {
        $admin = $this->userRepository->findByUuid($dto->adminUuid);
        if (!$admin || $admin->getRole() !== 'admin') {
            throw new RuntimeException("Unauthorized administrative action.");
        }

        $targetUser = $this->userRepository->findByUuid($dto->targetUserUuid);
        if (!$targetUser) {
            throw new RuntimeException("Target user not found.");
        }

        $targetUser->changePassword($this->hasher->hash($dto->newTempPassword));
        $this->userRepository->update($targetUser);

        $log = AdminAuditLog::logAction(
            action: 'RESET_USER_PASSWORD',
            targetType: 'users',
            targetId: $targetUser->getId(),
            targetUuid: $targetUser->getUuid(),
            adminId: $admin->getId(),
            reason: 'Forced administrative password modification',
            meta: ['email' => $targetUser->getEmail()]
        );
        $this->auditRepository->save($log);
    }
}