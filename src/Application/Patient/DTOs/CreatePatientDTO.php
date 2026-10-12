<?php
declare(strict_types=1);

namespace App\Application\Patient\DTOs;

class CreatePatientDTO
{
    public function __construct(
        public readonly string $doctorUuid,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly ?string $middleName = null,
        public readonly ?string $dateOfBirth = null,
        public readonly ?string $sex = null,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?string $addressLine = null,
        public readonly ?string $emergencyContactName = null,
        public readonly ?string $emergencyContactPhone = null,
        public readonly ?string $bloodGroup = null,
        public readonly ?string $allergies = null,
        public readonly ?string $chronicConditions = null
    ) {}
}