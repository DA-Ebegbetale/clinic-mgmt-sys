<?php
declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\Consultation\DTOs\RecordConsultationDTO;
use App\Application\Consultation\DTOs\ArchiveConsultationDTO;
use App\Application\Consultation\DTOs\RestoreConsultationDTO;
use App\Application\Consultation\DTOs\SearchConsultationDTO;

use App\Application\Consultation\Services\RecordConsultationService;
use App\Application\Consultation\Services\UpdateConsultationService;
use App\Application\Consultation\Services\ArchiveConsultationService;
use App\Application\Consultation\Services\RestoreConsultationService;
use App\Application\Consultation\Services\SearchConsultationService;
use App\Application\Consultation\Services\SearchArchivedConsultationService;
use App\Application\Consultation\Services\GetConsultationService;
use App\Application\Consultation\Services\GetConsultationReportService;
use App\Application\Consultation\Services\ExportConsultationPdfService;

use App\Application\Shared\Validators\Validator;
use App\Presentation\Core\Request;
use App\Presentation\Core\Response;
use App\Presentation\Core\Session;

class ConsultationController
{
    public function __construct(
        private RecordConsultationService $recordConsultationService,
        private UpdateConsultationService $updateConsultationService,
        private ArchiveConsultationService $archiveConsultationService,
        private RestoreConsultationService $restoreConsultationService,
        private SearchConsultationService $searchConsultationService,
        private SearchArchivedConsultationService $searchArchivedConsultationService,
        private GetConsultationService $getConsultationService,
        private GetConsultationReportService $getConsultationReportService,
        private ExportConsultationPdfService $exportConsultationPdfService,
    ) {
    }

    public function searchConsultations(Request $request): void
    {
        $field = $request->getQueryParam('field', 'all');
        $value = $request->getQueryParam('value', '');
        $page = (int) $request->getQueryParam('page', 1);
        $patientUuid = $request->getQueryParam('patient_uuid'); // Passed if viewing a specific patient's profile
        $fetchArchived = (bool) $request->getQueryParam('archived', false);

        $dto = new SearchConsultationDTO($_SESSION['user_uuid'], $field, $value, $patientUuid, $page, 10, $fetchArchived);
        $result = $this->searchConsultationService->execute($dto);

        Response::view('consultation/index', [
            'data' => $result,
            'consultations' => $result['consultations'] ?? [],
            'total' => $result['total'] ?? 0,
            'currentPage' => $page,
            'limit' => 10

        ]);
    }

    public function showCreateForm(Request $request): void
    {
        Response::view('consultation/create');
    }

    public function recordConsultation(Request $request): void
    {
        try {
            $data = $request->getBody();

            $patientUuid = Validator::validateRequiredString($data['patient_uuid'] ?? '', 'Patient Reference');
            $chiefComplaint = Validator::validateRequiredString($data['chief_complaint'] ?? '', 'Chief Complaint');

            // Time Validation
            $visitDatetime = Validator::validatePastOrPresentDate($data['visit_datetime'] ?? '', 'Visit Date/Time');
            $followUpDate = !empty($data['follow_up_date']) ? Validator::validateFutureDate($data['follow_up_date'], 'Follow-up Date') : null;

            $vitals = isset($data['vitals']) && is_array($data['vitals']) ? $data['vitals'] : null;

            $dto = new RecordConsultationDTO(
                $_SESSION['user_uuid'],
                $patientUuid,
                $visitDatetime,
                $chiefComplaint,
                $data['history_of_present_illness'] ?? null,
                $vitals,
                $data['assessment'] ?? null,
                $data['diagnosis'] ?? null,
                $data['plan'] ?? null,
                $data['prescription'] ?? null,
                $followUpDate,
                $data['additional_notes'] ?? null
            );

            $this->recordConsultationService->execute($dto);
            Session::setFlash('success', 'Consultation recorded securely.');
            Response::redirect('/consultations?patient_uuid=' . $patientUuid);
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/consultations/create');
        }
    }

    public function editConsultation(Request $request): void
    {
        try {
            $uuid = Validator::validateRequiredString($request->getQueryParam('uuid', ''), 'Consultation Reference');
            $consultation = $this->getConsultationService->execute(new \App\Application\Consultation\DTOs\GetConsultationDTO($uuid));

            Response::view('consultation/edit', ['consultation' => $consultation]);
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/consultations');
        }
    }

