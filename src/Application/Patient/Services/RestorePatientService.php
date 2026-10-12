<?php
declare(strict_types=1);

namespace App\Application\Patient\Services;

use App\Domain\Doctor\DoctorAuditLog;
use App\Domain\Doctor\DoctorAuditRepositoryInterface;
use App\Application\Patient\DTOs\RestorePatientDTO;
use App\Domain\Patient\PatientRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use RuntimeException;

class RestorePatientService
{
    public function __construct(
        private DoctorAuditRepositoryInterface $auditRepository,
        private PatientRepositoryInterface $patientRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(RestorePatientDTO $dto): void
    {
        // Admins can restore from the global list, Doctors from their own.
        // We first verify the actor exists to retrieve their ID logic.
        $actor = $this->userRepository->findByUuid($dto->doctorUuid);
        $patient = $this->patientRepository->findByUuid($dto->patientUuid);

        if (!$actor || !$patient) {
            throw new RuntimeException("Record not found.");
        }

        // Only the assigned doctor OR an admin can restore
        if ($actor->getRole() !== 'admin' && $patient->getDoctorId() !== $actor->getId()) {
            throw new RuntimeException("Unauthorized administrative action.");
        }

        $patient->restore();
        $this->patientRepository->update($patient);

        $auditLog = DoctorAuditLog::logAction(
            action: 'RESTORE_PATIENT',
            targetType: 'patients',
            targetUuid: $patient->getUuid(),
            doctorId: $actor->getId(),
            meta: ['email' => $patient->getEmail()]
        );
        $this->auditRepository->save($auditLog);
    }
}