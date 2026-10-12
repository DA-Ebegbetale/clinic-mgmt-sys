<?php
declare(strict_types=1);

namespace App\Domain\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function update(User $user): void; // for state mutations
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findByUuid(string $uuid): ?User;
    public function search(\App\Application\Admin\DTOs\SearchUserDTO $dto): array;
}