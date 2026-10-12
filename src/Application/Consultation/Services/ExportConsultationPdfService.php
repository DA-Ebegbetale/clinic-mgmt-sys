<?php
declare(strict_types=1);

namespace App\Application\Consultation\Services;

use App\Domain\Doctor\DoctorAuditLog;
use App\Domain\Doctor\DoctorAuditRepositoryInterface;
use App\Application\Consultation\DTOs\GetConsultationDTO;
use App\Infrastructure\Export\DomPdfConsultationExporter;

class ExportConsultationPdfService
{
    public function __construct(
        private DoctorAuditRepositoryInterface $auditRepository,
        private GetConsultationReportService $getConsultationReportService,
        private DomPdfConsultationExporter $exporter
    ) {}

    public function execute(GetConsultationDTO $dto): void
    {
        $report = $this->getConsultationReportService->execute($dto);
        $consultation = $report['consultation'];
        $doctor = $report['doctor'];

        $this->exporter->export(
            $consultation,
            $report['patient'],
            $doctor
        );
        
        $auditLog = DoctorAuditLog::logAction(
            action: 'CONSULTATION_PDF_EXPORT',
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
