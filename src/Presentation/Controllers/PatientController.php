<?php
declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\Patient\DTOs\CreatePatientDTO;
use App\Application\Patient\DTOs\ArchivePatientDTO;
use App\Application\Patient\DTOs\RestorePatientDTO;
use App\Application\Patient\DTOs\SearchPatientDTO;

use App\Application\Patient\Services\CreatePatientService;
use App\Application\Patient\Services\UpdatePatientService;
use App\Application\Patient\Services\ArchivePatientService;
use App\Application\Patient\Services\RestorePatientService;
use App\Application\Patient\Services\SearchPatientService;
use App\Application\Patient\Services\SearchArchivedPatientService;
use App\Application\Patient\Services\GetPatientService;
use App\Application\Patient\Services\GetPatientOverviewService;
use App\Application\Shared\Validators\Validator;
use App\Presentation\Core\Request;
use App\Presentation\Core\Response;
use App\Presentation\Core\Session;

class PatientController
{
    public function __construct(
        private CreatePatientService $createPatientService,
        private UpdatePatientService $updatePatientService,
        private ArchivePatientService $archivePatientService,
        private RestorePatientService $restorePatientService,
        private SearchPatientService $searchPatientService,
        private SearchArchivedPatientService $searchArchivedPatientService,
        private GetPatientService $getPatientService,
        private GetPatientOverviewService $getPatientOverviewService
    ) {
    }

    public function searchPatients(Request $request): void
    {
        $field = $request->getQueryParam('field', 'all');
        $value = $request->getQueryParam('value', '');
        $page = (int) $request->getQueryParam('page', 1);
        $limit = (int) $request->getQueryParam('limit', 10);
        $isArchived = (bool) $request->getQueryParam('archived', false);

        $dto = new SearchPatientDTO($_SESSION['user_uuid'], $field, $value, $page, $limit, $isArchived);
        $result = $this->searchPatientService->execute($dto);

        // Uses the table view. Admin looking at archives routes differently on the frontend
        $viewPath = $isArchived && $_SESSION['user_role'] === 'admin' ? 'admin/patients/index' : 'patient/index';
        // Provide both the original payload and the explicit variables expected by views
        Response::view($viewPath, [
            'data' => $result,
            'patients' => $result['patients'] ?? [],
            'total' => $result['total'] ?? 0,
            'currentPage' => $page,
            'limit' => $limit

        ]);
    }

    public function showCreateForm(Request $request): void
    {
        Response::view('patient/create');
    }

    public function createPatient(Request $request): void
    {
        try {
            $data = $request->getBody();

            // Core Required Validations
            $firstName = Validator::validateRequiredString($data['first_name'] ?? '', 'First Name');
            $lastName = Validator::validateRequiredString($data['last_name'] ?? '', 'Last Name');

            // Domain Validations
            $email = !empty($data['email']) ? Validator::validateEmail($data['email']) : null;
            $dateOfBirth = !empty($data['date_of_birth']) ? Validator::validateAgeLimit($data['date_of_birth'], 0) : null;

            $dto = new CreatePatientDTO(
                $_SESSION['user_uuid'],
                $firstName,
                $lastName,
                $data['middle_name'] ?? null,
                $dateOfBirth,
                $data['sex'] ?? null,
                $data['phone'] ?? null,
                $email,
                $data['address_line'] ?? null,
                $data['emergency_contact_name'] ?? null,
                $data['emergency_contact_phone'] ?? null,
                $data['blood_group'] ?? null,
                $data['allergies'] ?? null,
                $data['chronic_conditions'] ?? null
            );

            $this->createPatientService->execute($dto);
            Session::setFlash('success', 'Patient registered successfully.');
            Response::redirect('/patients');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/patient/create');
        }
    }

    public function editPatient(Request $request): void
    {
        try {
            $uuid = Validator::validateRequiredString($request->getQueryParam('uuid', ''), 'Patient Reference');
            $patient = $this->getPatientService->execute(new \App\Application\Patient\DTOs\GetPatientDTO($uuid));

            Response::view('patient/edit', ['patient' => $patient]);
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/patients');
        }
    }

