<?php
declare(strict_types=1);
namespace App\Application\Admin\Services;

use App\Application\Admin\DTOs\GetUserDTO;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use RuntimeException;

class GetUserService {
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function execute(GetUserDTO $dto): User {
        $user = $this->userRepository->findByUuid($dto->uuid);
        if (!$user) {
            throw new RuntimeException("User record not found.");
        }
        return $user;
    }
}