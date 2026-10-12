<?php
declare(strict_types=1);
namespace App\Application\Admin\Services;

use App\Application\Admin\DTOs\SearchUserDTO;
use App\Domain\User\UserRepositoryInterface;

class SearchInactiveUserService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(SearchUserDTO $dto): array
    {
        // Here, the DTO will be instantiated with isActive = 0 by the Controller
        return $this->userRepository->search($dto);
    }
}