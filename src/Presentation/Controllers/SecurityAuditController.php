<?php
declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\Security\DTOs\GetAuditLogsDTO;
use App\Application\Security\Services\GetAuditLogsService;
use App\Presentation\Core\Request;
use App\Presentation\Core\Response;

class SecurityAuditController
{
    public function __construct(
        private GetAuditLogsService $getAuditLogsService
    ) {
    }

    public function viewAuditLogs(Request $request): void
    {
        $page = (int) $request->getQueryParam('page', 1);
        $limit = 10;
        $dateFrom = $request->getQueryParam('date_from', date('Y-01-01'));
        $dateTo = $request->getQueryParam('date_to', date('Y-m-d'));

        $dto = new GetAuditLogsDTO($page);
        $result = $this->getAuditLogsService->execute($dto, $dateFrom, $dateTo);

        Response::view('security/audit/logs', [
            'logs' => $result['logs'] ?? [],
            'total' => $result['total'] ?? 0,
            'currentPage' => $page,
            'limit' => $limit,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }
}