<?php
declare(strict_types=1);

namespace App\Application\Patient\Services;

use App\Domain\Doctor\DoctorAuditLog;
use App\Domain\Doctor\DoctorAuditRepositoryInterface;
use App\Application\Patient\DTOs\UpdatePatientDTO;
use App\Domain\Patient\PatientRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use RuntimeException;

class UpdatePatientService
{
    public function __construct(
        private DoctorAuditRepositoryInterface $auditRepository,
        private PatientRepositoryInterface $patientRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(UpdatePatientDTO $dto): void
    {
        $doctor = $this->userRepository->findByUuid($dto->doctorUuid);
        $patient = $this->patientRepository->findByUuid($dto->patientUuid);

        if (!$doctor || !$patient) {
            throw new RuntimeException("Record not found.");
        }

        // authorization rule
        if ($patient->getDoctorId() !== $doctor->getId()) {
            throw new RuntimeException("Unauthorized access to patient record.");
        }

        $patient->updateProfile(
            $dto->firstName, $dto->lastName, $dto->middleName, $dto->dateOfBirth,
            $dto->sex, $dto->phone, $dto->email, $dto->addressLine,
            $dto->emergencyContactName, $dto->emergencyContactPhone,
            $dto->bloodGroup, $dto->allergies, $dto->chronicConditions
        );

        $this->patientRepository->update($patient);

        $auditLog = DoctorAuditLog::logAction(
            action: 'UPDATE_PATIENT',
            targetType: 'patients',
            targetUuid: $patient->getUuid(),
            doctorId: $doctor->getId(),
            meta: ['email' => $patient->getEmail()]
        );
        $this->auditRepository->save($auditLog);
    }
}