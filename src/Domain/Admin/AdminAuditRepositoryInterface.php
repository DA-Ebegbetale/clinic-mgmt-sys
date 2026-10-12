<?php
declare(strict_types=1);

namespace App\Domain\Admin;

interface AdminAuditRepositoryInterface
{
    public function save(AdminAuditLog $log): void; 
    public function getPaginatedLogs(int $limit, int $offset, ?string $dateFrom = null, ?string $dateTo = null): array;
}