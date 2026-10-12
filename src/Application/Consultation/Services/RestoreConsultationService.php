<?php
declare(strict_types=1);

namespace App\Application\Consultation\Services;

use App\Domain\Doctor\DoctorAuditLog;
use App\Domain\Doctor\DoctorAuditRepositoryInterface;
use App\Application\Consultation\DTOs\RestoreConsultationDTO;
use App\Domain\Consultation\ConsultationRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use RuntimeException;

class RestoreConsultationService
{
    public function __construct(
        private DoctorAuditRepositoryInterface $auditRepository,
        private ConsultationRepositoryInterface $consultationRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(RestoreConsultationDTO $dto): void
    {
        $actor = $this->userRepository->findByUuid($dto->doctorUuid);
        $consultation = $this->consultationRepository->findByUuid($dto->consultationUuid);

        if (!$actor || !$consultation) {
            throw new RuntimeException("Record not found.");
        }

        if ($actor->getRole() !== 'admin' && $consultation->getDoctorId() !== $actor->getId()) {
            throw new RuntimeException("Unauthorized administrative action.");
        }

        $consultation->restore();
        $this->consultationRepository->update($consultation);

        $auditLog = DoctorAuditLog::logAction(
            action: 'RESTORE_CONSULTATION',
            targetType: 'consultations',
            targetUuid: $consultation->getUuid(),
            doctorId: $actor->getId(),
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