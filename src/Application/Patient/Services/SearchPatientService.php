<?php
declare(strict_types=1);

namespace App\Application\Patient\Services;

use App\Application\Patient\DTOs\SearchPatientDTO;
use App\Domain\Patient\PatientRepositoryInterface;

class SearchPatientService
{
    public function __construct(private PatientRepositoryInterface $patientRepository) {}

    /** @return array{patients: array, total: int} */
    public function execute(SearchPatientDTO $dto): array
    {
        return $this->patientRepository->search($dto);
    }
}