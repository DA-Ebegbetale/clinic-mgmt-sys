<?php
declare(strict_types=1);
namespace App\Application\Consultation\Services;

use App\Application\Consultation\DTOs\SearchConsultationDTO;
use App\Domain\Consultation\ConsultationRepositoryInterface;

class SearchArchivedConsultationService
{
    private ConsultationRepositoryInterface $consultationRepository;

    public function __construct(ConsultationRepositoryInterface $consultationRepository)
    {
        $this->consultationRepository = $consultationRepository;
    }

    public function execute(SearchConsultationDTO $dto): array
    {
        return $this->consultationRepository->search($dto);
    }
}