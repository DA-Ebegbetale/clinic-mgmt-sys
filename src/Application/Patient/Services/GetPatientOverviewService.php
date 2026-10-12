<?php
declare(strict_types=1);

namespace App\Application\Patient\Services;

use App\Application\Patient\DTOs\GetPatientDTO;
use App\Application\Consultation\DTOs\SearchConsultationDTO;
use App\Domain\Patient\PatientRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Application\Consultation\Services\SearchConsultationService;
use RuntimeException;

class GetPatientOverviewService
{
    public function __construct(
        private GetPatientService $getPatientService,
        private SearchConsultationService $searchConsultationService,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(GetPatientDTO $dto, string $viewerUuid): array
    {
        $patient = $this->getPatientService->execute($dto);
        $doctor = $this->userRepository->findById($patient->getDoctorId());

        if (!$doctor) {
            throw new RuntimeException('Assigned doctor record not found.');
        }

        $consultationDto = new SearchConsultationDTO(
            $viewerUuid,
            '',
            '',
            $patient->getUuid(),
            1,
            10,
            false
        );

        $consultations = $this->searchConsultationService->execute($consultationDto);

        return [
            'patient' => $patient,
            'doctor' => $doctor,
            'consultations' => $consultations['consultations']
        ];
    }
}