<?php
declare(strict_types=1);

namespace App\Application\Admin\Services;

use App\Application\Admin\DTOs\RegisterUserDTO;
use App\Domain\Admin\AdminAuditLog;
use App\Domain\Admin\AdminAuditRepositoryInterface;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Security\BcryptPasswordHasher;
use RuntimeException;

class RegisterUserService
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

    public function execute(RegisterUserDTO $dto): void
    {
        // Admin Verification (internal ID needed for the audit log)
        $admin = $this->userRepository->findByUuid($dto->adminUuid);
        if (!$admin || $admin->getRole() !== 'admin') {
            throw new RuntimeException("Unauthorized: Only administrators can provision users.");
        }

        // Validate Uniqueness
        if ($this->userRepository->findByEmail($dto->email)) {
            throw new RuntimeException("A user with this email already exists.");
        }

        // Create User Entity
        $newUuid = \App\Domain\Shared\ValueObjects\Uuid::generate(); // Assuming you create this simple generator
        $hashedPassword = $this->hasher->hash($dto->tempPassword);

        $newUser = User::registerNewUser(
            $newUuid,
            $dto->firstName,
            $dto->lastName,
            $dto->middleName,
            $dto->email,
            $dto->phone,
            $hashedPassword,
            $dto->role
        );

        // Save User
        $this->userRepository->save($newUser);

        // Create & Save Audit Log
        $log = AdminAuditLog::logAction(
            action: 'REGISTER_USER',
            targetType: 'users',
            targetId: null, // ID is auto-incremented, we log the UUID
            targetUuid: $newUser->getUuid(),
            adminId: $admin->getId(),
            reason: 'Initial Provisioning',
            meta: ['email' => $newUser->getEmail()]
        );

        $this->auditRepository->save($log);
    }
}