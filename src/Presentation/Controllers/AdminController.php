<?php
declare(strict_types=1);

namespace App\Presentation\Controllers;

// DTOs
use App\Application\Admin\DTOs\RegisterUserDTO;
use App\Application\Admin\DTOs\DeactivateUserDTO;
use App\Application\Admin\DTOs\RestoreUserDTO;
use App\Application\Admin\DTOs\ResetUserPasswordDTO;
use App\Application\Admin\DTOs\SearchUserDTO;
use App\Application\Admin\DTOs\GetAuditLogsDTO;

// Services
use App\Application\Admin\Services\RegisterUserService;
use App\Application\Admin\Services\DeactivateUserService;
use App\Application\Admin\Services\RestoreUserService;
use App\Application\Admin\Services\ResetUserPasswordService;
use App\Application\Admin\Services\SearchUserService;
use App\Application\Admin\Services\GetAuditLogsService;
use App\Application\Patient\Services\SearchPatientService;
use App\Application\Admin\Services\GetUserService;
use App\Application\Admin\Services\SearchInactiveUserService;

// Core
use App\Presentation\Core\Request;
use App\Presentation\Core\Response;
use App\Presentation\Core\Session;
use App\Application\Shared\Validators\Validator;

class AdminController
{
    private RegisterUserService $registerUserService;
    private DeactivateUserService $deactivateUserService;
    private RestoreUserService $restoreUserService;
    private ResetUserPasswordService $resetUserPasswordService;
    private SearchUserService $searchUserService;
    private GetAuditLogsService $getAuditLogsService;
    private SearchPatientService $searchPatientService;
    private GetUserService $getUserService;
    private SearchInactiveUserService $searchInactiveUserService;
    //WIRED CONSTRUCTOR
    public function __construct(
        RegisterUserService $registerUserService,
        DeactivateUserService $deactivateUserService,
        RestoreUserService $restoreUserService,
        ResetUserPasswordService $resetUserPasswordService,
        SearchUserService $searchUserService,
        GetAuditLogsService $getAuditLogsService,
        SearchPatientService $searchPatientService,
        GetUserService $getUserService,
        SearchInactiveUserService $searchInactiveUserService
    ) {
        $this->registerUserService = $registerUserService;
        $this->deactivateUserService = $deactivateUserService;
        $this->restoreUserService = $restoreUserService;
        $this->resetUserPasswordService = $resetUserPasswordService;
        $this->searchUserService = $searchUserService;
        $this->getAuditLogsService = $getAuditLogsService;
        $this->searchPatientService = $searchPatientService;
        $this->getUserService = $getUserService;
        $this->searchInactiveUserService = $searchInactiveUserService;
    }

    public function searchUsers(Request $request): void
    {
        $field = $request->getQueryParam('field', 'all');
        $value = $request->getQueryParam('value', '');
        $page = (int) $request->getQueryParam('page', 1);

        $dto = new SearchUserDTO($field, $value, $page, 10, 1);
        $result = $this->searchUserService->execute($dto);

        Response::view('admin/users/index', [
            'data' => $result,
            'users' => $result['users'] ?? [],
            'total' => $result['total'] ?? 0,
            'currentPage' => $page

        ]);
    }

