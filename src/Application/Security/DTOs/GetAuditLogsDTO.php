<?php
declare(strict_types=1);

namespace App\Application\Security\DTOs;

class GetAuditLogsDTO
{
    public readonly int $page;
    public readonly int $limit;
    public readonly ?string $dateFrom;
    public readonly ?string $dateTo;

    public function __construct(int $page = 1, int $limit = 20, ?string $dateFrom = null, ?string $dateTo = null)
    {
        $this->page = $page;
        $this->limit = $limit;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}