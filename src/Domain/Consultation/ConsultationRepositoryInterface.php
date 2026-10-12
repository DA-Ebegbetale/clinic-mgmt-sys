<?php
declare(strict_types=1);

namespace App\Domain\Consultation;

interface ConsultationRepositoryInterface
{
    public function save(Consultation $consultation): void;
    public function update(Consultation $consultation): void;
    public function findById(int $id): ?Consultation;
    public function findByUuid(string $uuid): ?Consultation;
    public function search(\App\Application\Consultation\DTOs\SearchConsultationDTO $dto): array;
}