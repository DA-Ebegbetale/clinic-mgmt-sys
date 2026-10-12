<?php
declare(strict_types=1);

namespace App\Application\Consultation\DTOs;

class UpdateConsultationDTO
{
    public function __construct(
        public readonly string $doctorUuid,
        public readonly string $consultationUuid,
        public readonly string $chiefComplaint,
        public readonly ?string $historyOfPresentIllness = null,
        public readonly ?array $vitals = null,
        public readonly ?string $assessment = null,
        public readonly ?string $diagnosis = null,
        public readonly ?string $plan = null,
        public readonly ?string $prescription = null,
        public readonly ?string $followUpDate = null,
        public readonly ?string $additionalNotes = null
    ) {}
}