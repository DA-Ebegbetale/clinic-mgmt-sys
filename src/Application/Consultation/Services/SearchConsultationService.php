<?php
declare(strict_types=1);

namespace App\Application\Consultation\Services;

use App\Application\Consultation\DTOs\SearchConsultationDTO;
use App\Domain\Consultation\ConsultationRepositoryInterface;

class SearchConsultationService
{
    public function __construct(private ConsultationRepositoryInterface $consultationRepository) {}

    /** @return array{consultations: array, total: int} */
    public function execute(SearchConsultationDTO $dto): array
    {
        return $this->consultationRepository->search($dto);
    }
}