<?php
declare(strict_types=1);

namespace App\Application\Consultation\Services;

use App\Domain\Doctor\DoctorAuditLog;
use App\Domain\Doctor\DoctorAuditRepositoryInterface;
use App\Application\Consultation\DTOs\UpdateConsultationDTO;
use App\Domain\Consultation\ConsultationRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use RuntimeException;

class UpdateConsultationService
{
    public function __construct(
        private DoctorAuditRepositoryInterface $auditRepository,
        private ConsultationRepositoryInterface $consultationRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(UpdateConsultationDTO $dto): void
    {
        $doctor = $this->userRepository->findByUuid($dto->doctorUuid);
        $consultation = $this->consultationRepository->findByUuid($dto->consultationUuid);

        if (!$doctor || !$consultation) {
            throw new RuntimeException("Record not found.");
        }

        // authorization rule so that doctors can only edit their own consultation notes
        if ($consultation->getDoctorId() !== $doctor->getId()) {
            throw new RuntimeException("Unauthorized access to medical record.");
        }

        $consultation->updateNotes(
            $dto->chiefComplaint, $dto->historyOfPresentIllness, $dto->vitals,
            $dto->assessment, $dto->diagnosis, $dto->plan, $dto->prescription,
            $dto->followUpDate, $dto->additionalNotes
        );

        $this->consultationRepository->update($consultation);

        $auditLog = DoctorAuditLog::logAction(
            action: 'UPDATE_CONSULTATION',
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