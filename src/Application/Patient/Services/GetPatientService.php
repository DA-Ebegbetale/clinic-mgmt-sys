<?php
declare(strict_types=1);
namespace App\Application\Patient\Services;

use App\Application\Patient\DTOs\GetPatientDTO;
use App\Domain\Patient\Patient;
use App\Domain\Patient\PatientRepositoryInterface;
use RuntimeException;

class GetPatientService {
    public function __construct(private PatientRepositoryInterface $patientRepository) {}

    public function execute(GetPatientDTO $dto): Patient {
        $patient = $this->patientRepository->findByUuid($dto->uuid);
        if (!$patient) {
            throw new RuntimeException("Patient record not found.");
        }
        return $patient;
    }
}