<?php
declare(strict_types=1);

namespace App\Presentation\Controllers;

//DTOs
use App\Application\Doctor\DTOs\GetAuditLogsDTO;

// Services
use App\Application\Doctor\Services\GetAuditLogsService;

// Core
use App\Presentation\Core\Request;
use App\Presentation\Core\Response;

class DoctorAuditController
{
    private GetAuditLogsService $getAuditLogsService;

    public function __construct(
        GetAuditLogsService $getAuditLogsService
    ) {
        $this->getAuditLogsService = $getAuditLogsService;
    }

    public function viewAuditLogs(Request $request): void
    {
        $page = (int) $request->getQueryParam('page', 1);
        $dateFrom = $request->getQueryParam('date_from', date('Y-01-01'));
        $dateTo = $request->getQueryParam('date_to', date('Y-m-d'));

        $dto = new GetAuditLogsDTO($page);
        $result = $this->getAuditLogsService->execute($dto, $dateFrom, $dateTo);

        Response::view('doctor/audit/logs', [
            'logs' => $result['logs'] ?? [],
            'total' => $result['total'] ?? 0,
            'currentPage' => $page,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }
}