<?php
declare(strict_types=1);

namespace App\Application\Auth\Services;

use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use RuntimeException;

class GetCurrentUserService
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function execute(string $userUuid): User
    {
        $user = $this->userRepository->findByUuid($userUuid);

        if (!$user) {
            throw new RuntimeException('User record not found.');
        }

        return $user;
    }
}