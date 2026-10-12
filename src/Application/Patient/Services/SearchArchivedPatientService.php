<?php
declare(strict_types=1);
namespace App\Application\Patient\Services;

use App\Application\Patient\DTOs\SearchPatientDTO;
use App\Domain\Patient\PatientRepositoryInterface;

class SearchArchivedPatientService
{
    private PatientRepositoryInterface $patientRepository;

    public function __construct(PatientRepositoryInterface $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    public function execute(SearchPatientDTO $dto): array
    {
        return $this->patientRepository->search($dto);
    }
}