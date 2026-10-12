<?php
declare(strict_types=1);

namespace App\Application\Consultation\DTOs;

class SearchConsultationDTO
{
    public readonly string $doctorUuid;
    public readonly string $searchField;
    public readonly string $searchValue;
    public readonly ?string $patientUuid; // To fetch notes specific for a patient
    public readonly int $page;
    public readonly int $limit;
    public readonly ?bool $isArchived;

    public function __construct(string $doctorUuid, string $searchField, string $searchValue, string $patientUuid = null, int $page = 1, int $limit = 10, bool $isArchived = false) 
    {
        $this->doctorUuid = $doctorUuid;
        $this->searchField = $searchField;
        $this->searchValue = $searchValue;
        $this->patientUuid = $patientUuid;
        $this->page = $page;
        $this->limit = $limit;
        $this->isArchived = $isArchived;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }
}