<?php
declare(strict_types=1);

namespace App\Application\Admin\Services;

use App\Application\Admin\DTOs\GetAuditLogsDTO;
use App\Domain\Admin\AdminAuditRepositoryInterface;

class GetAuditLogsService
{
    private AdminAuditRepositoryInterface $auditRepository;

    public function __construct(AdminAuditRepositoryInterface $auditRepository)
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