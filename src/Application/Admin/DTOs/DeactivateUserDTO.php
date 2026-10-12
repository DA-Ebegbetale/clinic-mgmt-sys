<?php
declare(strict_types=1);

namespace App\Application\Admin\DTOs;

class DeactivateUserDTO
{
    public readonly string $adminUuid;
    public readonly string $targetUserUuid;
    public readonly string $reason;

    public function __construct(string $adminUuid, string $targetUserUuid, string $reason)
    {
        $this->adminUuid = $adminUuid;
        $this->targetUserUuid = $targetUserUuid;
        $this->reason = $reason;
    }
}