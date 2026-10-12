<?php
declare(strict_types=1);

namespace App\Application\Consultation\Services;

use App\Domain\Doctor\DoctorAuditLog;
use App\Domain\Doctor\DoctorAuditRepositoryInterface;
use App\Application\Consultation\DTOs\RecordConsultationDTO;
use App\Domain\Consultation\Consultation;
use App\Domain\Consultation\ConsultationRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\Patient\PatientRepositoryInterface;
use App\Domain\Shared\ValueObjects\Uuid;
use RuntimeException;

class RecordConsultationService
{
    public function __construct(
        private DoctorAuditRepositoryInterface $auditRepository,
        private ConsultationRepositoryInterface $consultationRepository,
        private UserRepositoryInterface $userRepository,
        private PatientRepositoryInterface $patientRepository
    ) {}

    public function execute(RecordConsultationDTO $dto): void
    {
        $doctor = $this->userRepository->findByUuid($dto->doctorUuid);
        $patient = $this->patientRepository->findByUuid($dto->patientUuid);

        if (!$doctor || !$patient) {
            throw new RuntimeException("Unauthorized: Doctor or Patient record not found.");
        }

        // Security Scope to ensure the doctor is authorized to write notes for this patient
        if ($patient->getDoctorId() !== $doctor->getId()) {
            throw new RuntimeException("Unauthorized: You do not have permission to record notes for this patient.");
        }

        $consultation = Consultation::recordNewConsultation(
            Uuid::generate(),
            $patient->getId(),
            $doctor->getId(),
            $dto->visitDatetime,
            $dto->chiefComplaint,
            $dto->historyOfPresentIllness,
            $dto->vitals,
            $dto->assessment,
            $dto->diagnosis,
            $dto->plan,
            $dto->prescription,
            $dto->followUpDate,
            $dto->additionalNotes
        );

        $this->consultationRepository->save($consultation);

        $auditLog = DoctorAuditLog::logAction(
            action: 'RECORD_CONSULTATION',
            targetType: 'consultations',
            targetUuid: $consultation->getUuid(),
            doctorId: $doctor->getId(),
            meta: [
                'consultation_uuid' => $consultation->getUuid(),
                'chief_complaint' => $consultation->getChiefComplaint(),
                'diagnosis' => $consultation->getDiagnosis(),
                'plan' => $consultation->getPlan()
            ]
        );
        $this->auditRepository->save($auditLog);
    }
}