    public function updatePatient(Request $request): void
    {
        try {
            $data = $request->getBody();

            $patientUuid = Validator::validateRequiredString($data['patient_uuid'] ?? '', 'Patient Reference');
            $firstName = Validator::validateRequiredString($data['first_name'] ?? '', 'First Name');
            $lastName = Validator::validateRequiredString($data['last_name'] ?? '', 'Last Name');
            $email = !empty($data['email']) ? Validator::validateEmail($data['email']) : null;
            $dateOfBirth = !empty($data['date_of_birth']) ? Validator::validateAgeLimit($data['date_of_birth'], 0) : null;

            $dto = new \App\Application\Patient\DTOs\UpdatePatientDTO(
                $_SESSION['user_uuid'],
                $patientUuid,
                $firstName,
                $lastName,
                $data['middle_name'] ?? null,
                $dateOfBirth,
                $data['sex'] ?? null,
                $data['phone'] ?? null,
                $email,
                $data['address_line'] ?? null,
                $data['emergency_contact_name'] ?? null,
                $data['emergency_contact_phone'] ?? null,
                $data['blood_group'] ?? null,
                $data['allergies'] ?? null,
                $data['chronic_conditions'] ?? null
            );

            $this->updatePatientService->execute($dto);
            Session::setFlash('success', 'Patient updated successfully.');
            Response::redirect('/patients');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/patient/edit?uuid=' . urlencode($request->getBody()['patient_uuid'] ?? ''));
        }
    }

    public function archivePatient(Request $request): void
    {
        try {
            $patientUuid = Validator::validateRequiredString($request->getBody()['patient_uuid'] ?? '', 'Patient Reference');
            $dto = new ArchivePatientDTO($_SESSION['user_uuid'], $patientUuid);
            $this->archivePatientService->execute($dto);

            Session::setFlash('success', 'Patient successfully archived.');
            Response::redirect('/patients');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/patients');
        }
    }

    public function restorePatient(Request $request): void
    {
        try {
            $patientUuid = Validator::validateRequiredString($request->getBody()['patient_uuid'] ?? '', 'Patient Reference');
            $dto = new RestorePatientDTO($_SESSION['user_uuid'], $patientUuid);
            $this->restorePatientService->execute($dto);

            Session::setFlash('success', 'Patient successfully restored.');
            // Route redirect based on role
            Response::redirect($_SESSION['user_role'] === 'admin' ? '/admin/patients/index' : '/patients');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/'); // Safe fallback
        }
    }

    public function showPatient(\App\Presentation\Core\Request $request): void
    {
        try {
            $uuid = \App\Application\Shared\Validators\Validator::validateRequiredString($request->getQueryParam('uuid', ''), 'Patient Reference');
            $overview = $this->getPatientOverviewService->execute(
                new \App\Application\Patient\DTOs\GetPatientDTO($uuid),
                $_SESSION['user_uuid']
            );

            \App\Presentation\Core\Response::view('patient/show', $overview);
        } catch (\Exception $e) {
            \App\Presentation\Core\Session::setFlash('error', $e->getMessage());
            \App\Presentation\Core\Response::redirect('/patients');
        }
    }

    public function viewArchived(\App\Presentation\Core\Request $request): void
    {
        $searchField = $request->getQueryParam('field', 'all');
        $searchValue = $request->getQueryParam('value', '');
        $page = (int) $request->getQueryParam('page', '1');
        $limit = 10;

        // Enforce isArchived = true
        $dto = new \App\Application\Patient\DTOs\SearchPatientDTO(
            $_SESSION['user_uuid'],
            $searchField,
            $searchValue,
            $page,
            $limit,
            true
        );
        $result = $this->searchArchivedPatientService->execute($dto);

        \App\Presentation\Core\Response::view('patient/archived', [
            'patients' => $result['patients'],
            'total' => $result['total'],
            'currentPage' => $page,
            'limit' => $limit
        ]);
    }
}