<?php
declare(strict_types=1);

namespace App\Domain\Patient;

interface PatientRepositoryInterface
{
    public function save(Patient $patient): void;
    public function update(Patient $patient): void;
    public function findById(int $id): ?Patient;
    public function findByUuid(string $uuid): ?Patient;
    public function search(\App\Application\Patient\DTOs\SearchPatientDTO $dto): array;
}