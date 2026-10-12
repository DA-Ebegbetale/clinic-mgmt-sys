<?php
declare(strict_types=1);

namespace App\Application\Admin\Services;

use App\Application\Admin\DTOs\SearchUserDTO;
use App\Domain\User\UserRepositoryInterface;

class SearchUserService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Returns an array containing the paginated users and the total count for UI math.
     * @return array{users: array, total: int}
     */
    public function execute(SearchUserDTO $dto): array
    {
        // The service passes the DTO to the repository
        return $this->userRepository->search($dto);
    }
}