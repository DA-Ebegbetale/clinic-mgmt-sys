<?php
declare(strict_types=1);
namespace App\Application\Consultation\Services;

use App\Application\Consultation\DTOs\GetConsultationDTO;
use App\Domain\Consultation\Consultation;
use App\Domain\Consultation\ConsultationRepositoryInterface;
use RuntimeException;

class GetConsultationService {
    public function __construct(private ConsultationRepositoryInterface $consultationRepository) {}

    public function execute(GetConsultationDTO $dto): Consultation {
        $consultation = $this->consultationRepository->findByUuid($dto->uuid);
        if (!$consultation) {
            throw new RuntimeException("Consultation record not found.");
        }
        return $consultation;
    }
}