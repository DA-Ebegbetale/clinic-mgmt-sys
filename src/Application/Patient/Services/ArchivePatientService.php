<?php
declare(strict_types=1);

namespace App\Application\Patient\Services;

use App\Domain\Doctor\DoctorAuditLog;
use App\Domain\Doctor\DoctorAuditRepositoryInterface;
use App\Application\Patient\DTOs\ArchivePatientDTO;
use App\Domain\Patient\PatientRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use RuntimeException;

class ArchivePatientService
{
    public function __construct(
        private DoctorAuditRepositoryInterface $auditRepository,
        private PatientRepositoryInterface $patientRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(ArchivePatientDTO $dto): void
    {
        $auditLog = new DoctorAuditLog();
        $doctor = $this->userRepository->findByUuid($dto->doctorUuid);
        $patient = $this->patientRepository->findByUuid($dto->patientUuid);

        if (!$doctor || !$patient || $patient->getDoctorId() !== $doctor->getId()) {
            throw new RuntimeException("Unauthorized or patient not found.");
        }

        $patient->archive();
        $this->patientRepository->update($patient);

        $auditLog = DoctorAuditLog::logAction(
            action: 'ARCHIVE_PATIENT',
            targetType: 'patients',
            targetUuid: $patient->getUuid(),
            doctorId: $doctor->getId(),
            meta: ['email' => $patient->getEmail()]
        );
        $this->auditRepository->save($auditLog);
    }
}