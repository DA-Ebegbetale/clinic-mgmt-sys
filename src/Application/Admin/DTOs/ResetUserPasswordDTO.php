<?php
declare(strict_types=1);

namespace App\Application\Admin\DTOs;

class ResetUserPasswordDTO
{
    public readonly string $adminUuid;
    public readonly string $targetUserUuid;
    public readonly string $newTempPassword;

    public function __construct(string $adminUuid, string $targetUserUuid, string $newTempPassword)
    {
        $this->adminUuid = $adminUuid;
        $this->targetUserUuid = $targetUserUuid;
        $this->newTempPassword = $newTempPassword;
    }
}