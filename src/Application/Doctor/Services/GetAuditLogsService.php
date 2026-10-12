<?php
declare(strict_types=1);

namespace App\Application\Doctor\Services;

use App\Application\Doctor\DTOs\GetAuditLogsDTO;
use App\Domain\Doctor\DoctorAuditRepositoryInterface;

class GetAuditLogsService
{
    private DoctorAuditRepositoryInterface $auditRepository;

    public function __construct(DoctorAuditRepositoryInterface $auditRepository)
    {
        $this->auditRepository = $auditRepository;
    }

    /**
     * @return array{logs: array, total: int}
     */
    public function execute(GetAuditLogsDTO $dto, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        return $this->auditRepository->getPaginatedLogs($dto->limit, $dto->getOffset(), $dateFrom, $dateTo);
    }
}