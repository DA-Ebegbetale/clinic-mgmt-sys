<?php
declare(strict_types=1);

namespace App\Application\Auth\DTOs;

class ChangePasswordDTO
{
    public readonly string $userUuid;
    public readonly string $currentPassword;
    public readonly string $newPassword;

    public function __construct(string $userUuid, string $currentPassword, string $newPassword)
    {
        $this->userUuid = $userUuid;
        $this->currentPassword = $currentPassword;
        $this->newPassword = $newPassword;
    }
}