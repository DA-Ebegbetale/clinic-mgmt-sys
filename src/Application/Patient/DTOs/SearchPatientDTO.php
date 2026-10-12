<?php
declare(strict_types=1);
namespace App\Application\Patient\DTOs;

class SearchPatientDTO
{
    public readonly string $doctorUuid;
    public readonly string $searchField;
    public readonly string $searchValue;
    public readonly int $page;
    public readonly int $limit;
    public readonly ?bool $isArchived;

    public function __construct(string $doctorUuid, string $searchField, string $searchValue, int $page = 1, int $limit = 10, ?bool $isArchived = null)
    {
        $this->doctorUuid = $doctorUuid;
        $this->searchField = $searchField;
        $this->searchValue = $searchValue;
        $this->page = $page;
        $this->limit = $limit;
        $this->isArchived = $isArchived;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}