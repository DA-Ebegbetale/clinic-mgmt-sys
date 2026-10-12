<?php
declare(strict_types=1);

namespace App\Application\Consultation\Services;

use App\Application\Consultation\DTOs\GetConsultationDTO;
use App\Domain\Patient\PatientRepositoryInterface;
use App\Domain\User\UserRepositoryInterface;
use RuntimeException;

class GetConsultationReportService
{
    public function __construct(
        private GetConsultationService $getConsultationService,
        private PatientRepositoryInterface $patientRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(GetConsultationDTO $dto): array
    {
        $consultation = $this->getConsultationService->execute($dto);
        $patient = $this->patientRepository->findById($consultation->getPatientId());
        $doctor = $this->userRepository->findById($consultation->getDoctorId());

        if (!$patient || !$doctor) {
            throw new RuntimeException('Related patient or doctor record not found.');
        }

        return [
            'consultation' => $consultation,
            'patient' => $patient,
            'doctor' => $doctor
        ];
    }
}