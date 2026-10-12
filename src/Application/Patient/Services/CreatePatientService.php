<?php
declare(strict_types=1);

namespace App\Application\Patient\Services;

use App\Domain\Doctor\DoctorAuditLog;
use App\Domain\Doctor\DoctorAuditRepositoryInterface;
use App\Application\Patient\DTOs\CreatePatientDTO;
use App\Domain\Patient\Patient;
use App\Domain\Patient\PatientRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\Shared\ValueObjects\Uuid;
use RuntimeException;

class CreatePatientService
{
    public function __construct(
        private DoctorAuditRepositoryInterface $auditRepository,
        private PatientRepositoryInterface $patientRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(CreatePatientDTO $dto): void
    {
        $doctor = $this->userRepository->findByUuid($dto->doctorUuid);
        if (!$doctor) {
            throw new RuntimeException("Unauthorized: Doctor profile not found.");
        }

        $patient = Patient::registerNewPatient(
            Uuid::generate(),
            $doctor->getId(),
            $dto->firstName,
            $dto->lastName,
            $dto->middleName,
            $dto->dateOfBirth,
            $dto->sex,
            $dto->phone,
            $dto->email,
            $dto->addressLine,
            $dto->emergencyContactName,
            $dto->emergencyContactPhone,
            $dto->bloodGroup,
            $dto->allergies,
            $dto->chronicConditions
        );

        $this->patientRepository->save($patient);

        $auditLog = DoctorAuditLog::logAction(
            action: 'REGISTER_PATIENT',
            targetType: 'patients',
            targetUuid: $patient->getUuid(),
            doctorId: $doctor->getId(),
            meta: ['email' => $patient->getEmail()]
        );
        $this->auditRepository->save($auditLog);
    }
}