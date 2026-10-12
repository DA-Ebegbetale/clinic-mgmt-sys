<?php
declare(strict_types=1);

namespace App\Application\Consultation\Services;

use App\Domain\Doctor\DoctorAuditLog;
use App\Domain\Doctor\DoctorAuditRepositoryInterface;
use App\Application\Consultation\DTOs\ArchiveConsultationDTO;
use App\Domain\Consultation\ConsultationRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use RuntimeException;

class ArchiveConsultationService
{
    public function __construct(
        private DoctorAuditRepositoryInterface $auditRepository,
        private ConsultationRepositoryInterface $consultationRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(ArchiveConsultationDTO $dto): void
    {
        $doctor = $this->userRepository->findByUuid($dto->doctorUuid);
        $consultation = $this->consultationRepository->findByUuid($dto->consultationUuid);

        if (!$doctor || !$consultation || $consultation->getDoctorId() !== $doctor->getId()) {
            throw new RuntimeException("Unauthorized or record not found.");
        }

        $consultation->archive();
        $this->consultationRepository->update($consultation);

        $auditLog = DoctorAuditLog::logAction(
            action: 'ARCHIVE_CONSULTATION',
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