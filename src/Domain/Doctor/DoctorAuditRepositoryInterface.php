<?php
declare(strict_types=1);

namespace App\Domain\Doctor;

interface DoctorAuditRepositoryInterface
{
    public function save(DoctorAuditLog $log): void;
    public function getPaginatedLogs(int $limit, int $offset, ?string $dateFrom = null, ?string $dateTo = null): array;
}