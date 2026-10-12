<?php
declare(strict_types=1);

namespace App\Domain\Security;

interface SecurityAuditRepositoryInterface
{
    public function save(SecurityAuditLog $log): void;
    public function getPaginatedLogs(int $limit, int $offset, ?string $dateFrom = null, ?string $dateTo = null): array;
}