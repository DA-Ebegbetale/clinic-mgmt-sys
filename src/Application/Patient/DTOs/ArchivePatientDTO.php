<?php
declare(strict_types=1);

namespace App\Application\Patient\DTOs;

class ArchivePatientDTO
{
    public function __construct(
        public readonly string $doctorUuid,
        public readonly string $patientUuid
    ) {}
}