    public function viewAuditLogs(Request $request): void
    {
        $page = (int) $request->getQueryParam('page', 1);
        $dateFrom = $request->getQueryParam('date_from', date('Y-01-01'));
        $dateTo = $request->getQueryParam('date_to', date('Y-m-d'));

        $dto = new GetAuditLogsDTO($page);
        $result = $this->getAuditLogsService->execute($dto, $dateFrom, $dateTo);

        Response::view('admin/logs/index', [
            'logs' => $result['logs'],
            'total' => $result['total'],
            'currentPage' => $page,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }

    public function showCreateForm(Request $request): void
    {
        Response::view('admin/users/create');
    }

    public function registerUser(Request $request): void
    {
        try {
            $data = $request->getBody();
            $adminUuid = $_SESSION['user_uuid'];

            $firstName = Validator::validateRequiredString($data['first_name'] ?? '', 'First Name');
            $lastName = Validator::validateRequiredString($data['last_name'] ?? '', 'Last Name');
            $email = Validator::validateEmail($data['email'] ?? '');
            $passwordMode = $data['password_mode'] ?? 'generate';

            if ($passwordMode === 'generate') {
                $password = bin2hex(random_bytes(4));
            } elseif ($passwordMode === 'manual') {
                $password = Validator::validateRequiredString(
                    $data['temp_password'] ?? '',
                    'Temporary Password'
                );
            } else {
                throw new \RuntimeException('Invalid password mode.');
            }
            $phone = !empty($data['phone']) ? Validator::validateRequiredString($data['phone'], 'Phone') : null;
            $middleName = !empty($data['middle_name']) ? Validator::validateRequiredString($data['middle_name'], 'Middle Name') : null;
            $role = $data['role'] ?? 'doctor';
            if (!in_array($role, ['doctor', 'admin'], true)) {
                throw new \RuntimeException('Invalid role selected.');
            }
            $dto = new RegisterUserDTO($adminUuid, $firstName, $lastName, $middleName, $email, $phone, $password, $role);
            $this->registerUserService->execute($dto);

            Session::setFlash(
                'success',
                'User provisioned successfully. Temporary password: ' . $password
            );
            Response::redirect('/admin/users');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/admin/users/create');
        }
    }

    public function deactivateUser(Request $request): void
    {
        try {
            $data = $request->getBody();
            $targetUuid = Validator::validateRequiredString($data['target_uuid'] ?? '', 'Target User UUID');
            $reason = Validator::validateRequiredString($data['reason'] ?? '', 'Reason');

            $dto = new DeactivateUserDTO($_SESSION['user_uuid'], $targetUuid, $reason);
            $this->deactivateUserService->execute($dto);

            Session::setFlash('success', 'User account deactivated.');
            Response::redirect('/admin/users');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/admin/users');
        }
    }

    public function restoreUser(Request $request): void
    {
        try {
            $data = $request->getBody();
            $targetUuid = Validator::validateRequiredString($data['target_uuid'] ?? '', 'Target User UUID');
            $reason = Validator::validateRequiredString($data['reason'] ?? '', 'Reason');

            $dto = new RestoreUserDTO($_SESSION['user_uuid'], $targetUuid, $reason);
            $this->restoreUserService->execute($dto);

            Session::setFlash('success', 'User account restored successfully.');
            Response::redirect('/admin/users');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/admin/users');
        }
    }

    public function resetPassword(Request $request): void
    {
        try {
            $data = $request->getBody();
            $targetUuid = Validator::validateRequiredString($data['target_uuid'] ?? '', 'Target User UUID');

            // Use provided new_password if given, else generate one
            if (!empty($data['new_password'])) {
                $newPassword = Validator::validateRequiredString($data['new_password'], 'New Password');
            } else {
                // Auto-generate a secure temporary password (8 hex chars)
                $newPassword = bin2hex(random_bytes(4));
            }

            $dto = new ResetUserPasswordDTO($_SESSION['user_uuid'], $targetUuid, $newPassword);
            $this->resetUserPasswordService->execute($dto);

            // Show the new temp password in a flash message for admin to copy (no email integration)
            Session::setFlash('success', 'User password successfully reset. Temporary password: ' . $newPassword);
            Response::redirect('/admin/users');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/admin/users');
        }
    }

    public function viewInactiveUsers(\App\Presentation\Core\Request $request): void
    {
        $searchField = $request->getQueryParam('field', 'all');
        $searchValue = $request->getQueryParam('value', '');
        $page = (int) $request->getQueryParam('page', '1');
        $limit = 10;

        // Here, the DTO enforces isActive = 0
        $dto = new \App\Application\Admin\DTOs\SearchUserDTO($searchField, $searchValue, $page, $limit, 0);

        $result = $this->searchInactiveUserService->execute($dto);

        \App\Presentation\Core\Response::view('admin/users/inactive', [
            'users' => $result['users'],
            'total' => $result['total'],
            'currentPage' => $page,
            'limit' => $limit
        ]);
    }

    public function viewGlobalArchivedPatients(\App\Presentation\Core\Request $request): void
    {
        // The 'true' parameter at the end tells the repository to fetch ONLY archived records
        $dto = new \App\Application\Patient\DTOs\SearchPatientDTO(
            $_SESSION['user_uuid'],
            '',
            '',
            (int) $request->getQueryParam('page', 1),
            10,
            true
        );

        $result = $this->searchPatientService->execute($dto);

        \App\Presentation\Core\Response::view('admin/patients/index', [
            'data' => $result,
            'patients' => $result['patients'] ?? [],
            'total' => $result['total'] ?? 0,
            'currentPage' => (int) $request->getQueryParam('page', 1),
            'limit' => 10

        ]);
    }

    public function showUser(\App\Presentation\Core\Request $request): void
    {
        try {
            $uuid = \App\Application\Shared\Validators\Validator::validateRequiredString($request->getQueryParam('uuid', ''), 'User Reference');
            $dto = new \App\Application\Admin\DTOs\GetUserDTO($uuid);
            $user = $this->getUserService->execute($dto);

            \App\Presentation\Core\Response::view('admin/users/show', ['user' => $user]);
        } catch (\Exception $e) {
            \App\Presentation\Core\Session::setFlash('error', $e->getMessage());
            \App\Presentation\Core\Response::redirect('/admin/users');
        }
    }
}