    public function updateConsultation(Request $request): void
    {
        try {
            $data = $request->getBody();

            $consultationUuid = Validator::validateRequiredString($data['consultation_uuid'] ?? '', 'Consultation Reference');
            $chiefComplaint = Validator::validateRequiredString($data['chief_complaint'] ?? '', 'Chief Complaint');

            $followUpDate = !empty($data['follow_up_date']) ? Validator::validateFutureDate($data['follow_up_date'], 'Follow-up Date') : null;
            $vitals = isset($data['vitals']) && is_array($data['vitals']) ? $data['vitals'] : null;

            $dto = new \App\Application\Consultation\DTOs\UpdateConsultationDTO(
                $_SESSION['user_uuid'],
                $consultationUuid,
                $chiefComplaint,
                $data['history_of_present_illness'] ?? null,
                $vitals,
                $data['assessment'] ?? null,
                $data['diagnosis'] ?? null,
                $data['plan'] ?? null,
                $data['prescription'] ?? null,
                $followUpDate,
                $data['additional_notes'] ?? null
            );

            $this->updateConsultationService->execute($dto);
            Session::setFlash('success', 'Consultation updated successfully.');
            Response::redirect('/consultation/show?uuid=' . urlencode($consultationUuid));
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/consultation/edit?uuid=' . urlencode($request->getBody()['consultation_uuid'] ?? ''));
        }
    }
    public function archiveConsultation(Request $request): void
    {
        try {
            $consultationUuid = Validator::validateRequiredString($request->getBody()['consultation_uuid'] ?? '', 'Consultation Reference');
            $dto = new ArchiveConsultationDTO($_SESSION['user_uuid'], $consultationUuid);
            $this->archiveConsultationService->execute($dto);

            Session::setFlash('success', 'Record archived.');
            Response::redirect('/consultations');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/consultations');
        }
    }

    public function restoreConsultation(Request $request): void
    {
        try {
            $consultationUuid = Validator::validateRequiredString($request->getBody()['consultation_uuid'] ?? '', 'Consultation Reference');
            $dto = new RestoreConsultationDTO($_SESSION['user_uuid'], $consultationUuid);
            $this->restoreConsultationService->execute($dto);

            Session::setFlash('success', 'Record restored.');
            Response::redirect('/consultations');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/consultations');
        }
    }

    public function showConsultation(\App\Presentation\Core\Request $request): void
    {
        try {
            $uuid = \App\Application\Shared\Validators\Validator::validateRequiredString($request->getQueryParam('uuid', ''), 'Consultation Reference');
            $report = $this->getConsultationReportService->execute(
                new \App\Application\Consultation\DTOs\GetConsultationDTO($uuid)
            );

            \App\Presentation\Core\Response::view('consultation/show', $report);
        } catch (\Exception $e) {
            \App\Presentation\Core\Session::setFlash('error', $e->getMessage());
            \App\Presentation\Core\Response::redirect('/consultations');
        }
    }

    public function exportPdf(\App\Presentation\Core\Request $request): void
    {
        try {
            $uuid = \App\Application\Shared\Validators\Validator::validateRequiredString($request->getQueryParam('uuid', ''), 'Consultation Reference');
            $this->exportConsultationPdfService->execute(
                new \App\Application\Consultation\DTOs\GetConsultationDTO($uuid)
            );
        } catch (\Exception $e) {
            \App\Presentation\Core\Session::setFlash('error', $e->getMessage());
            \App\Presentation\Core\Response::redirect('/consultations');
        }
    }

    public function viewArchived(\App\Presentation\Core\Request $request): void
    {
        $searchField = $request->getQueryParam('field', 'all');
        $searchValue = $request->getQueryParam('value', '');
        $page = (int) $request->getQueryParam('page', '1');
        $limit = 10;

        // Enforce isArchived = true
        $dto = new \App\Application\Consultation\DTOs\SearchConsultationDTO(
            $_SESSION['user_uuid'],
            $searchField,
            $searchValue,
            null,
            $page,
            $limit,
            true
        );
        $result = $this->searchArchivedConsultationService->execute($dto);

        \App\Presentation\Core\Response::view('consultation/archived', [
            'consultations' => $result['consultations'],
            'total' => $result['total'],
            'currentPage' => $page,
            'limit' => $limit
        ]);
    }
}