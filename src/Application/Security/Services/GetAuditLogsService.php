<?php
declare(strict_types=1);

namespace App\Application\Security\Services;

use App\Application\Security\DTOs\GetAuditLogsDTO;
use App\Domain\Security\SecurityAuditRepositoryInterface;

class GetAuditLogsService
{
    private SecurityAuditRepositoryInterface $auditRepository;

    public function __construct(SecurityAuditRepositoryInterface $auditRepository)
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