<?php
declare(strict_types=1);

namespace App\Application\Consultation\DTOs;

class ArchiveConsultationDTO
{
    public function __construct(
        public readonly string $doctorUuid,
        public readonly string $consultationUuid
    ) {}
}